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

        $this->_logins = array();

        if (trim($value) !== '')
        {
            $users_logins = preg_split('/[\s,]+/m', $value);

            $rows = g('User', 'model')
                ->filter(array('login' => $users_logins))
                ->whiteList(array('id', 'login'))
                ->exec('login')
            ;


            // convert to id=>login keeping order from $value

            $users = array();
            $not_users = array();
            foreach ($users_logins as $login)
            {
                if (array_key_exists($login, $rows))
                {
                    $row =& $rows[$login];
                    $users[$row['id']] = $row['login'];
                }
                else
                {
                    $not_users[] = $login;
                }
            }

            if (sizeof($not_users) > 0)
            {
                $not_users_html = '<ol><li>';
                $not_users_html .= implode('</li><li>', $not_users);
                $not_users_html .= '</li></ol>';
                $translator = g()->first_controller;
                $err['non-users'] = $translator->trans(
                    'Following are not %s user names: %s',
                    g()->conf['site_name'],
                    $not_users_html
                );
            }


            $this->_logins =& $users;
        }

        return $err;
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

