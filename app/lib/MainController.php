<?php
/**
 * Main page.
 *
 * @author m.augustynowicz created empty class
 */
 
g()->load('Pages', 'controller');

class MainController extends PagesController
{
    public function actionDefault(array $params)
    {
    }

    public function hasAccessToDefault(array & $params)
    {
        return true;
    }
}

