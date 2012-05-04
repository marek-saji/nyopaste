<?php
g()->load('DataSets', null);

/**
 * User group
 * @author m.augustynowicz
 */
class GroupModel extends Model
{

    /**
     * Parser used to produce [Description] meta field
     */
    protected static $_parser = null;


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

        $this->_addField(new FId('id'));
        $this->_addField(new FTimestamp('removed', false));
        $this->_addField(new FString('name', true));
        $this->_addField(new FMultilineString('description'));
        $this->_addField(new FHTTP('website', false, null, null, 1024));

        $this->_addField(new FForeignId('leader_id', true, 'User'));
        $this->relate('Leader', 'User', 'Nto1', 'leader_id', 'id');

        $this->_addField(new FBool('open', true, true));

        $this->_addField(new FBool('hidden', true, false));


        $this->_addField(new FTimestamp('creation', true))
            ->auto(array($this, 'autoValCreation'))
        ;


        // dummy fields
        $this->_addField(g('Users', 'field', array('members')));



        $this->_pk('id');

        $this->whiteListAll();
    }



    /**
     * Get filter for filtering groups accessible by a user
     * @author m.augustynowicz
     *
     * @param bool $include_removed
     *
     * @return FoBinaryChain set to AND
     */
    public function getAccessibleFilter($include_removed = false)
    {
        $user_id = g()->auth->id();


        $f_not_hidden  = new FoBinary($this['hidden'], '=', 'false');
        if ($user_id)
        {
            $f_is_leader = new FoBinary($this['leader_id'], '=', $user_id);
            $f_visible   = new FoBinaryChain(
                new FoBinaryChain($f_not_hidden, 'OR', $f_is_leader),
                'AND',
                'true'
            );
        }
        else
        {
            $f_visible = $f_not_hidden;
        }

        if ($include_removed)
        {
            $filter = $f_visible;
        }
        else
        {
            $f_not_removed = new FoBinary($this['removed'], 'IS NULL');
            $filter = new FoBinaryChain($f_not_removed, 'AND', $f_visible);
        }

        return $filter;
    }



    /**
     * Add additional, human-readable fields to a array fetched from db.
     * @author m.augustynowicz
     *
     * @param array $row
     *
     * @return void works on a reference
     */
    public function enchance(array & $row)
    {
        $f = g('Functions');

        $row['DisplayName'] = $this->trans('<em>%s</em> user group', $row['name']);

        $row['DisplayCreation'] = $f->formatDate($row['creation'], DATE_SHOW_DATE);

        if ($f->anyToBool($row['open']))
        {
            $row['Type'] = 'open';
        }
        else
        {
            $row['Type'] = 'open';
        }

        if ( ! self::$_parser )
        {
            self::$_parser = g('TextParser', 'class', array('parser'=>'markdown'));
        }
        $row['DisplayDescription'] = self::$_parser->parse($row['description']);

        $row['MembersCount'] = $this->fetchMembersCount($row['id']);
    }



    /**
     * Count members
     * @author m.augustynowicz
     *
     * @param int $group_id
     *
     * @return int
     */
    public function fetchMembersCount($group_id)
    {
        $count = g('GroupMembership', 'model')
            ->filter(array('group_id' => $group_id))
            ->getCount()
        ;

        return $count;
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

