<?php
g()->load('DataSets', null);

/**
 * User settings
 * @author m.augustynowicz
 */
class UserSettingModel extends Model
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
        $this->_addField(new FRich('class'));
        $this->_addField(new FRich('name', true));
        $this->_addField(new FRich('value', true));


        // relations

        $this->relate('User', 'User', 'Nto1');


        $this->_pk('user_id', 'class', 'name');

        $this->whiteListAll();
    }
}

