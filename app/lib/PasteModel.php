<?php
g()->load('DataSets', null);

/**
 * THE paste.
 *
 * Common attributes for all the types.
 * @author m.augustynowicz
 */
class PasteModel extends Model
{
    /**
     * Used as a base for url field suffixes.
     */
    const URL_BASE = '-_.ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    public function __construct()
    {
        parent::__construct();

        $this->_addField(new FId('id'));
        $this->_addField(new FString('url', true));
        $this->_addField(new FString('title', true))
            ->mess(array(
                'notnull' => $this->trans('You have to call your paste somehow.'),
            ));
        $this->_addField(new FMultilineString('content'));
        $this->_addField(new FString('version', true, '1'));

        $this->_addField(new FForeignId('parent_id', false, 'Paste'));
        $this->_addField(new FForeignId('root_id', false, 'Paste'));

        $this->_addField(new FString('paster'))
                ->auto(array($this, 'autoValPaster'));
        $this->_addField(new FForeignId('paster_id', false, 'User'))
                ->auto(array($this, 'autoValPasterId'));
        $this->relate('Paster', 'User', 'Nto1');

        $this->_addField(new FInt('status', 2, true, STATUS_ACTIVE));

        $this->_addField(new FString('author'));
        $this->_addField(new FURL('source_url'))
            ->mess(array(
                'unsupported protocol' => $this->trans('Please specify one of these protocols: http, https, ftp, gopher.'),
                'syntax error'         => $this->trans('This does not look like a URL.'),
                'no dns record'        => $this->trans('This host name does not exist.'),
            ));

        $this->_addField(new FString('type', true));


        // text search vector fields

        $this->_addField(new FTextSearchVectorPSQL('title_tsv'));
        $this->_addField(new FTextSearchVectorPSQL('paster_tsv'));
        $this->_addField(new FTextSearchVectorPSQL('tags_tsv'));
        $this->_addField(new FTextSearchVectorPSQL('content_tsv'));
        $this->_addField(new FTextSearchVectorPSQL('groups_tsv'));


        // privacy
        $this->_addField(new FEnum('privacy', 'paste_privacy', true, 'public'));
        $this->_addField(new FMD5String('enc_passwd', false));
        $this->_addField(new FBool('publicly_versionable', false, true));

        $this->_addField(new FTimestamp('creation'))
                ->auto(array($this, 'autoValCreation'));

        $this->_addField(new FTimestamp('last_edit'));

        $this->_pk('id');
        $this->whiteListAll();
    }


    public function autoValPasterId($action, $field, $value)
    {
        if ('insert' != $action)
        {
            return null;
        }
        else
        {
            return ($val = g()->auth->id()) ? $val : null;
        }
    }


    public function autoValPaster($action, $field, $value)
    {
        if ('insert' != $action)
        {
            return null;
        }
        else
        {
            return ($val = g()->auth->displayName()) ? $val : null;
        }
    }


    public function autoValCreation($action, $field, $value)
    {
        if ('insert' != $action)
        {
            return null;
        }
        else
        {
            return time();
        }
    }


    /**
     * @todo optimize
     */
    public function getNewTreeData(array $base=null)
    {
        $data = array(
            'version'   => 1,
            'parent_id' => @$base['id'],
            'root_id'   => @$base['root_id']
        );

        if (!$base)
        {
            return array('parent_id' => null) + $data;
        }

        $model = g('Paste', 'model');

        $model->order('id', 'ASC');
        $children = $model
            ->whiteList(array(
                'version'
            ))
            ->filter(array(
                'parent_id' => $base['id']
            ))
            ->exec();
        $children_count = sizeof($children);

        if ($children_count === 0)
        {
            $data['version'] =
                    $this->_incrementVersion($base['version']);
        }
        else
        {
            if ($children_count === 1)
            {
                $base_version = $base['version'];
            }
            else
            {
                $last_child = end($children);
                $base_version = $last_child['version'];
            }
            $separator = g()->conf['paste']['version_separator'];
            $data['version'] = $base_version . $separator . '1';
        }

        return $data;
    }


    protected function _incrementVersion($version)
    {
        $separator = g()->conf['paste']['version_separator'];
        $version = explode($separator, $version);
        end($version);
        $version[key($version)] = 1 + current($version);
        return implode($separator, $version);
    }


    public function validateType(&$value)
    {
        $errors = array();
        if (!isset(g()->conf['paste_types'][$value]))
        {
            $errors['invalid'] = true;
        }
        return $errors;
    }


    /**
     * Propose unique url.
     * @author m.augustynowicz
     *
     * @param string $url user value, if it's not unique, will be suffixed
     *        with some garbadge string
     *
     * @return string
     */
    public function uniquifyURL($url='', $title=null)
    {
        $base = self::URL_BASE;
        $base_len = strlen($base)-1;
        if ('' === $url)
        {
            $url = $title;
        }

        $url = iconv('utf-8', 'ascii//translit', $title);
        $url = trim(
            preg_replace("/[^".preg_quote($base)."]+/", $base{0}, $url),
            $base{0}
        );

        while ($this->filter(array('url'=>$url))->getCount())
        {
            $url .= $base[rand(0,$base_len)];
        }

        return $url;
    }


    /**
     * Gets paste by URL, with type data and tags.
     * @author m.augustynowicz
     *
     * @param string $url
     *
     * @return array
     */
    static public function getByUrl($url, $ver=null)
    {
        $filters = array(
            'url' => $url,
            'version' => $ver
        );

        $model = new PasteModel();

        $display_field = g()->conf['users']['display_name_field'];
        $ident_field   = g()->conf['users']['display_name_field'];
        $whitelist =
            array_keys($model->getFields()) // Paste.*
            +
            array(
                "Paster+$ident_field" => "User.$ident_field",
                'Paster+DisplayName'  => "User.$display_field",
                'Paster+email'        => 'User.email',
            )
        ;

        $result = $model
            ->rel('Paster')
                ->whiteList($whitelist)
                ->getRow($filters)
        ;

        if (!$result)
        {
            return false;
        }


        $result['Tags'] = array();
        $tags = g('PasteTag','model')
                ->filter(array('paste_id'=>$result['id']))
                ->exec();
        foreach ($tags as &$tag)
        {
            $result['Tags'][] = $tag['tag'];
        }
        $result['tags'] = implode(', ', $result['Tags']);


        return $result;
    }


    /**
     * Get pastes tree
     * @author m.augustynowicz
     *
     * @param null|int $root_id root_id value to filter with
     *
     * @return array paste gets [Children]
     */
    public function getTree($root_id = null)
    {
        if (func_num_args()==0)
        {
            if (count($this->_array) == 1)
            {
                $root_id = @$this->_array[0]['root_id'];
            }
            if (!$root_id)
            {
                $root_id = $this->getData('root_id');
            }
        }

        if (null !== $root_id)
        {
            $this->filter(array('root_id' => $root_id));
        }

        $tree = array();
        $flat = $this->exec('id');

        foreach ($flat as &$paste)
        {
            if (!@$paste['parent_id'])
            {
                $tree[$paste['id']] = &$paste;
            }
            else
            {
                $flat[$paste['parent_id']]['Children'][$paste['id']] = &$paste;
            }
        }

        return $tree;
    }

}

