<?php
g()->load('DataSets', null);

/**
 * Paste tag.
 * @author m.augustynowicz
 */
class PasteTagModel extends Model
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

        $this->_addField(new FForeignId('paste_id', true, 'Paste'));
        $this->_addField(new FString('tag', true));


        // relations

        $this->relate('Paste', 'Paste', 'Nto1');


        $this->_pk('paste_id', 'tag');

        $this->whiteListAll();
    }
}

