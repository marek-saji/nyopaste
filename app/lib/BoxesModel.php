<?php
g()->load('DataSets', null);

/**
 * Profile boxes model
 * @author m.augustynowicz
 */
class BoxesModel extends Model
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

        $this->_addField(new FId('id'));
        $this->_addField(new FString('title', false, '', 0, 256));
        $this->_addField(new FForeignId('user_id', true, 'User'));


        // relations

        $this->relate('User', 'User', 'Nto1');


        $this->_pk('id');

        $this->whiteListAll();
    }
}

