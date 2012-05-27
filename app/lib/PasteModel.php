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

    /**
     * Used in version field
     */
    const VER_SEPARATOR = '.';
    const VER_ONE = '83dcefb7'; // crc32 of '1'

    /**
     * Delay after which restoring is impossible.
     */
    const SECONDS_TO_RESTORE = 300;

    public function __construct()
    {
        parent::__construct();

        $this->_addField(new FId('id'));
        $this->_addField(new FString('url', true));
        $this->_addField(new FString('title', true))
            ->mess(array(
                'required' => $this->trans('You have to call your paste somehow.'),
            ));
        $this->_addField(new FMultilineString('content'));
        $this->_addField(new FString('version', true, self::VER_ONE));

        $this->_addField(new FForeignId('parent_id', false, 'Paste'));
        $this->_addField(new FForeignId('root_id', false, 'Paste'));

        $this->_addField(new FString('paster'))
                ->auto(array($this, 'autoValPaster'));
        $this->_addField(new FForeignId('paster_id', false, 'User'))
                ->auto(array($this, 'autoValPasterId'));
        $this->relate('Paster', 'User', 'Nto1');

        $this->_addField(new FTimestamp('removed'));

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
        $this->_addField(g('Users', 'field', array('access_users')));
        $this->_addField(new FMD5String('enc_passwd', false));
        $this->_addField(new FBool('publicly_versionable', false, true));

        $this->_addField(new FTimestamp('creation'))
                ->auto(array($this, 'autoValCreation'));

        $this->_addField(new FTimestamp('last_edit'));

        $this->_pk('id');

        $this->whitelist(
            array_keys(array_filter(
                $this->getFields(true),
                function ($field) {
                    return ! $field instanceof FTextSearchVectorPSQL;
                }
            ))
        );
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
            'version'   => self::VER_ONE,
            'parent_id' => @$base['id'],
            'root_id'   => @$base['root_id']
        );

        $model = new self;

        $data['version'] =
                $this->_incrementVersion($base);

        return $data;
    }


    protected function _incrementVersion($parent_paste)
    {
        $count = g('Paste', 'model')
            ->filter(array('root_id' => $parent_paste['root_id']))
            ->getCount()
        ;
        return sprintf("%x", crc32($parent_paste['version'] . $count));
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
    static public function getByUrl($url, $ver = null, $removed = null)
    {
        $filters = array(
            'url'     => $url,
            'version' => $ver,
        );
        if ($removed !== true)
        {
            $filters['removed'] = null;
        }
        else
        {
            $ttl = strtotime(self::SECONDS_TO_RESTORE . ' seconds ago');
            $filters[] = array('removed', '>', $ttl);
        }

        $model = new self;

        $display_field = g()->conf['users']['display_name_field'];
        $ident_field   = g()->conf['users']['display_name_field'];
        $whitelist =
            array_keys($model->whiteListAll()->whiteList()) // Paste.*
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

        // handle password protected
        if (null !== $result['enc_passwd'])
        {
            if ($result['root_id'])
            {
                $root_id = $result['root_id'];
            }
            else
            {
                $root_id = $result['id'];
            }
            if (self::hasPassword($result['id']))
            {
                $result['content'] = self::decrypt(
                    $result['content'],
                    self::_getPassword($result['id'])
                );
            }
            else
            {
                $result['content'] = null;
            }
        }

        // handle private pastes
        if ($result['privacy'] === 'private')
        {
            $allowed = 0 < g('PasteAccessUser', 'model')
                ->filter(array(
                    'user_id'       => g()->auth->id(),
                    'paste_root_id' => $result['root_id']
                ))
                ->getCount()
            ;
            if ($allowed === false)
            {
                return false;
            }
        }


        $result['Tags'] = array();
        $tags = g('PasteTag','model')
                ->filter(array('paste_id' => $result['id']))
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

        $filters = array(
            'removed' => null
        );

        if (null !== $root_id)
        {
            $filters['root_id'] = $root_id;
        }

        $this->filter($filters);
        $flat = $this->exec('id');
        $tree = array();

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


    /**
     * Get pastes by search query
     * @author m.augustynowicz
     *
     * @param string $query
     * @param array $options
     *        - [full] get paste with content
     *        - [paginator] PaginatorController instance
     *        - [limit], [offset] used only when no paginator given
     * @param bool $full get paste with content
     * @param int $count if given, total count will be returned by reference
     *
     * @return array
     */
    static public function getByQuery($query, array $options = array(), & $count = null)
    {
        $model = new self;

        $options += array(
            'full'   => false,
            'offset' => 0
        );

        if (!(@$options['paginator']) instanceof PaginatorController)
        {
            $options['paginator'] = false;
        }

        $get_count = (func_num_args() >= 3);

        $full =& $options['full'];

        // pagination

        $sql_offset = false;
        $sql_limit  = false;
        // pagination from component
        if ($options['paginator'])
        {
            list(
                $margin_from,
                ,
                $margin_limit
            ) = $options['paginator']->getMargins();
            $sql_offset = (int) $margin_from;
            $sql_limit  = (int) $margin_limit;
        }
        // pagination from method params
        else
        {
            if ($options['offset'])
            {
                $sql_offset = (int) $options['offset'];
            }
            if ($options['limit'])
            {
                $sql_limit  = (int) $options['limit'];
            }
        }



        if (g()->auth->loggedIn())
        {
            // mine

            $sql_val_user_id = $model['paster_id']->dbString(g()->auth->id());
            $sql_query_or_visible_to_me = " OR {$model['paster_id']} = {$sql_val_user_id}";

            // access has been given to me

            $accessuser_model = g('PasteAccessUser', 'model');
            $sql_query_or_visible_to_me .= " OR {$sql_val_user_id} IN (SELECT user_id FROM {$accessuser_model} WHERE {$accessuser_model['paste_root_id']} = {$model['root_id']})";
        }
        else
        {
            $sql_query_or_visible_to_me = '';
        }


        $sql_val_public  = $model['privacy']->dbString('public');

        if ($query)
        {
            $replacements = array(
                // map human readable operators
                '/\bOR\b/'  => '|',
                '/\bAND\b/' => '&',
                '/\bNOT\b/' => '!',
                '/\s+-(?=[^\s])/' => ' !',
                '/"/' => "'",
                // change spaces to underscores in quoted strings
                "/'([^']*)'/e" => 'str_replace(" ", "_", "$1")',
                // insert AND operator between words
                '/(?<=[^!|()&])\s+(?=[^|()&])/' => ' & ',
                // map human readable weights
                '/\bpaster:([^!|()&\s]+)\b/'  => '$1:B',
                '/\btag:([^!|()&\s]+)\b/'     => '$1:A',
                '/\bgroup:([^!|()&\s]+)\b/'   => '$1:D',
            );
            $query = preg_replace(
                array_keys($replacements),
                $replacements,
                $query
            );

            $sql_ts_query = FRich::dbString($query);

            $sql_ts_vector = <<< SQL_TSV
                (
                    setweight({$model['title_tsv']},   'C')
                    ||
                    setweight({$model['paster_tsv']},  'B')
                    ||
                    setweight({$model['tags_tsv']},    'A')
                    ||
                    setweight({$model['content_tsv']}, 'C')
                    ||
                    setweight({$model['groups_tsv']},  'D')
                )
SQL_TSV
            ;

            $sql_query_select_rank = <<< SQL_SELECT_RANK
                ts_rank(
                    '{0.1, 0.5, 0.7, 1.0}', -- D, C, B, A weights
                    {$sql_ts_vector},
                    _query
                ) AS _rank
SQL_SELECT_RANK
            ;
            $headline_cfg = "'StartSel=''<strong class=query-keyword>'', StopSel=</strong>'";
            $sql_query_select_hl = <<< SQL_SELECT_HIGHLIGHTS
                ts_headline({$model['title']},   _query, $headline_cfg) AS hl_title,
                ts_headline({$model['paster']},  _query, $headline_cfg) AS hl_paster
SQL_SELECT_HIGHLIGHTS
            ;
            if ($full)
            {
                $sql_query_select_hl .= ", ts_headline({$model['content']}, _query, $headline_cfg) AS hl_content";
            }
            $sql_query_plus_from_query = ", to_tsquery({$sql_ts_query}) AS _query";

            $sql_query_where_matches = "_query @@ {$sql_ts_vector}";

        }
        else // if $query else
        {
            $sql_query_select_rank = "{$model['creation']} AS _rank";
            $sql_query_select_hl = <<< SQL_SELECT_HIGHLIGHTS
                        {$model['title']}   AS hl_title,
                        {$model['paster']}  AS hl_paster
SQL_SELECT_HIGHLIGHTS
            ;
            if ($full)
            {
                $sql_query_select_hl .= ", {$model['content']} AS hl_content";
            }
            $sql_query_plus_from_query = '';
            $sql_query_where_matches = '1=1';
        }

        $sql_subquery = <<< QUERY_SQL
            (
                SELECT
                DISTINCT ON ({$model['root_id']})
                    -- basic data
                    {$model['url']},
                    {$model['version']},
                    {$model['creation']},
                    -- result rank
                    {$sql_query_select_rank},
                    -- hightlighted data
                    {$sql_query_select_hl}
                FROM
                    {$model}
                    {$sql_query_plus_from_query}
                WHERE
                    -- not removed
                    removed IS NULL
                    AND
                    -- not passworded
                    enc_passwd IS NULL
                    AND
                    -- visible
                    (
                        {$model['privacy']} = {$sql_val_public}
                        {$sql_query_or_visible_to_me}
                    )
                    AND
                    -- matches query
                    (
                        {$sql_query_where_matches}
                    )
                ORDER BY
                    {$model['root_id']}, -- for DISTINCT
                    {$model['creation']} DESC  -- promote latest version
            ) pastes
QUERY_SQL
        ;

        if ($options['paginator'] || $get_count)
        {
            $sql_count_query = "SELECT COUNT(1) FROM {$sql_subquery}";
            $count = g()->db->getOne($sql_count_query);
            if ($options['paginator'])
            {
                $options['paginator']->config($count);
            }
        }

        $sql_query = "SELECT * FROM {$sql_subquery} ORDER BY _rank DESC, creation DESC";
        if ($sql_limit !== false)
        {
            $sql_query .= " LIMIT {$sql_limit}";
        }
        if ($sql_offset !== false)
        {
            $sql_query .= " OFFSET {$sql_offset}";
        }
        $rows = g()->db->getAll($sql_query);

        if (!$rows)
        {
            $rows = array();
        }


        // fetch details

        foreach ($rows as &$row)
        {
            $row += self::getByUrl($row['url'], $row['version']);

            $row['title']   = $row['hl_title'];

            $row['paster']  = $row['hl_paster'];
            if (isset($row['Paster']['DisplayName']))
            {
                $row['Paster']['DisplayName'] = $row['hl_paster'];
            }

            if ($full)
            {
                $row['content'] = $row['hl_content'];
                preg_match("/^(.*$\s*){7}/m", $row['content'], $matches);
                $row['content_excerpt'] = rtrim($matches[0]);
            }
        }

        return $rows;
    }


    /**
     * Get remembered password for a paste
     */
    static protected function _getPassword($id)
    {
        return @ $_SESSION['PastePasswords'][$id];
    }

    /**
     * Check whether we have remembered password for a paste
     */
    static public function hasPassword($id)
    {
        return null !== self::_getPassword($id);
    }

    /**
     * Store a password for a paste
     */
    static public function rememberPassword($id, $pass)
    {
        $_SESSION['PastePasswords'][$id] = $pass;
    }


    /**
     * Encrypt paste contents
     */
    static public function encrypt($content, $secret)
    {
        $plain_text = $content;
        $key = $secret;

        $td = mcrypt_module_open('des', '', 'ecb', '');
        $key = substr($key, 0, mcrypt_enc_get_key_size($td));
        $iv_size = mcrypt_enc_get_iv_size($td);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);

        $c_t = mcrypt_generic($td, $plain_text);

        mcrypt_generic_deinit($td);

        return base64_encode($c_t);
    }

    /**
     * Decrypt paste contents
     */
    static public function decrypt($content, $secret)
    {
        $plain_text = base64_decode($content);
        $key = $secret;

        $td = mcrypt_module_open('des', '', 'ecb', '');
        $key = substr($secret, 0, mcrypt_enc_get_key_size($td));
        $iv_size = mcrypt_enc_get_iv_size($td);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);

        $p_t = mdecrypt_generic($td, $plain_text);

        mcrypt_generic_deinit($td);


        return rtrim($p_t, '\0');
    }
}

