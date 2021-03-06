<?php
g()->load('Pages', 'controller');
g()->load('IProfile', 'controller');

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
class UserController extends PagesController implements IUserController, IProfileController
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
                //'email', TODO changing e-mail without e-mail confirmation it was not a good idea
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


    protected $_user = null;


    /**
     * Get user fetched with _getUser().
     *
     * For use by subcontrollers
     * @author m.augustynowicz
     *
     * @return array|null
     */
    public function getRow()
    {
        return $this->_user;
    }


    /**
     * Prepare "default" action.
     *
     * Add boxes subcontrollers
     * @author m.augustynowicz
     */
    protected function _prepareActionDefault(array & $params)
    {
        $user_ident = @$params[0];

        if ($user_ident)
        {
            $this->_getUser();

            $this->addChild('ProfileBoxes', 'Boxes');
            $this->addChild('Group', 'Groups');
        }
        else
        {
            $this->addChild('Paginator', 'p');
        }
    }


    /**
     * Route between user show and listing
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        [0] user's ident (login), required for display ctrl
     * @return void
     */
    public function actionDefault(array $params)
    {
        $user_ident = @$params[0];

        if ($user_ident)
        {
            return $this->_actionShow($params);
        }
        else
        {
            return $this->_actionList($params);
        }
    }


    /**
     * Display user's profile
     *
     * @param array URL params
     *        [0] user's ident (login), required
     */
    protected function _actionShow(array $params)
    {
        $boxes = $this->getChild('Boxes');
        if ($boxes->getLaunchedAction() !== '')
        {
            $this->_passRenderingTo($boxes);
            return;
        }


        $this->_setTemplate('profile');


        $profile_owner = ($this->_user['id'] == g()->auth->id());
        $this->assignByRef('profile_owner', $profile_owner);

        // determine which action links we should display

        $db_data = $this->_user;

        $db_data['Actions'] = array(
            'edit'    => true
        );
        if (false === $profile_owner)
        {
            $db_data['Actions'] += array(
                'remove'  => !($this->_user['status'] & STATUS_DELETED)
                             && $this->_user['id'] > 0,
                'restore' => (bool) ($this->_user['status'] & STATUS_DELETED)
            );
        }
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
     * List users
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        - [0] group id, show only group's members
     * @return void
     */
    protected function _actionList(array $params)
    {
        $group_id = $this->getParentParam('Group');


        $this->_setTemplate('list');

        $user = g('User', 'model');
        $filter = array(
            'status' => STATUS_ACTIVE
        );
        if ( ! $group_id )
        {
            $model =& $user;
            $model->orderBy($user['login']);
        }
        else
        {
            $membership = g('GroupMembership', 'model');
            $model = new Join(
                $membership,
                $user,
                new FoBinary($membership['user_id'], '=', $user['id'])
            );
            $white_list = $user->getFields();
            $model->whiteList($white_list);

            $leader_id = $this->getAssigned('leader_id');
            if ($leader_id)
            {
                $model->orderBy(new FConst("id={$leader_id}",'FBool'), 'DESC');
            }
            $model->orderBy($membership['creation']);
            $filter['group_id'] = $group_id;
        }

        $model->filter($filter);

        $this->getChild('p')->setMargins($model);
        $rows = $model->exec();

        $this->assignByRef('rows', $rows);
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
            if (@g()->conf['keys']['recaptcha']['public'])
            {
                $this->assign('recaptcha_publickey',
                              g()->conf['keys']['recaptcha']['public']);
            }
            else
            {
                $use_captcha = false;
            }
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
                // welcome e-mail
                $user_email = $user->getData('email');
                $user_login = $user->getData('login');
                $user_ident = $user->getData(g()->conf['users']['ident_field']);
                if ($user_email)
                {
                    $mailer = g('Mail', array($this));
                    $mail_vars = array(
                        'user_login'  => $user_login,
                        'profile_url' => $this->url2a('', array($user_ident), true)
                    );

                    if (!$mailer->send($user_email, 'new', $mail_vars))
                    {
                        g()->addInfo(
                            'mail error',
                            'error',
                            $this->trans('There has been an error while sending welcome e-mail, we are sorry.')
                        );
                    }
                }

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

        $this->_getUser();
        $db_data = $this->_user;

        // determine which action links we should display

        $db_data['Actions'] = array(
            'default' => false,
            'remove'  => !($db_data['status'] & STATUS_DELETED)
                         && $db_data['id'] > 0,
            'restore' => (bool) ($db_data['status'] & STATUS_DELETED)
        );
        $db_data['permitted Actions'] = false;
        foreach ($db_data['Actions'] as $action => & $permitted)
        {
            if (!$permitted)
                continue;
            $permitted = (bool) $this->hasAccess($action, $params);
            $db_data['permitted Actions'] += $permitted;
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

        $this->_getUser();
        $db_data = $this->_user;

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

        $this->_getUser();
        $db_data = $this->_user;

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

        if (empty($value))
        {
            return $errors;
        }

        if (in_array(strtolower($value), g()->conf['users']['reserved logins']))
        {
            $errors['special_login'] = 'This login name is reserved, sorry. Try different one';
        }
        else
        {
            $matches = array();

            if (preg_match('/[^a-zA-Z0-9-]/', $value, $matches))
            {
                $errors['forbidden_signs'] = $this->trans('Only letters, digits and dashes (<q>-</q>) are allowed');
            }
            else
            {
                if ($this->_getUser(false, $value))
                {
                    $errors['not_unique'] = $this->trans('This login is already taken. If the account belongs to you, you can <a href="%s">retrieve your password</a>',
                            $this->url2a('lostPasswd') );
                }
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
            if ($this->_getUser(false, $value))
            {
                $errors['not_unique'] = $this->trans('Account with this e-mail address already exists. If the account belongs to you, you can use <a href="%s">retrieve your password</a>.',
                        $this->url2a('lostPasswd') );
            }
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
        $user = $this->_getUser(false, $params);
        return g()->auth->id() == @$user['id'];
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
            $user = $this->_getUser(false, $params);

            if (empty($user))
                return false;

            if ($user['id'] < 0)
                return false;
            
            return !($user['status'] & STATUS_DELETED);
        }
    }


    /**
     * Common code for fetching one user
     * @author m.augustynowicz
     *
     * @param bool $redirect if set to true, will redirect to error page,
     *        when no user fetched
     * @param array|string|null $params one of these:
     *        - null (`getParam(0)` will be used as ident),
     *          in this case result is stored in `$this->_user`
     *        - array with action params (`[0]` will be used as ident)
     *        - any other will be used as ident
     *
     * @return array|bool array with user data or false on failure
     */
    protected function _getUser($redirect = true, $params = null)
    {
        if ($params === null && $this->_user !== null)
        {
            return $this->_user;
        }

        static $can_restore = null;
        if (null === $can_restore)
        {
            $dummy = array();
            $can_restore = $this->hasAccess('restore', $dummy);
        }

        $conf = & g()->conf['users'];


        if ($params === null)
        {
            $id = $this->getParam(0);
        }
        else if (is_array($params))
        {
            $id = $params[0];
        }
        else
        {
            $id = $params;
        }


        if ($id === 'admin')
        {
            $new_id = @$conf['admin']['login'];
            if ($new_id)
            {
                $params = $this->getParams();
                $params[0] = $new_id;
                $this->redirect(array(
                    $this->url(),
                    '', // action included in url()
                    $params
                ));
            }
        }

        if (!$id)
        {
            $result = false;
        }
        else
        {
            $filters = array($conf['ident_field'] => $id);
            if (!$can_restore)
                $filters['status'] = STATUS_ACTIVE;

            $result = g('User','model')->getRow($filters);
        }

        if (!$result)
        {
            if ($redirect)
            {
                $this->redirect(array('HttpErrors', '', array(404)));
            }
            else
            {
                $result = false;
            }
        }

        if ($result)
        {
            $result['DisplayName'] = $result[$conf['display_name_field']];

            $parser = g('TextParser', 'class', array('parser'=>'markdown'));
            $result['DisplayDescription'] = $parser->parse($result['about_me']);

            static $user_type_mapping = array(
                USER_TYPE_ADMIN => 'admin',
                USER_TYPE_MOD   => 'moderator'
            );

            $result['ProfileType'] = 'user';
            $result['Type'] = @$user_type_mapping[ $result['type'] ];

            $result['DisplayCreation'] =
                g('Functions')->formatDate($result['creation'], DATE_SHOW_DATE)
            ;
        }

        if ($params === null)
        {
            $this->_user =& $result;
        }

        return $result;
    }

}

