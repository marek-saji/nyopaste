<?php
/**
 * Field for specyfing users.
 *
 * Note that it will not be represented in the database,
 * so you have to save it's value yourself.
 */
class UsersField extends FMultilineString
{
    protected $_logins = array();

    public function invalid(&$value)
    {
        $err = parent::invalid($value);

        $users_logins = preg_split('/[\s,]+/m', $value);
        $rows = g('User', 'model')
            ->filter(array('login' => $users_logins))
            ->whiteList(array('id', 'login'))
            ->exec('login')
        ;


        // convert to id=>login keeping order from $value

        $users = array();
        foreach ($users_logins as $login)
        {
            if (array_key_exists($login, $rows))
            {
                $row =& $rows[$login];
                $users[$row['id']] = $row['login'];
            }
        }


        $this->_logins =& $users;

        return $this->_errors($err, $value);
    }

    public function getLogins()
    {
        return $this->_logins;
    }

    public function isWriteable()
    {
        return false;
    }
}

