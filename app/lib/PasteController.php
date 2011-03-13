<?php
g()->load('Pages', 'controller');



/**
 * Heart of nyopaste, handling pastes
 * Type independent code
 * @author m.augustynowicz
 *
 * @todo default values in form
 */
class PasteController extends PagesController
{
    protected $_types = array();

    protected $_getOne_cache = null;

    public $forms = array(
        'paste' => array(
            'model' => 'Paste',
            'upload' => true,
            'inputs' => array(
                'paster',
                'title',
                'author',
                'source_url',
                'url',
                'tags' => array(
                    'fields' => null,
                    '_tpl'   => 'Forms/Encapsulating'
                ),

                'parent_id' => array(
                    '_tpl' => 'Forms/hidden'
                ),
                'root_id' => array(
                    '_tpl' => 'Forms/hidden'
                ),

                'content_type' => array(
                    'fields' => null,
                    '_tpl'   => 'Forms/FRadio-single'
                ),
                'content_text' => array(
                    'fields' => null,
                    '_tpl'   => 'Forms/FMultilineString'
                ),
                'content_file' => array(
                    'fields' => null,
                    '_tpl'   => 'Forms/FFile'
                ),


                // privacy
                'list',
                'encode' => array('fields' => null),
                'enc_passwd',
                //'acl' =>

                'type' => array(
                    'fields' => null,
                    '_tpl'=>'Forms/FRadio-single'
                ),

                // + captcha
                // + [accept TOS and add]
            ), // forms[paste][inputs]
        ), // forms[paste]
    ); // forms



    /**
     * Prepare default action.
     *
     * Add type-specific subcontrollers.
     * @author m.augustynowicz
     */
    protected function _prepareActionDefault(array & $params)
    {
        $this->_addPasteTypeSubcontrollers();
    }

    /**
     * Displaying a paste.
     *
     * @param array request params:
     *        - [0] pase's id, if none given, redirect to actionNew
     *        - [1] eigher "get" or "raw"
     *        - [v] paste version
     */
    public function actionDefault(array $params)
    {
        $url = @$params[0];
        $ver = @$params['v'];
        $display_type = @$params[1];

        if (!$url)
        {
            $this->redirect($this->url2a('new'));
        }


        // fetch main and paste type data
        if (!$this->getOne($url, $ver, true, $db_data))
        {
            return false;
        }

        switch ($display_type)
        {
            case 'get' :
                $this->assign(
                    'filename',
                    urlencode($db_data['title']) . '.' . g()->conf['paste_types']['source']['modes'][$db_data['syntax']]['extension']
                );
            case 'raw' :
                $this->assign('content', $db_data['content']);
                g()->view = g('TextView');
                $this->_setTemplate('default.raw');
                return;
        }

        // determine which action can be launched
        $action = $this->getLaunchedAction();
        if ($this->_default_action == $action)
            $action = '';
        $removed = $db_data['status'] & STATUS_DELETED;
        $action_params = array($url) + ($ver ? array('v'=>$ver) : array());
        $all_actions = array(
            'download'  => array($this, $action,   array(1=>'get') + $action_params),
            'raw'       => array($this, $action,   array(1=>'raw') + $action_params),
            'permalink' => array($this, $action,   $action_params),
            'newVer'    => array($this, 'new',     $action_params),
            'edit'      => array($this, 'edit',    $action_params),
            'remove'    => $removed ? false :
                           array($this, 'remove',  $action_params),
            'restore'   => !$removed ? false :
                           array($this, 'restore', $action_params),
        );
        foreach ($all_actions as $action => & $url)
        {
            if (false === $url)
                continue;
            $real_action = $url[1] ? $url[1] : 'default';
            if ($url[0] === $this)
            {
                if (!$this->hasAccess($real_action, $url[2]))
                    $url = false;
            }
            else
            {
                if (!g()->auth->hasAccess($url[0], $real_action, $url[2]))
                    $url = false;
            }
        }

        $basic_actions = array('download'=>1, 'raw'=>1, 'permalink'=>1);
        $db_data['BasicActions'] = array_intersect_key(
            $all_actions,
            $basic_actions
        );
        $db_data['Actions'] = array_diff_key(
            $all_actions,
            $basic_actions
        );

        $type = $this->_types[$db_data['type']];
        $this->assignByRef('row', $db_data);
        $this->assign('type', $type);
        $type->assign('row', $db_data);

    }


    /**
     * Prepare "search" action.
     *
     * Add paginator.
     * @author m.augustynowicz
     */
    protected function _prepareActionSearch(array & $params)
    {
        $this->addChild('Paginator', 'p');
    }

    /**
     * Search pastes.
     * @todo make it more "search", than "list"
     * @todo document params
     * @author m.augustynowicz
     */
    public function actionSearch(array $params)
    {
        $model = g('Paste', 'model');

        // let paginator set margins
        $this->getChild('p')->setMargins($model);

        $rows = $model->exec();
        $this->assignByRef('rows', $rows);
    }


    /**
     * Prepare "new" action.
     *
     * Add type-specific subcontrollers.
     * @author m.augustynowicz
     */
    protected function _prepareActionNew(array & $params)
    {
        $this->_addPasteTypeSubcontrollers();
    }

    /**
     * Create new paste
     * @author m.augustynowicz
     *
     * @param array $param request params:
     *        - [0] when creating new version of a paste, its the parent
     */
    public function actionNew(array $params)
    {
        $parent_url = @$params[0];
        $parent_ver = @$params['v'];

        $f = g('Functions');
        $form_id = 'paste';
        $inputs = & $this->forms[$form_id]['inputs'];
        $post_data = & $this->data[$form_id];
        $db_data = array();

        $this->assign(
            'max_upload_size_mb',
            min(
                $f->parseBytes(ini_get('upload_max_filesize')),
                $f->parseBytes(ini_get('post_max_size'))
            ) / 1024 / 1024
        );

        // list of static fields -- text gets rendered instead of input
        // key: name, value: text
        $static_fields = array();

        $new_ver = (bool) $parent_url;
        if($new_ver)
        {
            $this->getOne($parent_url, $parent_ver, false, $db_data);
            $static_fields = array(
                'title'         => &$db_data['title'],
                'url'           => &$db_data['url'],
                //'author'        => &$db_data['author'],
                //'source_url'    => &$db_data['source_url'],
                'tags'          => &$db_data['tags'],
                'paster'        => g()->auth->displayName(),
                'type'          => &$db_data['type'],
            );
            $one_type = $this->_types[$static_fields['type']];
            $this->_types = array();
            $this->_types[$static_fields['type']] = $one_type;
        }

        /** @todo handle editing? */
        if (empty($post_data))
        {
            // fill up form data
            if ($parent_url)
            {
                $post_data = $db_data;
                $post_data['content_text'] = & $post_data['content'];
                $post_data['parent_id'] = $db_data['id'];
                $post_data['root_id'] = $db_data['root_id'];

                /*
                foreach ($this->_types as $type)
                {
                    $type->data[$form_id] = $post_data;
                }
                 */
            }

            $post_data = (array)$post_data + g()->conf['paste']['defaults'];
        }
        else
        {
            if (@$this->_validated[$form_id])
            {
                $insert_data = $static_fields + $this->data[$form_id];
                g()->db->startTrans();
                do // just so we can break on errors
                {
                    /*
                    $db_data['last_edit'] = strtotime($db_data['last_edit']);
                    if ($post_data['_timestamp'] > $db_data['last_edit'])
                    {
                        $post_data['last_edit'] = & $post_data['_timestamp'];
                    }
                    else
                    {
                        g()->addInfo('post confict, updating user'.$id, 'error',
                            $this->trans('((error:POST conflict))') );
                        break;
                    }
                     */

                    $paste = g('Paste', 'model');

                    // url field must be not null and unique.
                    if (!@$static_fields['url'])
                    {
                        $insert_data['url'] = $paste->uniquifyURL(
                            @$insert_data['url'], @$insert_data['title']
                        );
                    }

                    $insert_data = $paste->getNewTreeData(@$db_data) + $insert_data;

                    if (true !== $err = $paste->sync($insert_data, true, 'insert'))
                    {
                        g()->addInfo('ds fail, inserting paste', 'error',
                            $this->trans('((error:DS:%s))', false) );
                        g()->debug->dump($err);
                        break;
                    }

                    $paste_id = $paste->getData('id');

                    if (!$post_data['root_id'])
                    {
                        $update_data = array(
                            'id' => $paste_id,
                            'root_id' => $paste_id
                        );
                        if (true !== $err = g('Paste','model')->sync($update_data, true, 'update'))
                        {
                        g()->addInfo('ds fail, inserting paste, updating root_id', 'error',
                            $this->trans('((error:DS:%s))', false) );
                        g()->debug->dump($err);
                        break;
                        }
                    }


                    if ($insert_data['tags'])
                    {
                        $paste_tag = g('PasteTag', 'model');
                        $tags = preg_split('/[\n\r,]+/', trim($insert_data['tags']));
                        $tags_insert = array();
                        foreach ($tags as $tag)
                        {
                            $tag_insert[] = array(
                                'paste_id' => $paste_id,
                                'tag' => $tag
                            );
                        }
                        if (true !== $err = $paste_tag->sync($tag_insert, true, 'insert'))
                        {
                            g()->addInfo('ds fail, inserting paste type', 'error',
                                $this->trans('((error:DS:%s))', false) );
                            g()->debug->dump($err);
                            break;
                        }
                    }


                    $type = $this->_types[$paste->getData('type')];
                    if (true !== $err = $type->sync($paste, 'insert'))
                    {
                        g()->addInfo('ds fail, inserting paste type', 'error',
                            $this->trans('((error:DS:%s))', false) );
                        g()->debug->dump($err);
                        break;
                    }

                    // if we got here, everything's must have gone fine
                    g()->addInfo(null, 'info',
                                 $this->trans('Paste created') );
                    g()->db->completeTrans();
                    $this->redirect($this->url2a(
                        '',
                        array(
                            $paste->getData('url'),
                            'v' => $paste->getData('version')
                        )
                    ));
                }
                // just so we can break on errors
                while ('past' > 'future');

                // if we are here, we have failed
                g()->db->failTrans();
                g()->db->completeTrans();
            }
        }

        if (g()->auth->loggedIn())
        {
            $static_fields['paster'] = $post_data['paster']
                    = g()->auth->displayName();
        }

        foreach ($static_fields as $input_name => $static_value)
        {
            $this->forms[$form_id]['inputs'][$input_name]['tpl'] = 'Forms/static';
        }
        $this->assignByRef('static_fields', $static_fields);
    }


    /**
     * Error 404: paste not found.
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        - [0] url of a missing paste
     * @return void
     */
    public function actionError404(array $params)
    {
        $this->assign('url', $params[0]);
    }


    /**
     * Common code for fetching one paste
     * @author m.augustynowicz
     *
     * @param mixed $id from URL
     * @param array $result rerence result will be stored to
     * @param bool $redirect if set to true, will redirect to error page,
     *        when no user fetched
     * @return bool success on fetching data
     */
    public function getOne($url, $ver, $with_tree=false, &$result, $redirect=true)
    {
        if (isset($this->_getOne_cache[$url][$ver][$with_tree]))
        {
            $result = $this->_getOne_cache[$url][$ver][$with_tree];
            return false !== $result;
        }

        $model = g('Paste', 'model');

        $result = $model->getByUrl($url, $ver);

        if (!$result)
        {
            if ($redirect)
            {
                $params = $this->getParams();
                $this->delegateAction('error404', $params);
                return false;
            }
            else
            {
                return false;
            }
        }

        $this->_types[$result['type']]
            ->getOne($result['id'], $result);

        if ($with_tree)
        {
            $model->order('creation', 'ASC');
            $model->order('id', 'ASC');
            $result['Tree'] = $model
                ->whiteList(array(
                    'parent_id',
                    'id',
                    'url',
                    'version',
                    'title',
                    'paster',
                    'paster_id',
                    'creation',
                ))
                ->getTree();
        }

        $this->_getOne_cache[$url][$ver][$with_tree] = $result;
        return true;
    }


    /**
     * Add type-specific subcontrollers.
     * @author m.augustynowicz
     */
    protected function _addPasteTypeSubcontrollers()
    {
        $paste_types = g()->conf['paste_types'];
        foreach ($paste_types as $type_name => & $type_conf)
        {
            $type = $this->addChild('PasteType'.ucfirst($type_name), $type_name);
            $type->data['paste'] = & $this->data['paste'];
            $this->_types[$type_name] = $type;
            $type->_action_to_launch = $this->_action_to_launch;
        }
        unset($type_conf);
        ksort($this->_types);
    }


    /**
     * Validating paste form
     *
     * Decides what content to use
     * @see validatePostContentText()
     * @see validatePostContentFile()
     * @author m.augustynowicz
     *
     * @param mixed @value set to false prevents the validation
     * @return array errors
     */
    public function validatePaste(array &$post)
    {
        if ('content_file' === $post['content_type'])
        {
            $post['content'] = & $post['content_file'];
            $post['content_text'] = false;
        }
        else
        {
            $post['content'] = & $post['content_text'];
            $post['content_file'] = false;
        }

        return array();
    }


    /**
     * Validating paste[content_text] form field
     *
     * Is validated as Paste model's content field, when not marked as dummy
     * @see validatePost()
     * @author m.augustynowicz
     *
     * @param mixed @value set to false prevents the validation
     * @return array errors
     */
    public function validatePasteContentText(& $value)
    {
        if (false === $value || g()->req->isAjax())
        {
            return array();
        }

        return g('Paste','model')->getField('content')->invalid($value);
    }


    /**
     * Validating paste[content_file] form field
     *
     * Reads uploaded file and put it's content into $value
     * @see validatePost()
     * @author m.augustynowicz
     *
     * @param mixed @value set to false prevents the validation
     * @return array errors
     */
    public function validatePasteContentFile(& $value)
    {
        if (false === $value || g()->req->isAjax())
        {
            return array();
        }

        switch (@$value['error'])
        {
            case UPLOAD_ERR_NO_FILE :
                return array('no_file' => $this->trans('No file uploaded'));
                break;
            case UPLOAD_ERR_FORM_SIZE :
            case UPLOAD_ERR_INI_SIZE :
                return array('too_big' => $this->trans('File too big'));
                break;
        }

        $value = file_get_contents($value['tmp_name']);
        $ret = g('Paste','model')->getField('content')->invalid($value);
        return $ret;
    }

    public function validatePasteUrl(&$value)
    {
        return array('stop_validation' => true);
    }


    /**
     * @todo implement this
     */
    /*
    public function validatePasteType(& $value)
    {
        $err = array();
        var_dump($value, $this->_types);
        if (!$value)
        {
            $err['notnull'] = 'No paste type chosen';
        }
        else if (!isset($this->_types[$value]))
        {
            $err['invalid'] = 'Incorrect paste type chosen';
        }
        return $err;
    }
     */


    /**
     * Checks access to "new" action
     * @todo implement me (for $params[0]
     * @author m.augustynowicz
     *
     * @param array $params request params
     *
     * @return bool
     */
    public function hasAccessToNew(array &$params)
    {
        return true;
    }

}

