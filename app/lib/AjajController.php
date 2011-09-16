<?php
g()->load('Pages', 'controller');

/**
 * Asynchronouse Javascript And JSON controller.
 *
 * Basically, it returns JSON.
 * @author m.augustynowicz
 */
class AjajController extends PagesController
{
    /**
     * Set view to AJAX for all actions.
     * @author m.augustynowicz
     */
    protected function _onAction($action, array & $params)
    {
        g()->view = g('AjaxView');

        return true;
    }


    /**
     * Autocomplete suggestions for users' logins
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        ignored, data passed with POST
     * @return void
     */
    public function actionAutoCompleteUser(array $params)
    {
        $input = $_POST['input'];
        $model = g('User', 'model')
            ->whitelist(array('login'))
            ->filter(array(
                array('login', 'ILIKE', $input . '%')
            ))
            ->setMargins(5)
        ;
        $suggestion = $model->exec();
        array_walk($suggestion, function (& $row) {
            $row = $row['login'];
        });

        $this->assign('json', array(
            'sql' => $model->query(),
            'suggestions' => $suggestion
        ));
    }

}

