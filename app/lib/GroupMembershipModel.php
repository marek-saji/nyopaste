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
     * Get list of groups that user is member of
     * @author m.augustynowicz
     *
     * @param id $user_id
     *
     * @return array e.g. `array(group_id => group name, ...)`
     */
    static public function getUserGroups($user_id)
    {
        $ds = g('GroupMembership', 'model')->rel('Group');
        $ds->whiteList(array(
            'group_id',
            'name'
        ));
        $ds->orderBy('Group.creation');
        $ds->filter(array('user_id' => $user_id));

        $rows = $ds->exec();
        $flat = array();
        foreach ($rows as & $row)
        {
            $flat[$row['group_id']] = $row['name'];
        }
        unset($row);

        return $flat;
    }


    /**
     * Add user to a group
     * @author m.augustynowicz
     *
     * @param int $user_id
     * @param int $group_id
     *
     * @return bool success of a operation
     */
    static public function addUser($user_id, $group_id)
    {
        g()->db->startTrans();
        do
        {
            $new_data = array(
                'user_id'  => $user_id,
                'group_id' => $group_id
            );

            $membership = new self();
            $result = $membership->sync($new_data, true, 'insert');
            if (true !== $result)
            {
                break;
            }

            $paste_class = g()->load('Paste', 'model');
            $result = $paste_class::updateGroupsTsv($new_data['user_id']);
            if (true !== $result)
            {
                break;
            }

            g()->db->completeTrans();

            return true;
        }
        while (false);
        g()->db->failTrans();
        g()->db->completeTrans();

        return false;
    }


    /**
     * Remove user from a group
     * @author m.augustynowicz
     *
     * @param int $user_id
     * @param int $group_id
     *
     * @return bool success of a operation
     */
    static public function removeUser($user_id, $group_id)
    {
        g()->db->startTrans();
        do
        {
            $old_data = array(
                'user_id'  => $user_id,
                'group_id' => $group_id
            );

            $membership = new self();
            $membership->filter($old_data)->delete(true);
            if (g()->db->lastErrorMsg())
            {
                break;
            }

            $paste_class = g()->load('Paste', 'model');
            $result = $paste_class::updateGroupsTsv($old_data['user_id']);
            if (true !== $result)
            {
                break;
            }

            g()->db->completeTrans();

            return true;
        }
        while (false);
        g()->db->failTrans();
        g()->db->completeTrans();

        return false;
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

