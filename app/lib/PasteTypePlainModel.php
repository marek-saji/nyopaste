<?php
g()->load('PasteType', 'model');

/**
 * Paste with plain text
 *
 * Common attributes for all the types.
 * @author m.augustynowicz
 */
class PasteTypePlainModel extends PasteTypeModel
{
    public function __construct()
    {
        parent::__construct();

        $this->_addField(new FForeignId('id'));
        //$this->relate('Paste', 'Paste', '1to1');

        $this->_addField(new FBool('line_numbers', true, true));
        $this->_addField(new FInt('wrap_at', 2, false, null, 0));
        $this->_addField(new FBool('wrap_words', true, true));

        $this->_pk('id');
        $this->whiteListAll();
    }

}

