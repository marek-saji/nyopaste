<?php
g()->load('Pages', 'controller');

/**
 * Render robots.txt
 * @author m.augustynowicz
 */
class RobotsTxtController extends PagesController
{
    /**
     * default action: set view to text.
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        ignored
     * @return void
     */
    public function actionDefault(array $params)
    {
        g()->view = g('TextView');
    }
}

