<?php
g()->load('Pages', 'controller');

/**
 * undocumented page controller FooController
 * @author m.augustynowicz
 */
class FooController extends PagesController
{
    

    /**
     * undocumented default action
     * @author m.augustynowicz
     *
     * @param array $params request params
     *        
     * @return void
     */
    public function actionDefault(array $params)
    {
        /* code */
        echo 'O HAI! '.$this->getParentParam('User');
    }
}

