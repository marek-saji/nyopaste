<?php
g()->load('PasteType', 'model');

/**
 * Paste with source code
 * @author m.augustynowicz
 */
class PasteTypeSourceModel extends PasteTypeModel
{
    public function __construct()
    {
        parent::__construct();

        $this->_addField(new FForeignId('id'));
        //$this->relate('Paste', 'Paste', '1to1');

        $this->_addField(new FString('syntax'));
        $this->_addField(new FBool('line_numbers', true, true));
        $this->_addField(new FString('colour_scheme', true, 'ace-clouds'));

        $this->_pk('id');
        $this->whiteListAll();
    }

}

