<?php
g()->load('PasteType', 'controller');
/**
 *
 * @author m.augustynowicz
 */
class PasteTypeMarkupController extends PasteTypeController
{
    protected $_type = 'markup';
    protected $_type_idx = 2;

    public $forms = array(
        'paste' => array(
            'model' => 'PasteTypeMarkup',
            'inputs' => array(
                'parser' => array(
                    '_tpl' => 'Forms/FSelect'
                ),
            ) // forms[paste][inputs]
        ) // forms[paste]
    ); // forms



    /**
     * Add new paste
     * @author m.augustynowicz
     *
     * @param array $params request params
     *        
     * @return void
     */
    public function actionNew(array $params)
    {
        $parsers = array_combine(
            array_keys(g()->conf['parsers']),
            array_keys(g()->conf['parsers'])
        );
        $this->assignByRef('parsers', $parsers);
    }
    



    public function getOne($id, array & $db_data)
    {
        parent::getOne($id, $db_data);


        if ($db_data['content'] && $db_data['parser'])
        {
            $db_data['ParsedContent'] = g('TextParser')->parse($db_data['parser'], $db_data['content']);
        }
    }

}

