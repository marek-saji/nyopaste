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
     * Register this file's path to list it's methods.
     * @author m.augustynowicz
     */
    public function __construct($params)
    {
        $this->_files[] = __FILE__;

        parent::__construct($params);
    }

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

}
