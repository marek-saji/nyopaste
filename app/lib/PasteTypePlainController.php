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
                'do_wrap_at' => array('fields' => null, '_tpl'=>'Forms/FBool'),
                'wrap_at',
                'wrap_words',
            ) // forms[paste][inputs]
        ) // forms[paste]
    ); // forms


    /**
     * Right before saving to database, unset [wrap_at],
     * if checkbox was not checked.
     * @author m.augustynowicz
     *
     * @param PasteModel $paste
     * @param string $action
     */
    public function sync(PasteModel $paste, $action)
    {
        $form_id = 'paste';
        $post_data =& $this->data[$form_id];

        if (false === g('Functions')->anyToBool($post_data['do_wrap_at']))
        {
            unset($post_data['wrap_at']);
        }

        return parent::sync($paste, $action);
    }

}

