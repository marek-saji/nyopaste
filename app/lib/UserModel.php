<?php
g()->load('DataSets', null);

/**
 * User
 */
class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct();

        static $login_len_msg  = 'Login have to be 2-32 characters long';
        static $passwd_len_msg = 'Password have to be 3-128 characters long';

        $this->_addField(new FId('id'));
        $this->_addField(new FString('login', true, null, 2, 32)) // UNIQUE
            ->mess(array(
                'minlength' => $login_len_msg,
                'maxlength' => $login_len_msg
            ));
        $this->_addField(new FEmail('email')); // UNIQUE or NULL
        $this->_addField(new FPassword('passwd', 3, 128))
            ->mess(array(
                'minlength' => $passwd_len_msg,
                'maxlength' => $passwd_len_msg
            ));

        // for user's statuses (STATUS_*) see main conf
        $this->_addField(new FInt('status', 2, true,
                                  (string) STATUS_ACTIVE));
        // for user's types (USER_TYPE_*) see conf.users
        $this->_addField(new FInt('type', 4, false,
                                  (string) USER_TYPE_AUTHORIZED));

        $this->_addField(new FTimestamp('creation'))
            ->auto(array($this, 'autoValCreated'))
        ;


        $this->_addField(new FTimestamp('last_edit'));

        // metadata
        $this->_addField(new FHTTP('website', false, null, null, 1024));
        $this->_addField(new FMultilineString('about_me', false, null, 0, 1048576));

        $this->_addField(new FTimestamp('last_correct_login', true, '1970-01-01 00:00:00'));
        $this->_addField(new FInt('incorrect_login_count'));

        $this->_addField(new FMD5String('passwd_reset_hash'));
        $this->_addField(new FTimestamp('passwd_reset_hash_creation'));

        $this->_pk('id');
        $this->whiteListAll();
    }



    /**
     * Auto value for `created` field
     * @author m.augustynowicz
     *
     * @param string $action action type: insert, update
     * @param IField $field
     * @param mixed $value current value (will be replaced)
     *
     * @return mixed|null new value, if different than null
     */
    public function autoValCreated($action, $field, $value)
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
     * Exec query, get rows.
     *
     * In addition to normal behaviour add [DisplayName] and [Ident] values.
     * @author m.augustynowicz
     *
     * @param string $key change keys of returned array to values of this field
     * @param bool $return do return rows
     *
     * @return array|void rows or nothing
     */
    public function exec($key=null, $return=true)
    {
        $result = parent::exec($key, $return);

        if ($return)
        {
            $conf = & g()->conf['users'];
            $aliases = array(
                'DisplayName' => $conf['display_name_field'],
                'Ident'       => $conf['ident_field']
            );
            foreach ($this->_array as &$row)
            {
                foreach ($aliases as $alias => $field_name)
                {
                    $row[$alias] =& $row[$field_name];
                }
            }

            if ($this->_limit==1 && !empty($this->_array))
            {
                return ($this->_array[0]);
            }
            else
            {
                return ($this->_array);
            }
        }
    }
}

