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

                'type_id' => array(
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
     */
    public function actionDefault(array $params)
    {
        $url = @$params[0];
        $display_type = @$params[1];

        if (!$url)
        {
            $this->redirect($this->url2a('new'));
        }


        // fetch main and paste type data
        if (!$this->_getOne($url, $db_data))
        {
            return false;
        }
        $type = $this->_types[$db_data['type_id']];
        $type->getOne($db_data['id'], $db_data);

        switch ($display_type)
        {
            case 'get' :
                $filename = urlencode($db_data['title']) . '.' . g()->conf['paste_types']['source']['modes'][$db_data['syntax']]['extension'];
                header('Content-Disposition: attachment; filename="'.$filename.'"');
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
        $all_actions = array(
            'download'  => array($this, $action,   array($url, 'get')),
            'raw'       => array($this, $action,   array($url, 'raw')),
            'permalink' => array($this, $action,   array($url)),
            'edit'      => array($this, 'edit',    array($url)),
            'remove'    => $removed ? false :
                           array($this, 'remove',  array($url)),
            'restore'   => !$removed ? false :
                           array($this, 'restore', array($url)),
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


        $this->assignByRef('row', $db_data);
        $this->assignByRef('type', $type);
        $type->assignByRef('row', $db_data);


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
     * @param array $param request params, ignored
     */
    public function actionNew(array $params)
    {
        $f = g('Functions');
        $form_id = 'paste';
        $inputs = & $this->forms[$form_id]['inputs'];
        $post_data = & $this->data[$form_id];

        // list of static fields -- text gets rendered instead of input
        // key: name, value: text
        $static_fields = array();


        /** @todo handle editing? */
        if (empty($post_data))
        {
            // fill up form data
            /*
            $post_data = $db_data;
             */
            if (g()->auth->loggedIn())
            {
                $static_fields['paster'] = $post_data['paster']
                        = g()->auth->displayName();
            }

            $this->data[$form_id]['content_type'] = 'content_text';
        }
        else
        {
            if ($this->_validated[$form_id])
            {
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
                    $post_data['url'] = $paste->uniquifyURL(
                        @$post_data['url'], @$post_data['title']
                    );

                    if (true !== $err = $paste->sync($post_data, true, 'insert'))
                    {
                        g()->addInfo('ds fail, inserting paste', 'error',
                            $this->trans('((error:DS:%s))', false) );
                        g()->debug->dump($err);
                        break;
                    }

                    $paste_id = $paste->getData('id');


                    if ($post_data['tags'])
                    {
                        $paste_tag = g('PasteTag', 'model');
                        $tags = preg_split('/[\n\r,]+/', trim($post_data['tags']));
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


                    $type = $this->_types[$post_data['type_id']];
                    if (true !== $err = $type->sync($paste, 'insert'))
                    {
                        g()->addInfo('ds fail, inserting paste type', 'error',
                            $this->trans('((error:DS:%s))', false) );
                        g()->debug->dump($err);
                        break;
                    }

                    // if we got here, everything's must have gone fine
                    g()->addInfo(null , 'info',
                                 $this->trans('Paste created') );
                    g()->db->completeTrans();
                    $new_url = $paste->getData('url');
                    $this->redirect($this->url2a('', array($new_url)));
                }
                // just so we can break on errors
                while ('past' > 'future');

                // if we are here, we have failed
                g()->db->failTrans();
                g()->db->completeTrans();
            }
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
        $v->addHeader('HTTP/1.0 404 Not Found');
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
    protected function _getOne($url, & $result, $redirect=true)
    {
        $filters = array('url' => $url);

        $result = g('Paste','model')->getRow($filters);

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


        $result['Tags'] = array();
        $tags = g('PasteTag','model')
                ->filter(array('paste_id'=>$result['id']))
                ->exec();
        foreach ($tags as &$tag)
        {
            $result['Tags'][] = $tag['tag'];
        }


        if (!$result['paster_id'])
        {
            $result['Paster'] = null;
        }
        else
        {
            $result['Paster'] = g('User','model')
                    ->getRow(array('id'=>$result['paster_id']));
            $display_name_field = g()->conf['users']['display_name_field'];
            $result['Paster']['DisplayName'] =
                    &$result['Paster'][$display_name_field];
        }

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
            $this->_types[$type->getIdx()] = $type;
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

}

