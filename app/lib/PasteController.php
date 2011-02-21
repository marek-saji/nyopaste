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
                'source',
                'preffered_url',
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


    protected function _prepareActionNew(array & $params)
    {
        $this->_addPasteTypeSubcontrollers();
    }
    protected function _prepareActionDefault(array & $params)
    {
        $this->_addPasteTypeSubcontrollers();
    }
    protected function _addPasteTypeSubcontrollers()
    {
        foreach (array('plain', 'markup') as $type_name)
        {
            $type = $this->addChild('PasteType'.ucfirst($type_name), $type_name);
            $this->_types[$type->getIdx()] = $type;
            $type->_action_to_launch = $this->_action_to_launch;
        }
    }
    protected function _prepareActionList(array & $params)
    {
        $type = $this->addChild('Paginator', 'p');
    }


    /**
     * Displaying a paste.
     *
     * @param array request params:
     *        - [0] pase's id, if none given, redirect to actionNew
     */
    public function actionDefault(array $params)
    {
        $id = @$params[0];

        if (!$id)
        {
            $this->redirect($this->url2a('new'));
        }


        // fetch main and paste type data
        $this->_getOne($id, $db_data);
        $type = $this->_types[$db_data['type_id']];
        $type->getOne($id, $db_data);


        // determine which action can be launched
        $action = $this->getLaunchedAction();
        if ($this->_default_action == $action)
            $action = '';
        $removed = $db_data['status'] & STATUS_DELETED;
        $db_data['Actions'] = array(
            'get'     => array($this, $action,   array($id, 'get')),
            'plain'   => array($this, $action,   array($id, 'plain')),
            'edit'    => array($this, 'edit',    array($id)),
            'remove'  => $removed ? false :
                         array($this, 'remove',  array($id)),
            'restore' => !$removed ? false :
                         array($this, 'restore', array($id)),
        );
        foreach ($db_data['Actions'] as $action => & $url)
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
        var_dump($db_data['Actions']);


        $this->assignByRef('row', $db_data);
        $this->assignByRef('type', $type);
        $type->assignByRef('row', $db_data);


    }


    /**
     * Temporary listing action, for testing new request processing in hologram
     */
    public function actionList(array $params)
    {
        $model = g('Paste', 'model');

        // let paginator set margins
        $this->getChild('p')->setMargins($model);

        $rows = $model->exec();
        $this->assignByRef('rows', $rows);
    }


    /**
     * Create new paste
     * @author m.augustynowicz
     * @todo accept content_file
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
                    if (true !== $err = $paste->sync($post_data, true, 'insert'))
                    {
                        g()->addInfo('ds fail, inserting paste', 'error',
                            $this->trans('((error:DS:%s))', false) );
                        g()->debug->dump($err);
                        break;
                    }

                    $type = $this->_types[$post_data['type_id']];
                    if (true !== $type->sync($paste, 'insert'))
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
                    //if (empty($post_data['_backlink']))
                        $new_id = $paste->getData('id');
                        $this->redirect($this->url2a('', array($new_id)));
                    //else
                    //    $this->redirect($post_data['_backlink']);
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
     * Common code for fetching one paste
     * @author m.augustynowicz
     *
     * @param mixed $id from URL
     * @param array $result rerence result will be stored to
     * @param bool $redirect if set to true, will redirect to error page,
     *        when no user fetched
     * @return bool success on fetching data
     */
    protected function _getOne($id, & $result, $redirect=true)
    {
        $filters = array('id' => $id);

        $result = g('Paste','model')->getRow($filters);

        if (!$result)
        {
            if ($redirect)
            {
                $this->redirect(array('HttpErrors', '', array(404)));
            }
            else
            {
                return false;
            }
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

    public function validatePasteTitle()
    {
        return array();
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

