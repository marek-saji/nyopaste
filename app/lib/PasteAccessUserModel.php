<?php
g()->load('DataSets', null);

/**
 * Access to pastes for users
 * @author m.augustynowicz
 */
class PasteAccessUserModel extends Model
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

        $this->_addField(new FForeignId('paste_root_id', true, 'Paste'));
        $this->_addField(new FForeignId('user_id', true, 'User'));


        // relations

        $this->relate('Paste', 'Paste', 'Nto1', 'paste_root_id', 'root_id');
        $this->relate('User',  'User',  'Nto1', 'user_id',       'id');


        $this->_pk('paste_root_id', 'user_id');

        $this->whiteListAll();
    }
}

