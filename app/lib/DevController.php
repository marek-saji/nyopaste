<?php
g()->load('Developer', 'controller');

/**
 * Developer controller
 * @author someone
 *
 */
class DevController extends DeveloperController
{
    /**
     * Used by parent
     */
    protected $_file = __FILE__;

    /**
     * Template action.
     * Copy and add the missig asterisk on the top of this comment.
     * @author someone
     * 
     * @param array $params accepts "die" parameter
     * @return void
     */
    public function actionTemplate(array $params)
    {
        $this->_devActionBegin($params, __FUNCTION__);
        // put your code here.
        $this->_devActionEnd($params, __FUNCTION__);
    }

    /**
     * Adds default stuff (like users etc)
     * @author m.augustynowicz
     *
     * @param array $params accepts "die" parameter
     */
    public function actionAddDefaults(array $params)
    {
        $this->_devActionBegin($params, __FUNCTION__);

        $this->_insertSomething(
            'User',
            array(
                array(
                    'id'     => -1,
                    'login'  => 'admin',
                    'passwd' => 'foo',
                    'type'   => USER_TYPE_ADMIN,
                    'status' => STATUS_ACTIVE,
                ),
            )
        );

        $this->_devActionEnd($params, __FUNCTION__);
    }

    /**
     * Insert some infopages
     *
     * No data validation, yay~
     * @author m.augustynowicz
     */
    protected function _insertSomething($model_name, array $data)
    {
        $model = g($model_name, 'model');
        foreach($data as &$row)
        {
            $model->filter($row);
            if(!$model->getCount())
            {
                if(true !== $model->sync($row, true, 'insert'))
                {
                    g()->addInfo(null, 'error', $this->trans('Error while adding ' . $model_name));
                    return;
                }
            }
        }
    }
}
