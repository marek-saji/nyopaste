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
                'syntax' => array(
                    '_tpl' => 'Forms/FSelect'
                ),
                'line_numbers',
                'colour_scheme' => array(
                    '_tpl' => 'Forms/FSelect'
                ),
            ) // forms[paste][inputs]
        ) // forms[paste]
    ); // forms


    /**
     * Add new paste.
     *
     * Assign template variables
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        ignored
     * @return void
     */
    public function actionNew(array $params)
    {
        $conf = &g()->conf['paste_types']['source'];

        $modes = array();
        foreach ($conf['modes'] as $key => &$mode)
        {
            $modes[$key] = $mode['name'];
        }
        unset($mode);
        $this->assignByRef('syntax_values', $modes);

        $this->assignByRef('colour_scheme_values', $conf['themes']);

        $this->data['paste'] =
            (isset($this->data['paste']) ? (array)$this->data['paste'] : array())
            +
            array(
                'syntax' => $conf['default mode'],
                'colour_scheme' => $conf['default theme']
            );
    }

}

