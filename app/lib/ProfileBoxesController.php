<?php
/**
 * Profile boxes
 * @author m.augustynowicz
 */
class ProfileBoxesController extends Component
{
    /**
     * @var array form definictions
     * @see Forms
     */
    public $forms = array(
    );

    /**
     * undocumented default action
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        ignored
     * @return void
     */
    public function actionDefault(array $params)
    {
        $parent = $this->getParent();

        if ($parent->getName() === 'User')
        {
            $user = $parent->getUser();
            $boxes = g('Boxes', 'model')
                ->orderBy('id')
                ->filter(array(
                    'user_id' => $user['id']
                ))
                ->exec()
            ;

            $this->_fillBoxes($boxes);
        }
        else
        {
            $boxes = array();
        }

        $this->assignByRef('boxes', $boxes);


        // actions

        $actions = array();

        if ($this->hasAccess('new'))
        {
            //$actions['new'] = true;
            $actions['new'] = array(true, $this->url2c('UnderConstruction'));
        }

        $this->assignByRef('actions', $actions);
    }

    /**
     * Create new box
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        ignored
     * @return void
     */
    public function actionNew(array $params)
    {
        $user_ident = $this->getParentParam('User', 'default', 0);
    }


    /**
     * Check access to "new" action
     * @author m.augustynowicz
     *
     * @param array $params same that will be passed to action
     *        ignored
     */
    public function hasAccessToNew(array &$params)
    {
        $user = $this->getParent()->getUser();
        return g()->auth->id() == @$user['id'];
    }

    /**
     * Check access to "edit" action
     * @author m.augustynowicz
     *
     * @param array $params same that will be passed to action
     *        ignored
     */
    public function hasAccessToEdit(array &$params)
    {
        // TODO implement this properly
        return $this->hasAccessToNew($params);
    }


    /**
     * Add pastes to boxes
     * @author m.augustynowicz
     *
     * @param array $boxes reference to array returned by `BoxesModel::exec()`
     *
     * @return void operates on `$boxes` reference
     */
    public function _fillBoxes(array & $boxes)
    {
        $model = g('Paste', 'model')
            ->whiteList(array(
                'id',
                'url',
                'version',
                'title',
                'paster',
                'paster_id',
                'creation'
            ))
            ->distinct(array('root_id'))
            ->orderBy('root_id',  'DESC')
            ->orderBy('creation', 'DESC')
            ->setMargins(0, 5)
        ;

        $can_edit = $this->hasAccess('edit');

        foreach ($boxes as & $box)
        {
            $box['Pastes'] = $model
                ->filter(array(
                    'paster_id' => $box['user_id']
                ))
                ->exec()
            ;

            $box['Actions'] = array();

            if ($can_edit)
            {
                $box['Actions']['edit'] = array(true, $this->url2c('UnderConstruction'));
            }

            $box['Actions']['all'] = array(true, $this->url2c('UnderConstruction'));
        }
    }
}
