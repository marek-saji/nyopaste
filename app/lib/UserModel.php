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
                'min_length' => $login_len_msg,
                'max_length' => $login_len_msg
            ));
        $this->_addField(new FEmail('email', true)); // UNIQUE
        $this->_addField(new FPassword('passwd', 3, 128))
            ->mess(array(
                'min_length' => $passwd_len_msg,
                'max_length' => $passwd_len_msg
            ));

        // for user's statuses (STATUS_*) see main conf
        $this->_addField(new FInt('status', 2, true,
                                  (string) STATUS_ACTIVE));
        // for user's types (USER_TYPE_*) see conf.users
        $this->_addField(new FInt('type', 4, false,
                                  (string) USER_TYPE_AUTHORIZED));

        $this->_addField(new FTimestamp('last_edit'));

        // metadata
        $this->_addField(new FHTTP('website', false, null, null, 1024));
        $this->_addField(new FMultilineString('about_me', false, null, 0, 1048576));

        $this->_addField(new FTimestamp('last_correct_login', true, '0'));
        $this->_addField(new FInt('incorrect_login_count'));

        $this->_pk('id');
        $this->whiteListAll();
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

