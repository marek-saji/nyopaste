<?php
g()->load('PasteType', 'controller');
/**
 *
 * @author m.augustynowicz
 */
class PasteTypePlainController extends PasteTypeController
{
    protected $_type = 'plain';
    protected $_type_idx = 3;

    public $forms = array(
        'paste' => array(
            'model' => 'PasteTypePlain',
            'inputs' => array(
                'line_numbers',
                'wrap_at',
                'wrap_words',
            ) // forms[paste][inputs]
        ) // forms[paste]
    ); // forms


}

