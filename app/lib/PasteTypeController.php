<?php
/**
 *
 * @author m.augustynowicz
 */
abstract class PasteTypeController extends Component
{
    protected $_type = null;
    protected $_type_idx = 0;

    public $forms = array();


    public function __construct(array $args)
    {
        parent::__construct($args);

        $this->_model_name = 'PasteType'.ucfirst($this->_type);
    }


    // allow
    protected function _onAction($action, array & $params)
    {
        return true;
    }


    // dummy
    public function actionDefault(array $params)
    {
    }


    // dummy
    public function actionNew(array $params)
    {
    }


    public function getOne($id, array & $db_data)
    {
        $type = g($this->_model_name, 'model');
        $type_db_data = $type
                ->filter(array('id' => $id))
                ->setMargins(1)
                ->exec();

        foreach ($type_db_data as $name => & $value)
        {
            if (!isset($db_data[$name]))
            {
                $db_data[$name] = & $value;
            }
        }
        unset($value);
    }


    public function sync(PasteModel $paste, $action)
    {
        $f = g('Functions');
        $form_id = 'paste';
        //$inputs = & $this->forms[$form_id]['inputs'];
        $post_data = & $this->data[$form_id];


        $post_data['id'] = $paste->getData('id');

        $type = g($this->_model_name, 'model');
        $err = $type->sync($post_data, true, $action);

        return $err;
    }


    public function getType()
    {
        return $this->_type;
    }


    public function __toString()
    {
        return $this->_type;
    }


    public function getIdx()
    {
        return $this->_type_idx;
    }

}

