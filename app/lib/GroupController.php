<?php
g()->load('Pages', 'controller');

/**
 * User groups: searching, viewing, joining, creating...
 * @author m.augustynowicz
 */
class GroupController extends PagesController
{
    /**
     * @var array form definictions
     * @see Forms
     */
    public $forms = array(
        'form' => array(
            'model' => 'Group',
            'inputs' => array(
                'name',
                'description',
                'website',
                'open',
                'hidden'
            ),
        ), // form

        'confirm' => array(
            'model' => '',
            'inputs' => array(),
        ), // confirm

    );



    /**
     * Prepare "default" action.
     *
     * Add paginator.
     * @author m.augustynowicz
     */
    protected function _prepareActionDefault(array & $params)
    {
        if (empty($params[0]))
        {
            $this->addChild('Paginator', 'p');
        }
        else
        {
            $this->addChild('ProfileBoxes', 'Boxes')
                ->assign('owner_is_grou', true)
            ;

        }
    }

    /**
     * User group details
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        - [0] group id
     * @return void
     */
    public function actionDefault(array $params)
    {
        if (empty($params[0]))
        {
            return $this->_actionList($params);
        }
        else
        {
            return $this->_actionShow($params);
        }
    }



    /**
     * List latest pastes
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        ignored
     * @return void
     */
    protected function _actionList(array $params)
    {
        if ('User' === $this->getParent()->getName())
        {
            $user = $this->getParent()->getRow();
            $user_id = $user['id'];
        }
        else
        {
            $user_id = false;
        }


        if ($user_id)
        {
            $this->_setTemplate('list');
        }
        else
        {
            $this->_setTemplate('latest');
        }


        // assign groups (paginated)

        $group = g('Group', 'model');
        $filter = $group->getAccessibleFilter();
        if ( ! $user_id )
        {
            $model =& $group;
        }
        else
        {
            $membership = g('GroupMembership', 'model');
            $model = new Join(
                $membership,
                $group,
                new FoBinary($membership['group_id'], '=', $group['id'])
            );
            $white_list = $group->getFields();
            $model->whiteList($white_list);

            $filter->also(new FoBinary($membership['user_id'], '=', $user_id));
        }

        $model
            ->orderBy($group['creation'])
            ->filter($filter)
        ;

        $this->getChild('p')->setMargins($model);
        $rows = $model->exec();

        if ( ! $user_id )
        {
            foreach ($rows as & $row)
            {
                $group->enchance($row);
            }
            unset($row);
        }

        $this->assignByRef('rows', $rows);


        if ( ! $user_id )
        {
            // assign global group actions

            $actions = array();

            $actionNew_params = $params;
            if ($this->hasAccess('new', $actionNew_params))
            {
                $actions['new'] = array(
                    true,
                    $this->url2aInside('new', array('#' => 'content')),
                    'class' => 'modal'
                );
            }
            unset($actionNew_params);

            $this->assignByRef('actions', $actions);
        }
    }



    /**
     * Display group profile
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        [0] group id
     * @return void
     */
    protected function _actionShow(array $params)
    {
        $id = (int) $params[0];


        $f = g('Functions');


        $this->_setTemplate('profile');


        // assign row data

        $group = g('Group', 'model')
            ->orderBy('creation', 'DESC')
        ;
        $filter = $group->getAccessibleFilter()
            ->also(new FoBinary($group['id'], '=', $id))
        ;
        $row = $group->filter($filter)->getRow();

        $row['IsGroup'] = true;

        // meta-data (common names with User)
        $group->enchance($row);


        // assign group actions

        $actions = array();

        $actionEdit_params = $params;
        if ($this->hasAccess('edit', $actionEdit_params))
        {
            $actions['edit'] = array(
                true,
                $this->url2aInside('edit', array($id, '#' => 'content')),
                'class' => 'modal'
            );
        }
        unset($actionEdit_params);

        $row['Actions'] =& $actions;


        $this->assignByRef('row', $row);
    }



    /**
     * Create new group
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        ignored
     * @return void
     */
    public function actionNew(array $params)
    {
        return $this->_addOrEdit($params, false);
    }



    /**
     * Edit existing group
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        [0] group id
     * @return void
     */
    public function actionEdit(array $params)
    {
        return $this->_addOrEdit($params, true);
    }

    /**
     * Grant access for group leader
     * @author m.augustynowicz
     *
     * @param array $params request params
     *        [0] group id
     *
     * @return bool
     */
    protected function hasAccessToEdit(array & $params)
    {
        $count = g('Group', 'model')
            ->filter(array(
                'id'        => $params[0],
                'leader_id' => g()->auth->id(),
                'removed'   => null
            ))
            ->getCount()
        ;
        return $count > 0;
    }


    /**
     * Grant access 
     * @author m.augustynowicz
     *
     * @param array $params request params
     *        [0] group id
     *
     * @return bool
     */
    protected function hasAccessToDefault(array & $params)
    {
        $id = (int) @$params[0];

        if (empty($id))
        {
            return true;
        }

        $group = g('Group', 'model');
        $filter = $group->getAccessibleFilter()
            ->also(new FoBinary($group['id'], '=', $id))
        ;

        $count = $group->filter($filter)->getCount();

        return $count > 0;
    }



    /**
     * Common code for new and edit actions
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        [0] group id, when editing
     * @param bool $editing
     *
     * @return void
     */
    protected function _addOrEdit(array $params, $editing = false)
    {
        $id = (int) $params[0];


        if ($editing)
        {
            $row = g('Group', 'model')->getRow($id);
            if (empty($row))
            {
                return $this->redirect(array('HttpErrors', 'error404'));
            }
        }

        $this->_setTemplate('form');

        $form_ident = 'form';

        if (empty($this->data[$form_ident]))
        {
            if ($editing)
            {
                $this->data[$form_ident] = $row;
            }
        }
        else if ($this->_validated[$form_ident])
        {
            $model = g('Group', 'model');
            $new_data = $this->data[$form_ident];

            $backlink =& $this->data[$form_ident]['_backlink'];
            if (empty($backlink))
            {
                $backlink = $this->url2a('', $params);
            }

            if ($editing)
            {
                $db_action = 'update';
                $new_data['id'] = $id;
                $success_msg = 'Group updated';
            }
            else
            {
                $db_action = 'insert';
                $success_msg = 'Group created';
                $new_data = array(
                    'leader_id' => g()->auth->id()
                ) + $new_data;
            }


            g()->db->startTrans();

            $result = $model->sync($new_data, true, $db_action);

            if ( true === $result && ! $editing )
            {
                $membership = g('GroupMembership', 'model');
                $membership_new_data = array(
                    'user_id'  => $new_data['leader_id'],
                    'group_id' => $new_data['id']
                );
                $result = $membership->sync($membership_new_data, true, 'insert');
            }


            if (true !== $result)
            {
                g()->db->failTrans();
                g()->db->completeTrans();

                g()->debug->dump($result);
                g()->addInfo(
                    "db fail, group $db_action {$params[0]}",
                    'error',
                    $this->trans('((error:DS:%s))', false)
                );
            }
            else
            {
                g()->db->completeTrans();

                g()->addInfo(
                    "group $db_action {$params[0]}",
                    'info',
                    $this->trans($success_msg)
                );
                $this->redirect($backlink);
            }
        }

        $this->assignByRef('row', $row);
        $this->assignByRef('editing', $editing);

    }

}

