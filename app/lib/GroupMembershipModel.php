<?php
g()->load('DataSets', null);

/**
 * Group membership. Connecting User and Group models
 * @author m.augustynowicz
 */
class GroupMembershipModel extends Model
{
    /**
     * Add fields, relations and set primary keys
     * @author m.augustynowicz
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // fields

        $this->_addField(new FForeignId('user_id', true, 'User'));
        $this->relate('User', 'User', 'Nto1');

        $this->_addField(new FForeignId('group_id', true, 'Group'));
        $this->relate('Group', 'Group', 'Nto1');

        $this->_addField(new FTimestamp('creation', true))
            ->auto(array($this, 'autoValCreation'))
        ;


        $this->_pk('user_id', 'group_id');

        $this->whiteListAll();
    }


    /**
     * Check whether user is a group member
     * @author m.augustynowicz
     *
     * @param int $group_id
     * @param int $user_id
     *
     * @return bool
     */
    static public function isMember($group_id, $user_id)
    {
        if ( ! $user_id || ! $group_id )
        {
            return false;
        }


        $membership = new self;
        $count = $membership
            ->filter(array(
                'group_id' => $group_id,
                'user_id'  => $user_id
            ))
            ->getCount()
        ;

        return $count > 0;
    }


    /**
     * Invite a user to become member of closed group
     * @author m.augustynowicz
     *
     * @param int $user_id
     * @param int $group_id
     *
     * @return bool success of a operation
     */
    static public function invite($user_id, $group_id)
    {
        $new_data = array(
            'user_id'  => $user_id,
            'group_id' => $group_id
        );

        $membership = new self();

        $result = $membership->sync($new_data, true, 'insert');

        return (true === $result);
    }


    /**
     * Auto value for `creation` field
     * @author m.augustynowicz
     *
     * @param string $action database action name: insert, update
     * @param IField $field
     * @param mixed $old_value previously selected value
     *
     * @return null|mixed returning null leaves current value
     */
    public function autoValCreation($action, IField $field, $old_value)
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

}

