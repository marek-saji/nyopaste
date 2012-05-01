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
