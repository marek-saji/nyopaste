<?php
g()->load('Pages', 'controller');

/**
 * @author m.augustynowicz
 * A class used for standard user's actions like login, register, etc.
 *
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
        'add' => array(
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
                'old_passwd' => array(
                    '_tpl' => 'Forms/FPassword_single',
                    'fields' => null,
                ),
                'passwd' => array(
                    '_tpl' => 'Forms/FPassword',
                    'fields' => null,
                ),
                'email',
                'del_photo' => array(
                    '_tpl' => 'Forms/FBool',
                    'fields' => null,
                ),
                'photo' => array(
                    '_tpl' => 'Forms/FImageFile',
                    'fields' => null,
                ),
                'location_coords',
                'country' => array(
                    '_tpl' => 'Forms/FSelect',
                ),
                'birth_date',
                'description' => array(
                    '_tpl' => 'Forms/FString_area',
                ),
                'notices',
            ),
        ),
        'edit_admin' => array(
            'ajax' => true,
            'upload' => true,
            'model' => 'User',
            'inputs' => array(
                'passwd' => array(
                    '_tpl' => 'Forms/FPassword',
                    'fields' => null,
                ),
                'email',
                'del_photo' => array(
                    '_tpl' => 'Forms/FBool',
                    'fields' => null,
                ),
                'photo' => array(
                    '_tpl' => 'Forms/FImageFile',
                    'fields' => null,
                ),
                'location_coords',
                'country' => array(
                    '_tpl' => 'Forms/FSelect',
                ),
                'birth_date',
                'description' => array(
                    '_tpl' => 'Forms/FString_area',
                ),
                'notices',
            ),
        ),
        'lostpasswd' => array(
            'ajax' => true,
            'model' => 'User',
            'inputs' => array(
                'email',
            ),
        ),
        'lostpasswd_reset' => array(
            'ajax' => true,
            'model' => 'User',
            'inputs' => array(
                'passwd',
            ),
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
    public function defaultAction(array $params)
    {
        $login = @$params[0];
        
        if (!$login)
        {
            $this->redirect(array('HttpError','error404'));
        }

        $filters = array('login' => $login);
        if (!g()->auth->isUserInGroup(USER_TYPE_ADMIN))
            $filters['status'] = STATUS_ACTIVE;

        $model = g('User','model');
        $model->setMargins(1)->filter($filters);
        $db_data = $model->exec();

        if (!$db_data)
        {
            // we could be more specific here
            $this->redirect(array('HttpError','error404'));
        }


        // determine which action links we should display

        $db_data['Actions'] = array(
            'edit' => false,
            'remove' => false,
            'recover' => false,
        );
        foreach ($db_data['Actions'] as $action => & $permitted)
        {
            $permitted = $this->hasAccess($action, $params);
        }


        $this->assignByRef('data', $db_data);
    }

    /**
     * Login
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
            var_dump($auth_data);
            if (g()->auth->login($auth_data))
            {
                $this->redirect($post_data['_backlink']);
            }
            else
            {
                $err_name = g()->auth->getLastError();
                if ('not_active' == $err_name)
                    $err_msg = 'This account has not been activated yet';
                else
                    $err_msg = 'Wrong e-mail or password';
                if (g()->debug->on())
                    $err_msg .= ' ' . $err_name;
                g()->addInfo('signing in', 'error', $this->trans($err_msg));
            }
        }

    }

    public function actionLogout(array $params)
    {
        g()->auth->logout();
        $this->redirect((string)g()->req->getReferer());
    }

    public function actionAdd(array $params)
    {
        /*if(g()->auth->loggedIn())
            $this->redirect();*/

    	if(!empty($params) && @$params['admin'] == '1')
    	{
    		$admin = true;
	        $types = array(
	            USER_TYPE_ADMIN => $this->trans('administrator'),
	            USER_TYPE_MOD => $this->trans('moderator'),
	            USER_TYPE_REGISTERED => $this->trans('registered user'),
	        );
	        $this->assign('values', $types);
    	}
   		else
   		{
   			$admin = false;
   			unset($this->forms['add']['inputs']['type']);
   		}

        $use_captcha = !g()->debug->on() && !$admin;

        if($use_captcha)
        {
            g()->load('recaptcha/recaptchalib', null);
            $this->assign('publickey', RECAPTCHA_PUBLIC_KEY);
        }

        $this->assign('use_captcha', $use_captcha);
        $this->assign('admin', $admin);
        $data = &$this->data['add'];

        if(!($data && $this->__validated['add']))
            return; // nothing to do.

        if($use_captcha)
        {
            $resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
            if(!$resp->is_valid)
            {
                g()->addInfo(null, 'error', $this->trans('The code wasn\'t entered correctly.'));
                return false;
            }
        }

        $data['creation_date'] = date('Y-m-d H:i:s');
        $data['status'] = STATUS_ACTIVE;

        if(!$admin)
        	$data['type'] = USER_TYPE_REGISTERED;

        $model = g('User', 'model');

        if($model->sync($data, true) !== true)
        {
            g()->addInfo(null, 'error', $this->trans('Error while adding user. Please try again later and if error still occurs -- contact this site\'s administrator.'));
            return;
        }
        else
        {
            if(!empty($data['email']))
            {
		        $mail_content = $this->trans('Welcome, %s!<br/><br/>Thank you for registration on our website.<br/><br/>Regards,<br/>%s team', $data['login'], g()->conf['site_name']);
		        $subject = $this->trans('Registration on %s website', g()->conf['site_name']);
                $mail = g('Mails', 'class');
                //$mail->setPort(587); - unnecessary, Mails class reads it in constructor from configuration
                $mail->send('', $mail_content, '', $subject, '', $data['email']);
            }

            if(!$admin)
            	g('Auth')->login(array('login' => $data['login'], 'passwd' => $data['passwd']));

        	g()->addInfo(null, 'info', $this->trans('User has been added.'));
           	$this->redirect($this->url2a('edit', array($data['login'])));
        }
    }

    public function actionEdit(array $params)
    {
        if(!$login = @$params[0])
            $this->redirect();

        $admin = false;

        if($this->hasAccess('edit', $params) === 1)
        {
        	if($login != g()->auth->get('login'))
        	{
	        	$admin = true;
	        	$this->data['edit'] = @$this->data['edit_admin'];
	        	$this->__validated['edit'] = @$this->__validated['edit_admin'];
        	}
        }

        $this->assign('admin', $admin);
        $this->assign('login', $login);
        $this->assign('values', g()->conf['countries']);

        $model = g('User', 'model');
        $model->filter(array('login' => $login));
        $model->setMargins(1);
        $data = $model->exec();

        $upload_dir = APP_DIR . 'htdocs/upload/';
        $images_model = g('ImagesUpload', 'model');
        $images_model->setUploadDir($upload_dir);

        if(!empty($data['photo_hash']))
        {
            $images_model->filter(array('id' => $data['photo_hash']));
            $images_model->setMargins(1);
            $images_data = $images_model->exec();
        }

        if(empty($data['country']))
        	$data['country'] = 'PL';

        $this->assign('avatar', !empty($data['photo_hash']) ? '/upload/User/' . $data['photo_hash'] . '/' . @$model->image_files['sizes'][0] . '.' . $images_data['extension'] : '/gfx/avatar.png');

        if(!empty($this->data['edit']) && $this->__validated['edit'])
        {
            if(!$this->data['edit']['passwd'])
                unset($this->data['edit']['passwd']);
        
            if(!empty($this->data['edit']['passwd']) && !$admin)
            {
                $model = g('User', 'model');
                $model->whiteList(array(
                    'id',
                ));
                $model->filter(array(
                	'login' => $login,
                	'passwd' => $this->data['edit']['old_passwd'],
                ));
                $exists = $model->getCount();

                if(!$exists)
                {
                    g()->addInfo(null, 'error', $this->trans('Given password is incorrect. Your password won\'t be changed.'));
                	unset($this->data['edit']['passwd']);
                }
            }

            if(!empty($this->data['edit']['email']))
            {
                $model = g('User', 'model');
                $model->whiteList(array(
                    'id',
                ));
                $model->filter(array(
                	'email' => $this->data['edit']['email'],
                    array('login', '!=', $login),
                ));
                $exists = $model->getCount();

                if($exists)
                {
                    g()->addInfo(null, 'error', $this->trans('User with this e-mail already exists. Use "forgotten password" feature to retrieve your password if you don\'t remember it.'));
                    return;
                }
            }

            $this->data['edit']['login'] = $login;
            $this->data['edit']['password_reset_link'] = '';
            $this->data['edit']['id'] = $data['id'];

            if(!empty($this->data['edit']['del_photo']))
            {
                if(!empty($data['photo_hash']))
                {
                    $d = array(
                        'id' => $images_data['id'],
                        'model' => $model->getName(),
                    );

                    if($images_model->sync($d, true, 'delete') === true)
                        $this->data['edit']['photo_hash'] = '';
                }
            }
            elseif(@$this->data['edit']['photo']['tmp_name'])
            {
                $d = array(
                    'id' => $data['photo_hash'],
                    'id_in_model' => $data['id'],
                    'model' => $model->getName(),
                    'file' => $this->data['edit']['photo'],
                    'title' => $login,
                );

                if(!empty($data['photo_hash']))
                    $action = 'update';
                else
                    $action = 'insert';

                if($images_model->sync($d, true, $action) === true)
                    $this->data['edit']['photo_hash'] = $images_model->getData('id');
            }
            else
                unset($this->data['edit']['photo']);

            if($model->sync($this->data['edit'], true, 'update') === true)
            {
                g()->addInfo('user edited ' . $data['id'], 'info', $this->trans('Changes has been saved.'));
                $this->redirect($this->url2a('', array($login)));
            }
            else
            {
                g()->addInfo(null, 'error', $this->trans('Error while editing user. Please try again later and if error still occurs -- contact this site\'s administrator.'));
                return;
            }
        }
        else
        {
            if(!empty($data))
            {
            	if($admin)
                	$this->data['edit_admin'] = $data;
               	else
                	$this->data['edit'] = $data;
            }
            else
            {
                g()->addInfo(null, 'error', $this->trans('There is no user with this ID.'));
                return;
            }
        }
    }

    public function actionDelete(array $params)
    {
        if(!$login = @$params[0])
            $this->redirect();

        $this->assign('login', $login);

        if($this->hasAccess('delete', $params) === 1)
            $status = STATUS_DELETED_BY_MOD;
        else// if($this->hasAccess('delete', $params) === true)
            $status = STATUS_DELETED_BY_OWNER;

        if(!empty($params[1]) && $params[1] === 'recover')
        {
            unset($this->forms['delete']['inputs']['with_objects']);
            unset($this->forms['delete']['inputs']['with_comments']);
            $status = STATUS_ACTIVE;
            $this->_setTemplate('recover');
        }

        $user = g('User', 'model');
        $user->filter(array('login' => $login));
        $user->setMargins(1);
        $user = $user->exec();

        if(empty($user) || $user['status'] == $status)
        {
            $msg = empty($user) ? 'There is no such user.' : 'User already deleted.';
            g()->addInfo(null, 'error', $this->trans($msg));
            $this->redirect($this->url2a('default'));
        }

        //these users cannot be deleted
        if($user['id'] < 0)
        {
            g()->addInfo(null, 'error', $this->trans('This user cannot be deleted.'));
            $this->redirect();
        }

        if(!empty($this->data['delete']))
        {
            g()->db->startTrans();

            do
            {
                $this->data['delete']['id'] = $user['id'];
                $this->data['delete']['status'] = $status;
                $model = g('User', 'model');

                if($model->sync($this->data['delete'], true, 'update') === true)
                {
                    //recovering objects and comments deleted with user
                    if($status == STATUS_ACTIVE)
                    {
                        $rel = $model->rel('Objects');
                        $rel->alias('o');
                        $rel->filter(array('owner' => $user['id'], '"o1"."status"' => STATUS_DELETED_WITH_USER));
                        $rel->whiteList(array('"o1"."id"', '"o1"."status"'));
                        $object_data = $rel->exec();

                        foreach($object_data as &$obj)
                            $obj['status'] = STATUS_ACTIVE;

                        if(!empty($object_data))
                            if(g('Object', 'model')->sync($object_data, true, 'update') !== true)
                            {
                                g()->db->failTrans();
                                g()->addInfo(null, 'error', $this->trans('Error while deleting user. Please try again later and if error still occurs -- contact this site\'s administrator.'));
                                break;
                            }

                        $rel = $model->rel('Comments');
                        $rel->alias('c');
                        $rel->filter(array('users_id' => $user['id'], '"c1"."status"' => STATUS_DELETED_WITH_USER));
                        $rel->whiteList(array('"c1"."id"', '"c1"."status"'));
                        $comment_data = $rel->exec();

                        foreach($comment_data as &$obj)
                            $obj['status'] = STATUS_ACTIVE;

                        if(!empty($comment_data))
                            if(g('Comment', 'model')->sync($comment_data, true, 'update') !== true)
                            {
                                g()->db->failTrans();
                                g()->addInfo(null, 'error', $this->trans('Error while deleting user. Please try again later and if error still occurs -- contact this site\'s administrator.'));
                                break;
                            }
                    }

                    //deleting objects with user
                    if(!empty($this->data['delete']['with_objects']))
        			{
                        $rel = $model->rel('Objects');
                        $rel->alias('o');
                        $rel->filter(array('owner' => $user['id']));
                        $rel->whiteList(array('"o1"."id"', '"o1"."status"'));
                        $object_data = $rel->exec();

                        foreach($object_data as &$obj)
                            $obj['status'] = STATUS_DELETED_WITH_USER;

                        if(!empty($object_data))
                            if(g('Object', 'model')->sync($object_data, true, 'update') !== true)
                            {
                                g()->db->failTrans();
                                g()->addInfo(null, 'error', $this->trans('Error while deleting user. Please try again later and if error still occurs -- contact this site\'s administrator.'));
                                break;
                            }
        			}

                    if(!empty($this->data['delete']['with_comments']))
        			{
                        $rel = $model->rel('Comments');
                        $rel->alias('c');
                        $rel->filter(array('users_id' => $user['id']));
                        $rel->whiteList(array('"c1"."id"', '"c1"."status"'));
                        $comment_data = $rel->exec();

                        foreach($comment_data as &$obj)
                            $obj['status'] = STATUS_DELETED_WITH_USER;

                        if(!empty($comment_data))
                            if(g('Comment', 'model')->sync($comment_data, true, 'update') !== true)
                            {
                                g()->db->failTrans();
                                g()->addInfo(null, 'error', $this->trans('Error while deleting user. Please try again later and if error still occurs -- contact this site\'s administrator.'));
                                break;
                            }
        			}

                    g()->db->completeTrans();

                    if($status == STATUS_DELETED_BY_OWNER)
                    {
                        g()->auth->logout();
                        $this->redirect();
                    }

                    $this->redirect($this->url2a('', array($login)));
                    break;
                }
                else
                {
                    g()->db->failTrans();
                    g()->addInfo(null, 'error', $this->trans('Error while deleting user. Please try again later and if error still occurs -- contact this site\'s administrator.'));
                    break;
                }
            }
            while(false);

            g()->db->completeTrans();
        }
    }

    public function actionLostPasswd(array $params)
    {
        $model = g('User', 'model');

        if((!$login = @$params[0]) || (!$hash = @$params[1]))
        {
            if(!empty($this->data['lostpasswd']) && $this->__validated['lostpasswd'])
            {
                $model->filter(array('email' => $this->data['lostpasswd']['email']));
                $model->setMargins(0, 1);
                $model->whiteList(array('id', 'login', 'email', 'password_reset_link'));
                $data = $model->exec();

                if(!empty($data))
                {
                    set_time_limit(360);
                    $model->filter(array('id' => $data['id']));
                    $hash = empty($data['password_reset_link']) ? g('Functions')->generateSimpleKey(40) : $data['password_reset_link'];
                    $data['password_reset_link'] = $hash;

                    if($model->sync($data, true) === true)
                    {
                        $link = g()->req->getBaseUri(true) . $this->url2a('lostPasswd', array($data['login'], $hash));
                        $mail_content = $this->trans('Welcome, %s!<br/><br/>Someone (probably you) gave your e-mail address for reseting your password on our website.<br/>Here is a link to password changing:<br/>%s<br/><br/>Regards,<br/>%s team', $data['login'], '<a href="' . $link . '">' . $link . '</a>', g()->conf['site_name']);
                        $subject = $this->trans('Password reminder on %s website', g()->conf['site_name']);
                        $mail = g('Mails', 'class');

                        if($mail->send('', $mail_content, '', $subject, '', $data['email']))
                            $this->_setTemplate('lostpasswd_sent');
                        else
                        {
                            g()->addInfo(null, 'error', $this->trans('Error while sending an e-mail. Please try again later and if error still occurs -- contact this site\'s administrator.'));
                            return;
                        }
                    }
                    else
                    {
                        g()->addInfo(null, 'error', $this->trans('Error while reseting password. Please try again later and if error still occurs -- contact this site\'s administrator.'));
                        return;
                    }
                }
                else
                {
                    g()->addInfo(null, 'error', $this->trans('E-mail address does not exist in database!'));
                    return;
                }
            }
        }
        else
        {
            $model->filter(array('login' => $login, 'password_reset_link' => $hash));
            $model->setMargins(0, 1);
            $model->whiteList(array('id', 'login', 'email', 'password_reset_link'));
            $data = $model->exec();

            if(empty($data))
                $this->redirect($this->url2a('lostPasswd'));

            $this->_setTemplate('lostpasswd_reset');
            
            if(!empty($this->data['lostpasswd_reset']) && $this->__validated['lostpasswd_reset'])
            {
                $data['passwd'] = $this->data['lostpasswd_reset']['passwd'];
                $data['password_reset_link'] = '';

                if($model->sync($data, true) !== true)
                {
                    g()->addInfo(null, 'error', $this->trans('Error while reseting password. Please try again later and if error still occurs -- contact this site\'s administrator.'));
                    return;
                }

                g()->addInfo(null, 'info', $this->trans('You can log in with your new password.'));
                $this->redirect($this->url2a('login', array($data['login'])));
            }
        }
    }
    
    public function actionPresentTrip(array $params)
    {
        $this->_trips($params,'Show');
    }
    
    public function actionEditTrip(array $params)
    {
        $this->_trips($params,'Edit');
    }
    
    public function actionReviewTrip(array $params)
    {
        $this->_trips($params,'Review');
    }
    
    public function actionTripPhotos(array $params)
    {
        $this->_trips($params,'Photos');
    }

    private function _trips(array $params, $action)
    {
        if(!$trip_id = @$params[0])
            $this->redirect();

        $this->trip_component->launchAction($action, $params);
        $this->_setTemplate('presenttrip');
    }

    public function actionAdminister(array $params)
    {
        $model = g('User', 'model');
        $model->filter(array(
            array('id', '>', '0'),
            array('type', '<', '0'),
        ));
        $model->order('status', 'DESC');
        $model->order('type', 'DESC');
        $model->order('login', 'ASC');
        $data = $model->exec();

        $this->assign('list', $data);
    }

    public function actionChangeType(array $params)
    {
        if(!$login = @$params[0])
            $this->redirect();

        $model = g('User', 'model');
        $model->filter(array('login' => $login));
        $model->setMargins(1);
        $data = $model->exec();

        if($data['id'] < 0)
        {
            g()->addInfo(null, 'error', $this->trans('This user cannot be edited.'));
            return;
        }

        $types = array(
            USER_TYPE_ADMIN => $this->trans('administrator'),
            USER_TYPE_MOD => $this->trans('moderator'),
            USER_TYPE_REGISTERED => $this->trans('registered user'),
        );

        $this->assign('login', $login);
        $this->assign('values', $types);

        if(!empty($this->data['changetype']) && $this->__validated['changetype'])
        {
            if(array_key_exists($this->data['changetype']['type'], $types))
            {
                $this->data['changetype']['id'] = $data['id'];

                if($model->sync($this->data['changetype'], true, 'update') === true)
                {
                    g()->addInfo('account type changed ' . $data['id'], 'info', $this->trans('Changes has been saved.'));
                    $this->redirect($this->url2a('administer'));
                }
                else
                {
                    g()->addInfo(null, 'error', $this->trans('Error while editing user. Please try again later and if error still occurs -- contact this site\'s administrator.'));
                    return;
                }
            }
        }
        else
        {
            if(!empty($data))
                $this->data['changetype'] = $data;
            else
            {
                $this->assign('values', null);
                g()->addInfo(null, 'error', $this->trans('There is no user with this ID.'));
                return;
            }
        }
    }

    public function validateAddPasswd(&$value)
    {
        $errors = array();
        if(is_array($value))
        {
            $value0 = @array_shift($value);
            $value1 = @array_shift($value);
            if(null !== $value1) // two values given
            {
                if(!$ret = ($value0 == $value1))
                {
                    $errors['mismatch'] = $this->trans('Passwords do not match');
                    $errors['stop_validation'] = true;
                }
            }
            $value = (string)$value0;
        }
        return $errors;
    }

    public function validateAddLogin(&$value)
    {
        $errors = array();
        $matches = array();

        if(preg_match_all('/[^a-zA-Z0-9-]/', $value, $matches))
            $errors['forbidden_signs'] = $this->trans('Only letters, digits and "-" are allowed.');

        $model = g('User', 'model');
        $model->whiteList(array(
            'id',
        ));
        $model->filter(array(
            'login' => $value,
        ));
        $exists = $model->getCount();

        if($exists)
            $errors['not_unique'] = $this->trans('Login taken');

        return $errors;
    }

    public function validateAddEmail(&$value)
    {
        $errors = array();

        if($value != NULL)
        {
            $model = g('User', 'model');
            $model->whiteList(array(
                'id',
            ));
            $model->filter(array(
                'email' => $value,
            ));
            $model->setMargins(0, 1);
            $exists = $model->exec();
            $exists = !empty($exists);

            if($exists)
                $errors['not_unique'] = $this->trans('User with this e-mail already exists. Use "forgotten password" feature to retrieve your password if you don\'t remember it.');
        }

        return $errors;
    }

    public function validateEditPhoto(&$value)
    {
        $errors = array();
        $mime = 0; // = don't check
        $i_model = g('ImagesUpload', 'model');

        if(is_array($value) && @$value['tmp_name'])
        {
            // false == given path is not relative to UPLOAD_DIR
            if(!g()->conf['get_mime_type_by_suffix'])
                $mime = $i_model->getMIMETypeByFile($value['tmp_name'], false);
            else
            {
                $ext = explode('.', $value['name']);
                $ext = $ext[count($ext) - 1];
                $mime = $i_model->getMIMETypeBySuffix($ext);
            } 
        }
        elseif((!is_array($value)) && $value)
        {
            $value = trim($value);
            $value = str_replace('\\\\', '/', $value);
            $value = explode('/', $value);
            $value = $value[count($value) - 1];

            if(strlen($value) > 0)
            {
                $ext = explode('.', $value);
                $ext = $ext[count($ext) - 1];
                $mime = $i_model->getMIMETypeBySuffix($ext);
            }
        }

        if(0 !== $mime && !in_array($mime, $this->_acceptedPhotoMIMETypes))
            $errors['wrong_type'] = $this->trans('Photo must be a JPEG, GIF or PNG file.');

        return $errors;
    }

    public function validateEditPasswd(&$value)
    {
        return $this->validateAddPasswd($value);
    }
    
    public function validateEditAdminPhoto(&$value)
    {
    	return $this->validateEditPhoto($value);
    }

    public function validateEditAdminPasswd(&$value)
    {
        return $this->validateEditPasswd($value);
    }

    public function validateLostPasswdResetPasswd(&$value)
    {
        return $this->validateAddPasswd($value);
    }

    public function hasAccessToEdit(array $params = array())
    {
        if(!$login = @$params[0])
            return false;

        $groups = g()->auth->getUserGroups();

        if(!empty($groups[USER_TYPE_MOD]))
            return true;

        $id = g()->auth->id();

        if($id === false)
            return false;

        $model = g('User', 'model');
        $model->filter(array('id' => $id, 'login' => $login));
        $ok = $model->getCount();

        if($ok)
            return true;

        return false;
    }

    public function hasAccessToDelete(array $params = array())
    {
        return $this->hasAccessToEdit($params);
    }
}
