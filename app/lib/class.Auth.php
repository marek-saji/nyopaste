<?php
/**
 * THIS SHOULD BE ONLY TEMPORARY SOLUTION.
 * THINK OF BETTER AUTH IMPLEMENTATION AND PUT IN IN HG REPO
 */

if(!defined('INCORRECT_LOGIN_COUNT'))
    define('INCORRECT_LOGIN_COUNT', NULL);

//user types conf
if(!defined('USER_TYPE_UNAUTHORIZED'))
    define('USER_TYPE_UNAUTHORIZED', 0);
if(!defined('USER_TYPE_AUTHORIZED'))
    define('USER_TYPE_AUTHORIZED', 1);
if(!defined('USER_TYPE_STUDENT'))
    define('USER_TYPE_STUDENT', 2);
if(!defined('USER_TYPE_COMPANY'))
    define('USER_TYPE_COMPANY', 3);
if(!defined('USER_TYPE_MOD_STUDENT'))
    define('USER_TYPE_MOD_STUDENT', -2);
if(!defined('USER_TYPE_MOD_COMPANY'))
    define('USER_TYPE_MOD_COMPANY', -3);
if(!defined('USER_TYPE_MOD'))
    define('USER_TYPE_MOD', -1);
if(!defined('USER_TYPE_ADMIN'))
    define('USER_TYPE_ADMIN', -100);

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
        $this->_loadACL();
        $this->getUserGroups();
		
		if(isset($_COOKIE['remember_me']))
		{
			if(get_magic_quotes_gpc())
				$cookie_data = stripslashes($_COOKIE['remember_me']);
			else
				$cookie_data = $_COOKIE['remember_me'];
				
			$user_cookie_data = json_decode($cookie_data, true);

			$this->login($user_cookie_data);
		}
    }

    /*
     *  (this method is template safe)
     */
    public function loggedIn()
    {
        return false !== $this->id();
    }

    /**
     * Returns loggen-in user's display name
     *
     * @return boolean|integer user's display name
     *          false, when user is not logged-in,
     */
    public function displayName()
    {
        $display_name_field = g()->conf['users']['display_name_field'];
        $display_name = @$this->_session['user'][$display_name_field];
        return $display_name ? $display_name : false;
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
        $id = @$this->_session['user']['id'];
        return $id ? $id : false;
    }

    public function login(array $auth_data)
    {
        extract($auth_data); // be careful with that!
        $this->__user = array();
        $this->__error = '';
        $check_password = isset($force_data) ? (!$force_data) : true;

        if(!isset($pass))
            $pass = isset($passwd) ? $passwd : (isset($password) ? $password : (isset($md5_passwd) ? $md5_passwd : null));

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

        if(isset(g()->conf['login_fields']))
            $login_fields = g()->conf['login_fields'];
        else
            $login_fields = array(
                'login',
            );

        if(count($login_fields) <= 1)
            $user_model->filter(array(
                $login_fields[0] => $login
            ));
        else
        {
            $sql_fields = array();

            foreach($login_fields as $login_field)
                $sql_fields[] = "{$login_field} = " . $user_model->getField($login_field)->dbString($login);

            $user_model->filter(join(' OR ', $sql_fields))->order('id', 'DESC');
        }

        $user_model->whiteList(array(
            'id',
            'login',
            'passwd',
            'last_correct_login',
            'incorrect_login_count',
            'status',
        	'type',
        ));
        $user_model->setMargins(1);
        $user = $user_model->exec();

        if(!$user)
        {
            $this->__error = 'no_such_user';
            return false;
        }

        if ($user['status'] == STATUS_WAITING_FOR_ACTIVATION)
        {
            $this->__error = 'account_not_activated';
            return false;
        }
        elseif ($user['status'] == STATUS_BLOCKED)
        {
            $this->__error = 'account_blocked';
            return false;
        }
        elseif ($user['status'] & STATUS_DELETED)
        {
            $this->__error = 'account_deleted';
            return false;
        }
		
		$passwd_match = isset($md5_passwd) ? $user['passwd'] == $pass : $user['passwd'] == md5($pass);
		
        if($check_password && !$passwd_match)
        {
            $this->__error = 'bad_password';

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
            'last_correct_login' => 'now',
            'incorrect_login_count' => 0,
            'activation_hash' => '',
        );
        $user_model->sync($data, true);
        $this->_session['user'] = $user;
        return true;
    }

    public function getLastError()
    {
        return $this->__error;
    }

    public function logout()
    {
        $this->_session['user'] = array();
		
		if(isset($_COOKIE['remember_me']))
		{
			setcookie('remember_me', '', time() - 1, g()->req->getBaseUri());	// delete cookie
		}
    }

    public function hasAccess($ctrl, $action = null, $target = null, $user = null)
    {
        static $cache = array();

        if('' === $action)
            $action = 'default';

        if($action)
            $action = ucfirst($action);

        /** @todo */
        if(null !== $user)
            throw new HgException('Checking access for not logged-in user is not implemented.');

        if(null === $target)
            $target = array();

        if(empty($ctrl) || (is_array($ctrl) && empty($ctrl['url']) && empty($ctrl['box'])))
        {
            $lib_path = g()->first_controller->displayingCtrl()->getParent()->path();
            $ctrl = array(
                'url' => g()->conf['controllers'][$lib_path]['default']
            );
        }
        $this->_loadACL();
        // get whole box and url paths
        if(is_object($ctrl))
        {
            $keys_sources = array(
                'box' => false, 
                'url' => $ctrl->url(),
            );
            // get box path only from BoxedControllers
            if(class_exists('BoxedController', false))
                if($ctrl instanceof BoxedController)
                    $keys_sources['box'] = $ctrl->boxPath();
        }
        else
        {
            $keys_sources = array(
                'box' => @$ctrl['box'],
                'url' => @$ctrl['url'],
            );
        }

        // ignore empty values
        if(empty($keys_sources['box']))
            unset($keys_sources['box']);

        if(empty($keys_sources['url']))
            unset($keys_sources['url']);

        $cache_key = json_encode($keys_sources) . json_encode($action) . json_encode($target) . json_encode($user);
        $this_cache = &$cache[$cache_key];

        if(null !== $this_cache)
            return $this_cache;

        $groups = $this->getUserGroups($user);
        $groups['*'] = '*'; // dummy group for matching

        if(!in_array('unauthorized', $groups))
            $groups['authorized'] = 'authorized'; // dummy group for matching

        /*
        $sys_groups = array_flip(g()->conf['groups']);
        foreach ($groups as $id => &$group)
        {
            if (isset($sys_groups[$id]))
                $group = $sys_groups[$id];
        }
        unset($group);
         */

        // we want to ultimately check permission to the very root.
        $keys = array(
            'url: ' => false
        );
        // keys consists of key name (type of
        // path and the path itself) and
        // action name to check
        // build parent paths
        foreach($keys_sources as $type => $path)
        {
            $path_a = explode('/', $path);
            $last_in_path = end($path_a);
            do
            {
                // actions are checked only for exact matches (ergo false)
                if ('box' == $type)
                    $keys["$type: ".end($path_a)] = false;
                $keys["$type: " . join('/', $path_a)] = false;
                array_pop($path_a);
            }
            while($path_a);
            // exact match. save action name.
            if ('box' == $type)
                $keys["$type: $last_in_path"] = (string)$action;
            $keys["$type: $path"] = (string)$action;
        }
        $keys = array_reverse($keys);
        /*
        if (g()->debug->on())
            var_dump($keys);
         */
        // at this point keys contains all pathes that should be checked
        // first match found will return true
        // fals is returned if no matches were found
        $ret = false;
        foreach($keys as $key => $action) // warning: overriding $action
        {
            if ($action)
                $action = ucfirst($action);
            if(!isset($this->_acl[$key]))
                continue;
            $acl = &$this->_acl[$key];
            // check permission to whole controller
            foreach($groups as $group)
            {
                if(@$acl['groups'][$group])
                {
                    $ret = true;
                    if (g()->debug->on())
                        var_dump("Got access at key $key, $group");
                    break (2);
                }
            }
            // permission to actions
            if (''===$action)
                $action = 'Default';
            if($action && isset($acl['actions']))
            {
                if(!isset($acl['actions'][$action]))
                    continue;
                $acl_action = & $acl['actions'][$action];
                // each parameters set
                foreach($acl_action as $acl_params)
                {
                    foreach($groups as $group)
                    {
                        if(isset($acl_params['groups'][$group]))
                            if($this->_matchParams($acl_params['groups'][$group], $target))
                            {
                                $ret = true;
                                if (g()->debug->on())
                                    var_dump("Got access at $key / $action:".($acl_params)." with group $group");
                                break (2);
                            }
                    }
                }
            }
        }
        $this_cache = $ret;
        return $ret;
    }

    public function get($field)
    {
        $value = @$this->_session['user'][$field];
        return $value ? $value : false;       
    }

    protected function _loadACL()
    {
        if(null !== $this->_acl)
            return true;
        $acl = $this->_acl = array();
        $dir = APP_DIR . 'conf/';
        $xml = sprintf('%s%s.xml', $dir, 'acl');
        $xml = file_get_contents($xml);
        try
        {
            $xml = new SimpleXMLElement($xml);
        }
        catch(Exception $e)
        {
            if(g()->debug->allowed())
                trigger_error($e->getMessage(), E_USER_WARNING);
            return;
        }
        foreach($xml->ctrl as $ctrl)
        {
            $url = (string)$ctrl['url'];
            $box = (string)$ctrl['box'];
            if($url)
                $key = "url: $url";
            else 
                if($box)
                    $key = "box: $box";
                else
                    $key = "url: ";
                //echo '<hr />'; var_dump($url);
            foreach($ctrl->group as $group)
                $acl[$key]['groups'][(string)$group] = true;
                /*
            foreach ($ctrl->user as $user)
                $acl[$url]['users'][(string)$user] = true;
            */
            foreach($ctrl->action as $action)
            {
                $act = ucfirst((string)$action['name']);
                if(empty($act))
                    $act = '*';
                $params = (string)@$action['params'];
                $params_a = g()->req->decodeParams($params);
                //var_dump($act, $params);
                foreach($action->group as $group)
                    $acl[$key]['actions'][$act][$params]['groups'][(string)$group] = $params_a;
                /*
                foreach ($action->user as $user)
                    $acl[$key]['actions'][$act][$params]['users'][(string)$user] = $params_a;
                 */
            }
        }
        $this->_acl = $acl;
    }

    /**
     * (this method is template safe only for $user=null)
     * WARNING: currently works only for logged in user!
     * @param null|boolean|array $user when true passed, will fill the cache
     */
    public function getUserGroups($user = null)
    {
        static $cache = array();

        $ret = array();

        if(null === $user)
        {
            if(!$this->loggedIn())
                return array(USER_TYPE_UNAUTHORIZED => 'unauthorized');
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
                ->filter(array('id' => $user_id, 'status' => USER_STATUS_ACTIVE))
                ->whiteList(array('type', 'id'))
                ->setMargins(1)
                ->exec();
        }

        if(empty($user_data))
        	return array();

        $user_id = &$user_data['id'];

        switch($user_data['type'])
        {
            case USER_TYPE_ADMIN:
                $ret[USER_TYPE_ADMIN] = "admins";
            case USER_TYPE_MOD_STUDENT:
                $ret[USER_TYPE_MOD] = "mods";
            case USER_TYPE_AUTHORIZED:
                $ret[USER_TYPE_AUTHORIZED] = "authorized";
                break;
            default:
                $ret[USER_TYPE_UNAUTHORIZED] = "unauthorized";
            break;
        }

        $cache[$user_id] = &$ret;
        return $ret;
    }

    /**
     * (this method is template safe only for $user=null)
     */
    public function isUserInGroup($group, $user = null)
    {
        if(is_int($group))
            return array_key_exists($group, $this->getUserGroups($user));
        else
            return in_array($group, $this->getUserGroups($user));
    }

    /**
     */
    protected function _matchParams(array $acl_params, array $url_params)
    {
        foreach($acl_params as $name => $value)
        {
            if('' === $value)
            {
                if(isset($url_params[$name]))
                    return false;
            }
            else
            {
                if(isset($url_params[$name]) && $url_params[$name] != $value)
                    return false;
            }
        }
        return true;
    }
}
