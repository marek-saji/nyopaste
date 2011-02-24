<?php
g()->load('PasteTypeSource', 'controller');
/**
 * Paste with source code
 * @author m.augustynowicz
 */
class PasteTypeSourceController extends PasteTypePlainController
{
    protected $_type = 'source';
    protected $_type_idx = 1;

    public $forms = array(
        'paste' => array(
            'model' => 'PasteTypeSource',
            'inputs' => array(
                'syntax',
                'line_numbers',
                'colour_scheme'
            ) // forms[paste][inputs]
        ) // forms[paste]
    ); // forms


}

