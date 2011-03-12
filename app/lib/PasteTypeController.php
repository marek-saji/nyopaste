<?php
/**
 *
 * @author m.augustynowicz
 */
abstract class PasteTypeController extends Component
{
    protected $_type = null;

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
        $url = $this->getParentParam('Paste', 'default', 0);
        $ver = $this->getParentParam('Paste', 'default', 'v');

        $db_data = array();
        $this->getParent()->getOne($url, $ver, true, $db_data);
        $this->assignByRef('row', $db_data);
    }


    // dummy
    public function actionNew(array $params)
    {
        $conf = &g()->conf['paste_types'][$this->_type];

        if (empty($this->data['paste']))
        {
            $url = $this->getParentParam('Paste', 'new', 0);
            $ver = $this->getParentParam('Paste', 'new', 'v');
            if ($url)
            {
                $this->getParent()->getOne($url, $ver, true, $this->data['paste']);
            }
        }

        $this->data['paste'] =
            ((array) @$this->data['paste'])
            +
            ((array) @$conf['defaults'])
        ;
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

}

