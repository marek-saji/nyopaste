<?php
if(!defined('INCORRECT_LOGIN_COUNT'))
    define('INCORRECT_LOGIN_COUNT', NULL);

//user statuses conf
if(!defined('USER_STATUS_ACTIVE'))
    define('USER_STATUS_ACTIVE', 1);
if(!defined('USER_STATUS_NOT_ACTIVE'))
    define('USER_STATUS_NOT_ACTIVE', 0);
if(!defined('USER_STATUS_BLOCKED'))
    define('USER_STATUS_BLOCKED', -1);
if(!defined('USER_STATUS_DELETED_BY_OWNER'))
    define('USER_STATUS_DELETED_BY_OWNER', -2);
if(!defined('USER_STATUS_DELETED_BY_ADMIN'))
    define('USER_STATUS_DELETED_BY_ADMIN', -3);
if(!defined('USER_STATUS_NOT_VERIFIED'))
    define('USER_STATUS_NOT_VERIFIED', -4);

class Auth extends HgBase implements IAuth
{
    static $singleton = true;
    private $__user = array();
    private $__error = '';
    protected $_session = null;
    protected $_acl = null;

    public function __construct()
    {
        parent::__construct();
        $this->_session = &$_SESSION[g()->conf['SID']]['AUTH'];

        if($id = $this->id())
            $this->get($id);

        // fill the cache
        $this->getUserGroups();
		
        
        if(!$this->loggedIn())
            // FIXME stealing cookies is quite easy!
    		if(isset($_COOKIE['remember_me'])) 
    		{
                $this->login(
                        json_decode(
                                stripslashes($_COOKIE['remember_me']),
                                true
                             )
                        );
    		}
    }

    /**
     *  (this method is template safe)
     */
    public function loggedIn()
    {
        return false !== $this->id();
    }

    /**
     * Returns value of loggen-in user's id in the database.
     * @author m.augustynowicz
     *
     * @return mixed false, when user is not logged-in,
     *         key's value otherwise (usually it will be integer)
     */
    public function id()
    {
        return $this->get('id');
    }


    /**
     * Returns loggen-in user's ident
     *
     * @return boolean|string user's ident,
     *          false, when user is not logged-in
     */
    public function ident()
    {
        return $this->get(g()->conf['users']['ident_field']);
    }


    /**
     * Returns loggen-in user's display name
     *
     * @return boolean|integer user's display name,
     *          false, when user is not logged-in
     */
    public function displayName()
    {
        return $this->get(g()->conf['users']['display_name_field']);
    }


    /**
     * Authenticate
     */
    public function login(array $auth_data)
    {
        $login = @$auth_data['login'];
        $pass = isset($auth_data['passwd']) ? $auth_data['passwd']
              : (isset($auth_data['password']) ? $auth_data['password']
              : (isset($auth_data['md5_passwd']) ? $auth_data['md5_passwd']
              : null));;
        $check_password = @$auth_data['check_password'];
        $force_data = @$auth_data['force_data'];
        $as_admin = @$auth_data['as_admin'];
        $md5_passwd = isset($auth_data['md5_passwd']) ? $auth_data['md5_passwd'] : null;

        $this->__user = array();
        $this->__error = '';
        $check_password = isset($force_data) ? (!$force_data) : true;

        if(!$pass)
        {
            $this->__error = 'no_password_given';
            return false;
        }

        if((!@$login) || ($check_password && !@$pass))
        {
            $this->__error = 'empty_login_or_password';
            return false;
        }

        $user_model = g('User', 'model');

        if(isset(g()->conf['users']['login_fields']))
            $login_fields = g()->conf['users']['login_fields'];
        else
        {
            $login_fields = array(
                'login',
            );
        }

        if(count($login_fields) <= 1)
        {
            $user_model->filter(array(
                $login_fields[0] => $login,
                array('type', '!=', USER_TYPE_UNAUTHORIZED)
            ));
        }
        else
        {
            $sql_fields = array();

            foreach($login_fields as $login_field)
            {
                $field = $user_model[$login_field];
                $sql_fields[] = "{$field} = " . $field->dbString($login);
            }

            $user_model->filter(join(' OR ', $sql_fields))
                       ->order('id', 'DESC');
        }

        $user_model->whiteList(array(
            'id',
            'login',
            'passwd',
            'last_correct_login',
            'incorrect_login_count',
            'status',
        	'type',
            g()->conf['users']['display_name_field'],
            'email',
        ));
        $user_model->setMargins(1);
        $user = $user_model->exec();

        if(!$user)
        {
            $this->__error = 'bad_user_password';//no_such_user'; // don't let "them" know what do we have in db
            return false;
        }
        
        if($user['status'] == USER_STATUS_NOT_ACTIVE)
        {
            $this->__error = 'account_not_activated';
            return false;
        }
        elseif($user['status'] == USER_STATUS_BLOCKED)
        {
            $this->__error = 'account_blocked';
            return false;
        }
        elseif($user['status'] == USER_STATUS_DELETED_BY_ADMIN || $user['status'] == USER_STATUS_DELETED_BY_OWNER)
        {
            $this->__error = 'account_deleted';
            return false;
        }
		
		$passwd_match = ($md5_passwd!==null) ? $user['passwd'] == $pass : $user['passwd'] == FPassword::hash($pass);
		
        if($check_password && !$passwd_match)
        {
            $this->__error = 'bad_user_password';//bad_password'; // don't let "them" know what do we have in db

            if(NULL !== INCORRECT_LOGIN_COUNT)
            {
                $data = array(
                    'id' => $user['id'],
                    'incorrect_login_count' => $user['incorrect_login_count'] + 1,
                );
                $user_model->sync($data, true);
            }

            return false;
        }
        else
        {
            unset($user['passwd']);
        }

        if(NULL !== INCORRECT_LOGIN_COUNT)
        {
            if($user['incorrect_login_count'] > INCORRECT_LOGIN_COUNT)
            {
                $this->__error = 'incorrect_login_count_exceeded';
                return false;
            }
        }

        $data = array(
            'id' => $user['id'], // PK
            'last_correct_login' => 'NOW()',
            'incorrect_login_count' => 0,
            'activation_hash' => '',
        );
        $user_model->sync($data, true);

        if($this->loggedIn() && $this->isUserInGroup('mod') && !empty($as_admin))
            $user['as_admin'] = $as_admin;

        $this->_session['user'] = $user;
        return true;
    }

    /**
     */
    public function getLastError()
    {
        return $this->__error;
    }


    /**
     * Deauthenticate
     */
    public function logout()
    {
        $this->_session['user'] = array();
        unset($_SESSION['PastePasswords']);
		
		if(isset($_COOKIE['remember_me']))
		{
			setcookie('remember_me', '', time() - 1, g()->req->getBaseUri());	// delete cookie
		}
    }


    /**
     * Check wheter user has access to some action
     * 
     * WARNING: checking only for logged-in user implemented ($user=null)
     * NOTE: $target ignored
     * @see $conf[acl]
     */
    public function hasAccess($ctrl, $action = null, $target = null, $user = null)
    {
        // find default actions by 'default'
        if('' === $action)
            $action = 'default';

        // lcfirst() for <php-5.3
        if ($action)
            $action[0] = strtolower($action[0]);

        /** @todo */
        if(null !== $user)
            throw new HgException('Checking access for not logged-in user is not implemented.');

        // detect $ctrl format and obtain url (without action)
        switch (true)
        {
            // Component
            case is_object($ctrl) :
                if (!$ctrl instanceof Controller)
                {
                    throw new HgException('Unsupported $ctrl type passed: '.get_class($ctrl));
                }
                $url = $ctrl->url();
                break;
            // array with [url] (format used in MUS)
            case is_array($ctrl) :
                $url = $ctrl['url'];
                $url = $ctrl['url'];
                break;
            // string with url
            case is_string($ctrl) :
                $url = $ctrl;
                break;
            default :
                throw new HgException('Unsupported $ctrl type passed: '.gettype($ctrl));
        }
        $url = '/' . trim($url, '/');
        $exploded_url = explode('/', $url);
        $name = end($exploded_url);
        $full_url = $url . '/' . $action;

        // we could use isUserInGroup() every time, but this way will be faster
        $groups = $this->getUserGroups($user);

        // get possible matches from ACL
        $acl = array_intersect_key(
                g()->conf['acl'],
                array(
                    $name       => true,
                    $url        => true,
                    $full_url   => true,
                    '*'         => true,
                )
            );

        foreach ($acl as $key => $node)
        {
            if (is_bool($node))
            {
                // just access or deny (access)
                return $node;
            }
            else if (is_array($node))
            {
                // list of groups ('group'=>access)
                foreach ($node as $group => $acc)
                {
                    if (array_key_exists($group, $groups))
                    {
                        return $acc;
                    }
                }
            }
            else
            {
                throw new HgException('Incorrect ACL definition: '
                        . 'acl['.$key.']=' . var_export($node,true) );
            }
        }

        // deny by default
        return false;
    }


    /**
     * Get value of logged-in user's field
     * NOTE: only fields whitelisted in login() can be fetched
     * @author m.augustynowicz
     */
    public function get($field)
    {
        $value = @$this->_session['user'][$field];
        return $value ? $value : false;
    }


    /**
     * (this method is template safe only for $user=null)
     * @see $conf[enums][user_type] for available user types
     * @param null|boolean|array $user when true passed, will fill the cache
     */
    public function getUserGroups($user = null)
    {
        static $cache = array();

        $ret = array();

        if($user === null || $user === false)
        {
            if(!$this->loggedIn())
                return array('unauthorized' => 'unauthorized');
            $user_id = $this->id();
        }
        elseif(is_array($user))
        {
            $user_data = &$user;

            if(!isset($user_data['id']) || !isset($user_data['type']))
                throw new HgException('Invalid parameter passed: incomplete array');
        }

        elseif(g('Functions')->isInt($user))
            $user_id = $user;
        else
            throw new HgException('Invalid parameter passed: unknown type');

        if(isset($cache[$user_id]))
            return $cache[$user_id];

        if(!isset($user_data))
        {
            $user_data = g('User', 'model')
                    ->filter(array(
                        'id' => $user_id,
                    ))
                    ->whiteList(array(
                        'type',
                        'id',
                    ))
                    ->setMargins(1)
                    ->exec();
        }

        if(empty($user_data))
        	return array();

        $user_id = &$user_data['id'];

        $ret = array(
            'authorized' => 'authorized',
            $user_data['type'] => $user_data['type']
        );

        $cache[$user_id] = &$ret;
        return $ret;
    }

    /**
     * (this method is template safe only for $user=null)
     */
    public function isUserInGroup($group, $user = null)
    {
        return array_key_exists($group, $this->getUserGroups($user));
    }

}

