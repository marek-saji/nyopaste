<?php
g()->load('Pages', 'controller');

/**
 * Handling users
 *
 * @todo implement changing e-mail
 * @todo implement list of users
 * @todo handle sensitive data (e-mail etc) when removing account
 * @todo allow modyfying account type
 * 
 * displaying, listing, signing ip/up/out/left/right/etc
 * @author m.augustynowicz
 */
class UserController extends PagesController implements IUserController
{

    public $forms = array(
        'login' => array(
            'ajax' => false,
            'model' => 'User',
            'inputs' => array(
                'login' => 'User',
                'passwd' => array(
                    null,
                    '_tpl' => 'Forms/FPassword_single',
                ),
            ),
        ),
        'new' => array(
            'ajax' => true,
            'model' => 'User',
            'inputs' => array(
                'login',
                'passwd',
                'email',
                'type' => array(
                    '_tpl' => 'Forms/FSelect',
                ),
            ),
        ),
        'edit' => array(
            'ajax' => true,
            'upload' => true,
            'model' => 'User',
            'inputs' => array(
                'email',
                'website',
                'about_me',
                'del_photo' => array(
                    '_tpl' => 'Forms/FBool',
                    'fields' => null,
                ),
                'photo' => array(
                    '_tpl' => 'Forms/FImageFile',
                    'fields' => null,
                ),

                'old_passwd' => array(
                    '_tpl'   => 'Forms/FPassword_single',
                    'fields' => null,
                ),
                'new_passwd' => array(
                    '_tpl'   => 'Forms/FPassword',
                    'fields' => null,
                ),
            ),
        ),
        'lostpasswd' => array(
            'ajax' => true,
            'model' => 'User',
            'inputs' => array(
                'email',
            ),
        ),
        'passwd_reset' => array(
            'ajax'   => true,
            'model'  => 'User',
            'inputs' => array(
                'passwd',
            ),
        ),
        'confirm' => array(
            'ajax' => false,
            'model' => false,
            'inputs' => array(),
        ),
        'delete' => array(
            'ajax' => false,
            'model' => 'User',
            'inputs' => array(
                'id' => array(
                    '_tpl' => 'Forms/hidden',
                ),
            ),
        ),
    );


    /**
     * Display user's profile
     *
     * @param array URL params
     *        [0] user's login, required
     */
    public function actionDefault(array $params)
    {
        $conf = & g()->conf['users'];
        $id = @$params[0];
        
        if (!$id)
        {
            $this->redirect(array('HttpErrors', '', array(404)));
        }


        // fetch user data
        $this->_getOne($id, $db_data);


        // determine which action links we should display

        $db_data['Actions'] = array(
            'edit'    => true,
            'remove'  => !($db_data['status'] & STATUS_DELETED)
                         && $db_data['id'] > 0,
            'restore' => (bool) ($db_data['status'] & STATUS_DELETED)
        );
        foreach ($db_data['Actions'] as $action => & $permitted)
        {
            if (!$permitted)
                continue;
            $permitted = (bool) $this->hasAccess($action, $params);
        }
        unset($permitted);


        $this->assignByRef('row', $db_data);
    }

    /**
     * Log in
     *
     * @params array URL params
     *         [0] user's login, optional
     */
    public function actionLogin(array $params)
    {
        $post_data = & $this->data['login'];
        if (!$post_data)
        {
            $this->data['login']['login'] = @$params[0];
        }
        else
        {
            $auth_data = array(
                'login'  => $post_data['login'],
                'passwd' => $post_data['passwd'],
            );
            if (g()->auth->login($auth_data))
            {
                g()->addInfo('signed in', 'info',
                        $this->trans('Welcome, <em>%s</em>',
                        g()->auth->displayName() ));
                $this->redirect($post_data['_backlink']);
            }
            else
            {
                $err_name = g()->auth->getLastError();
                if ('not_active' == $err_name)
                    $err_msg = 'This account has not been activated yet';
                else
                {
                    $err_msg = $this->trans(
                        'Wrong login or password. Don\'t remember your password? You can %s.',
                        $this->l2c($this->trans('reset it'), 'User', 'lostPasswd', array(), array('class'=>'modal', 'title'=>$this->trans('reset your password')))
                    );
                }
                $err_ident = 'siging in';
                if (g()->debug->on())
                    $err_ident .= ' ' . $err_name;
                g()->addInfo($err_ident, 'error', $this->trans($err_msg));
            }
        }

    }


    /**
     * Log out
     *
     * @param array URL params, none used
     */
    public function actionLogout(array $params)
    {
        $who = g()->auth->displayName();

        g()->auth->logout();

        g()->addInfo(
            'signing out',
            'info',
            'Good bye, %s',
            $who
        );

        $this->redirect((string)g()->req->getReferer());
    }


    /**
     * Create new user
     *
     * We are assuming, that if we are logged-in, we can
     * add any user with group id <= ours
     *
     * @param array $param URL params, none used
     */
    public function actionNew(array $params)
    {
        $f = g('Functions');
        $form_id = 'new';
        $inputs = & $this->forms[$form_id]['inputs'];
        $post_data = & $this->data[$form_id];

    	if (g()->auth->loggedIn())
    	{
	        $types = array(
	            USER_TYPE_ADMIN => $this->trans('administrator'),
	            USER_TYPE_MOD => $this->trans('moderator'),
	            USER_TYPE_AUTHORIZED => $this->trans('regular user'),
	        );
            // don't allow me to create users with higher GIDs, than my own
            $my_gid = g()->auth->get('type');
            foreach ($types as $gid => & $tmp)
            {
                if ($gid > $my_gid)
                {
                    unset($types[$gid]);
                }
            }
            unset($tmp);
	        $this->assignByRef('user_types_values', $types);
    	}


        $use_captcha = !g()->auth->loggedIn() && !g()->debug->on('disable', 'captcha');
        if ($use_captcha)
        {
            g()->load('recaptcha-php-1.10/recaptchalib', null);
            $this->assign('recaptcha_publickey',
                          g()->conf['keys']['recaptcha']['public']);
        }
        $this->assign('use_captcha', $use_captcha);


        if (!@$this->_validated[$form_id])
            return; // nothing to do.


        if ($use_captcha)
        {
            $recaptcha_response = recaptcha_check_answer(
                    g()->conf['keys']['recaptcha']['private'],
                    $_SERVER['REMOTE_ADDR'],
                    $_POST['recaptcha_challenge_field'],
                    $_POST['recaptcha_response_field']
                );
            if (!$recaptcha_response->is_valid)
            {
                g()->addInfo('wrong captcha, user adding', 'error',
                        $this->trans('Entered CAPTCHA code is incorrect, try again') );
                return false;
            }
        }


        $post_data['creation_date'] = time();
        $post_data['last_edit'] = $post_data['_timestamp'];
        $post_data['status'] = STATUS_ACTIVE;

        if (!g()->auth->loggedIn())
        	$post_data['type'] = USER_TYPE_AUTHORIZED;

        $user = g('User', 'model');
        if (true !== $err = $user->sync($post_data, true, 'insert'))
        {
            g()->addInfo('ds fail, adding user'.$id, 'error',
                $this->trans('((error:DS:%s))', false) );
            g()->debug->dump($err);
            return;
        }
        else
        {
            /** @todo send an e-mail */

            if (g()->auth->loggedIn())
            {
                g()->addInfo('user created', 'info',
                        $this->trans('New user account has been created') );
                if (empty($post_data['_backlink']))
                {
                    $id = $user->getData($conf['ident_field']);
                    $this->redirect($this->url2a('', array($id)));
                }
                else
                {
                    $this->redirect($post_data['_backlink']);
                }
            }
            else
            {
                g()->addInfo('user created', 'info',
                        $this->trans('Your account has been created. You may sign in now') );
                $this->redirect($this->url2a('login'));
            }
        }
    }


    /**
     * Editing profile information
     * @author m.augustynowicz
     *
     * @param array URL params
     *        [0] user's login, required
     */
    public function actionEdit(array $params)
    {
        $f = g('Functions');
        $form_id = 'edit';
        //$inputs = & $this->forms[$form_id]['inputs'];
        $post_data = & $this->data[$form_id];

        $conf = & g()->conf['users'];
        $id = @$params[0];

        if (!$id)
        {
            $this->redirect(array('HttpErrors', '', array(404)));
        }

        // fetch user data
        $this->_getOne($id, $db_data);


        // determine which action links we should display

        $db_data['Actions'] = array(
            'default' => false,
            'remove'  => !($db_data['status'] & STATUS_DELETED)
                         && $db_data['id'] > 0,
            'restore' => (bool) ($db_data['status'] & STATUS_DELETED)
        );
        foreach ($db_data['Actions'] as $action => & $permitted)
        {
            if (!$permitted)
                continue;
            $permitted = (bool) $this->hasAccess($action, $params);
        }
        unset($permitted);


        if (empty($post_data))
        {
            // fill up form data
            $post_data = $db_data;
        }
        else
        {
            // change password
            if ('' !== $post_data['old_passwd'])
            {
                if (md5($post_data['old_passwd']) != $db_data['passwd'])
                {
                    $form_key  = $this->getFormsIdent($form_id);
                    $input     = 'old_passwd';
                    $error_id  = 'incorrect';
                    $error_msg = $this->trans('Incorrect password');
                    g()->addInfo("$form_key $input $error_id", 'forms', $error_msg);
                    $this->_validated[$form_id] = false;
                }
                else // if passwords do not match
                {
                    $post_data['passwd'] = $post_data['new_passwd'];
                }
            }

            if ($this->_validated[$form_id])
            {
                //g()->db->startTrans();
                do // just so we can break on errors
                {
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


                    $post_data['id'] = & $db_data['id'];
                    $user = g('User', 'model');
                    if (true !== $err = $user->sync($post_data, true, 'update'))
                    {
                        g()->addInfo('ds fail, updating user'.$id, 'error',
                            $this->trans('((error:DS:%s))', false) );
                        g()->debug->dump($err);
                        break;
                    }

                    // if we got here, everything's must have gone fine
                    g()->addInfo('updating user'.$id, 'info',
                                 $this->trans('Profile has been updated') );
                    //g()->db->completeTrans();
                    if (empty($post_data['_backlink']))
                        $this->redirect($this->url2a('', $this->_params));
                    else
                        $this->redirect($post_data['_backlink']);
                }
                // just so we can break on errors
                while ('what you see' == 'what you get');
                // if we are here, we have failed
                //g()->db->failTrans();
                //g()->db->completeTrans();
            }
        }

        $this->assignByRef('row', $db_data);
    }

    /**
     * Removing user
     * @author m.augustynowicz
     *
     * @param array $params URL params
     *        [0] $id user's login
     */
    public function actionRemove(array $params)
    {
        $f = g('Functions');
        $form_id = 'confirm';
        //$inputs = & $this->forms[$form_id]['inputs'];
        $post_data = & $this->data[$form_id];

        $conf = & g()->conf['users'];
        $id = @$params[0];

        if (!$id)
        {
            $this->redirect(array('HttpErrors', '', array(404)));
        }

        // fetch user data
        $this->_getOne($id, $db_data);

        if (@$this->_validated[$form_id])
        {
            // these users cannot be removed
            if ($db_data['id'] < 0)
            {
                g()->addInfo('will not rm sys user, deleting user'.$id, 'error',
                        $this->trans('Can\'t remove system user <em>%s</em>', $db_data['DisplayName']) );
                $this->redirect($post_data['_backlink']);
            }

            $data_update = array('id' => $db_data['id']);
            if (g()->auth->isUserInGroup(USER_TYPE_MOD))
                $data_update['status'] = STATUS_DELETED;
            else
                $data_update['status'] = STATUS_DELETED_BY_OWNER;
            /** @todo remove sensitive data, release the login and e-mail */
            $user = g('User', 'model');
            if (true !== $err = $user->sync($data_update, true, 'update'))
            {
                g()->addInfo('ds fail, deleting user'.$id, 'error',
                    $this->trans('((error:DS:%s))', false) );
                g()->debug->dump($err);
            }
            else
            {
                if (g()->auth->id() == $db_data['id'])
                {
                    g()->addInfo('removed user'.$id, 'info',
                            $this->trans('You have removed your account. Good bye.') );
                    g()->auth->logout();
                }
                else
                {
                    g()->addInfo('removed user'.$id, 'info',
                        $this->trans('You have removed <em>%s\'s</em> account.', $db_data['DisplayName']) );
                }
                $this->redirect($post_data['_backlink']);
            }
        }

        if (g()->auth->id() == $db_data['id'])
        {
            $this->assign('question',
                'Are you sure you want to <strong>remove your account</strong>? This <em>cannot be undone</em>.' );
        }
        else
        {
            $this->assign('question', $this->trans(
                'Are you sure you want to remove <em>%s</em>\'s account?',
                $db_data['DisplayName'] ));
        }
        $this->assign(array(
            'yes' => 'remove',
            'no'  => 'don\'t'
        ));
        $this->_setTemplate('confirm');
    }


    /**
     * Restoring user
     * @author m.augustynowicz
     *
     * @param array $params URL params
     *        [0] $id user's login
     */
    public function actionRestore(array $params)
    {
        $f = g('Functions');
        $form_id = 'confirm';
        //$inputs = & $this->forms[$form_id]['inputs'];
        $post_data = & $this->data[$form_id];

        $conf = & g()->conf['users'];
        $id = @$params[0];

        if (!$id)
        {
            $this->redirect(array('HttpErrors', '', array(404)));
        }

        // fetch user data
        $this->_getOne($id, $db_data);

        if (@$this->_validated[$form_id])
        {
            $data_update = array('id' => $db_data['id']);
            $data_update['status'] = STATUS_ACTIVE;
            /** @todo handle removed sensitive data, release the login and e-mail */
            $user = g('User', 'model');
            if (true !== $err = $user->sync($data_update, true, 'update'))
            {
                g()->addInfo('ds fail, restoring user'.$id, 'error',
                    $this->trans('((error:DS:%s))', false) );
                g()->debug->dump($err);
            }
            else
            {
                g()->addInfo('removed user'.$id, 'info',
                    $this->trans('You have restored <em>%s\'s</em> account.', $db_data['DisplayName']) );
                $this->redirect($post_data['_backlink']);
            }
        }

        $this->assign(array(
            'question' => $this->trans('Are you sure you want to restore <em>%s</em>\'s account?',
                                $db_data['DisplayName'] ),
            'yes' => 'restore',
            'no'  => 'don\'t'
        ));
        $this->_setTemplate('confirm');
    }


    /**
     * @todo re-implement User::actionLostPasswd()
     */
    public function actionLostPasswd(array $params)
    {
        $form = 'lostpasswd';

        if ($this->_validated[$form])
        {
            $post_data = @$this->data[$form];
            $model = g('User', 'model');
            $user = $model->getRow(array('email'=>$post_data['email']));
            switch (true)
            {
                // no user
                case !$user :
                    g()->addInfo(
                        'user does not exist',
                        'error',
                        $this->trans(
                            'No user with such e-mail exist in our database. If it was not a typo, feel free to <a href="%s">sign up</a>!',
                            $this->url2a('new')
                        )
                    );
                    break;
                // user not active
                case $user['status'] != STATUS_ACTIVE :
                    g()->addInfo(
                        'user not active',
                        'error',
                        $this->trans('Account associated with this e-mail is not active.')
                    );
                    break;
                // ok, carry on..
                default :

                    $reset_hash = g('Functions')->generateSimpleKey(32);

                    $update = array(
                        'passwd_reset_hash'          => $reset_hash,
                        'passwd_reset_hash_creation' => 'NOW()'
                    );
                    $result = $model
                        ->filter(array('id' => $user['id']))
                        ->update($update, true)
                    ;
                    if (!$result)
                    {
                        g()->addInfo(
                            'db error',
                            'error',
                            $this->trans('((error:DS:%s))', false)
                        );
                    }
                    else
                    {
                        $mailer = g('Mail', array($this));
                        $mail_vars = array(
                            'reset_hash' => $reset_hash,
                            'user'       => & $user
                        );

                        if (!$mailer->send($user['email'], 'lostPasswd', $mail_vars))
                        {
                            g()->addInfo(
                                'db error',
                                'error',
                                $this->trans('((error:DS:%s))', $this->trans('Error while sending e-mail'))
                            );
                        }
                        else
                        {
                            g()->addInfo(
                                'passwd reset mail sent',
                                'info',
                                $this->trans('E-mail with a link allowing you to reset the password has been sent. You should receive it in matter of minutes.')
                            );
                            $this->redirect($post_data['_backlink']);
                        }
                    }
            }
        }
    }


    /**
     * Set new password
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        - [0] user id
     *        - [0] reset_passwd_hash
     * @return void
     */
    public function actionResetPasswd(array $params)
    {
        $user_id    = g('Functions')->isInt($params[0]) ? $params[0] : null;
        $reset_hash = $params[1];

        if (empty($user_id) || empty($reset_hash))
        {
            g()->addInfo(
                'invalid action params',
                'error',
                $this->trans('Invalid action parameters')
            );
            $this->redirect();
        }


        $model = g('User', 'model');

        $user = $model
            ->getRow(array(
                'id'                                 =>  $user_id,
                'passwd_reset_hash'                  =>  $reset_hash,
                array('passwd_reset_hash_creation', '>', strtotime('yesterday'))
            ))
        ;

        if (!$user)
        {
            g()->addInfo(
                'invalid action params',
                'error',
                $this->trans('Invalid action parameters')
            );
            $this->redirect();
        }

        $this->assignByRef('user', $user);


        if ($this->_validated['passwd_reset'])
        {
            $update = array(
                'passwd_reset_hash' => null,
                'passwd_reset_hash_creation' => null,
                'passwd' => $this->data['passwd_reset']['passwd']
            );

            $result = $model
                ->filter(array('id' => $user_id))
                ->update($update, true)
            ;

            if (!$result)
            {
                g()->addInfo(
                    'db error',
                    'error',
                    $this->trans(
                        '((error:DS:%s))',
                        $this->trans('Setting password failed')
                    )
                );
                $this->redirect();
            }
            else
            {
                g()->addInfo(
                    'passwd changed',
                    'info',
                    $this->trans('Password changed, you may sign-in now.')
                );
                $this->redirect(array($this->url(), 'login'));
            }
        }
    }


    /**
     * Validate user's login in [new] form
     *
     * Make sure it's well formatted and unique
     * @author m.augustynowicz
     *
     * @param string $value
     *
     * @return array errors
     */
    public function validateNewLogin(&$value)
    {
        $errors = array();
        $matches = array();

        if (preg_match('/[^a-zA-Z0-9-]/', $value, $matches))
        {
            $errors['forbidden_signs'] = $this->trans('Only letters, digits and dashes (<q>-</q>) are allowed');
        }
        else
        {
            if ($this->_getOne($value, $db_data, false))
            {
                $errors['not_unique'] = $this->trans('This login is already taken. If the account belongs to you, you can <a href="%s">retrieve your password</a>',
                        $this->url2a('lostPasswd') );
            }
        }

        return $errors;
    }


    /**
     * Validate repeated password in [add] form
     * @author m.augustynowicz
     *
     * @param array|string $value it double-valued array, when came from POST
     *        and flat string, when from AJAX
     * @return array errors
     */
    public function validateNewPasswd(&$value)
    {
        $errors = array();
        if (is_array($value))
        {
            $value0 = @array_shift($value);
            $value1 = @array_shift($value);
            if (null !== $value1) // two values given
            {
                if (!$ret = ($value0 == $value1))
                {
                    $errors['mismatch'] = $this->trans('Passwords do not match');
                    $errors['stop_validation'] = true;
                }
            }
            $value = (string) $value0;
        }
        return $errors;
    }


    /**
     * Validate user's e-mail in [add] form
     *
     * Make sure it's unique
     * @author m.augustynowicz
     *
     * @param string $value
     * @return array errors
     */
    public function validateNewEmail(&$value)
    {
        if ('' === $value)
        {
            return array('stop_validation' => true);
        }

        $errors = array();

        if ($value != NULL)
        {
            $this->_getOne($value, $db_data, false);

            if (!empty($db_data))
                $errors['not_unique'] = $this->trans('Account with this e-mail address already exists. If the account belongs to you, you can use <a href="%s">retrieve your password</a>.',
                        $this->url2a('lostPasswd') );
        }

        return $errors;
    }


    /**
     * Validate repeated password in [add] form
     */
    public function validateEditNewPasswd(&$value)
    {
        $errors = $this->validateNewPasswd($value);

        if ('' === $value)
        {
            return array('stop_validation' => true);
        }

        if (!$errors)
        {
            $errors = g('User', 'model')->validateField('passwd', $value);
        }
        return $errors;
    }

    public function validatePasswdResetPasswd(&$value)
    {
        return $this->validateNewPasswd($value);
    }


    /**
     * Check access to edit action
     * @author m.augustynowicz
     *
     * @param array $params same that will be passed to action
     */
    public function hasAccessToEdit(array &$params)
    {
        if (!$id = @$params[0])
            $this->redirect(array('HttpErrors', '', array(404)));

        $this->_getOne($id, $db_data);

        return g()->auth->id() == $db_data['id'];
    }


    /**
     * Check access to remove action
     * @author m.augustynowicz
     *
     * @param array $params same that will be passed to action
     */
    public function hasAccessToRemove(array &$params)
    {
        $can_edit = $this->hasAccess('edit', $params);

        if (!$can_edit)
        {
            return false;
        }
        else
        {
            $this->_getOne(@$params[0], $db_data);

            if (empty($db_data))
                return false;

            if ($db_data['id'] < 0)
                return false;
            
            return !($db_data['status'] & STATUS_DELETED);
        }
    }


    /**
     * Common code for fetching one user
     * @author m.augustynowicz
     *
     * @param mixed $id value of field defined in conf[users][ident_field]
     * @param array $result rerence result will be stored to
     * @param bool $redirect if set to true, will redirect to error page,
     *        when no user fetched
     * @return bool success on fetching data
     */
    protected function _getOne($id, & $result, $redirect=true)
    {
        static $can_restore = null;
        if (null === $can_restore)
        {
            $dummy = array();
            $can_restore = $this->hasAccess('restore', $dummy);
        }

        $conf = & g()->conf['users'];

        $filters = array($conf['ident_field'] => $id);
        if (!$can_restore)
            $filters['status'] = STATUS_ACTIVE;

        $result = g('User','model')->getRow($filters);

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

        $result['DisplayName'] = $result[$conf['display_name_field']];

        $result['AboutMe'] = g('TextParser')->parse('markdown', $result['about_me']);

        static $user_type_mapping = array(
            USER_TYPE_ADMIN => 'admin',
            USER_TYPE_MOD   => 'moderator'
        );

        $result['Type'] = $user_type_mapping[ $result['type'] ];

        return true;
    }



    protected function _prepareActionDefault(array &$params)
    {
        $this->addChild('Foo');
    }

    protected function _onRoutingToFoo($in_request)
    {
        echo 'JEST';
        if ($in_request)
            $this->_passRenderingTo('foo');
        return $in_request;
    }
}

