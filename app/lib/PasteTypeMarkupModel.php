<?php
g()->load('PasteType', 'model');

/**
 * Paste with plain text
 *
 * Common attributes for all the types.
 * @author m.augustynowicz
 */
class PasteTypeMarkupModel extends PasteTypeModel
{
    public function __construct()
    {
        parent::__construct();


        // fields

        $this->_addField(new FForeignId('id'));

        $this->_addField(new FString('parser', true));


        // relations

        //$this->relate('Paste', 'Paste', '1to1');

        $this->_pk('id');
        $this->whiteListAll();
    }


	/**
     * Parser should be declared in {@see conf[parsers]}
	 * @author m.augustynowicz
	 *
	 * @param string $value user-submitted value
	 * 
	 * @return array errors (see blockcomment for Component for more details)
	 */
	public function _validateParser(& $value)
	{
        $errors = array();

        if ($value && !isset(g()->conf['parsers'][$value]))
        {
            $errors['no such parser'] = 'No such parser';
            $errors['stop_validation'] = true;
        }
        
        return $errors;
	}
}

