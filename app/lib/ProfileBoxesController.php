<?php
/**
 * Profile boxes
 * @author m.augustynowicz
 */
class ProfileBoxesController extends Component
{
    const DEFAULT_LIMIT = 5;


    /**
     * @var array form definictions
     * @see Forms
     */
    public $forms = array(
        'box' => array(
            'ajax'   => true,
            'model'  => 'Box',
            'inputs' => array(
                'title',
                'query',
                'limit',
                'list_paster'
            ),
        ),
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

        if ( is_a($parent, 'IProfileController'))
        {
            $profile = $parent->getRow();
            $boxes = g('Box', 'model')
                ->orderBy('order')
                ->orderBy('id', 'DESC') // in case of non-unique order value
                ->filter(array(
                    'profile_type' => $profile['ProfileType'],
                    'profile_id'   => $profile['id'],
                    'removed'      => null
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
            $actions['new'] = array(
                true,
                $this->url2aInside('new', array('#' => 'content')),
                'class' => 'modal'
            );
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
        return $this->_addOrEdit($params, false);
    }


    /**
     * Edit existing box
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        - [0] box id
     * @return void
     */
    public function actionEdit(array $params)
    {
        return $this->_addOrEdit($params, true);
    }


    /**
     * Move box up or down
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        - [0] box id
     *        - [1] direction: up or down
     * @return void
     */
    public function actionMove(array $params)
    {
        $backlink = g()->req->getReferer();

        $id = $params[0];
        $up = ($params[1] === 'up');

        $model = g('Box', 'model')
            ->whiteList(array('id', 'order'))
            ->orderBy('order')
            ->orderBy('id', 'DESC') // in case of non-unique order value
            ->setMargins(1)
        ;

        g()->db->startTrans();

        $boxes = array();
        $boxes[0] = $model->getRow($id);
        if (empty($boxes[0]))
        {
            $this->redirect($backlink);
        }
        $box_order = $boxes[0]['order'];
        $boxes[1] = $model
            ->orderBy('order', $up ? 'DESC' : 'ASC')
            ->getRow(array(
                array('order', $up ? '<' : '>', $box_order)
            ))
        ;
        if (empty($boxes[1]))
        {
            $this->redirect($backlink);
        }
        $boxes[0]['order'] = $boxes[1]['order'];
        $boxes[1]['order'] = $box_order;

        $model->sync($boxes, true, 'update');

        g()->db->completeTrans();

        $this->redirect($backlink);
    }


    /**
     * Set box as removed
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        - [0] box id
     * @return void
     */
    public function actionRemove(array $params)
    {
        return $this->_removeOrRestore(
            $params,
            // update "removed" column with
            time(),
            // success msg getter
            function ($that, $box) {
                $undo_link = $that->l2aInside(
                    $that->trans('Undo'),
                    'restore',
                    array($box['id'])
                );
                return $that->trans(
                    'Search list <em>%s</em> removed (%s).',
                    $box['title'],
                    $undo_link
                );
            }
        );
    }


    /**
     * Set box to not removed
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        - [0] box id
     * @return void
     */
    public function actionRestore(array $params)
    {
        return $this->_removeOrRestore(
            $params,
            // update "removed" column with
            null,
            // success msg getter
            function ($that, $box) {
                return $that->trans(
                    'Search list <em>%s</em> restored.',
                    $box['title']
                );
            }
        );
    }


    /**
     * Grant access to adding and removing to profile owner.
     * @author m.augustynowicz
     *
     * @param string $action
     * @param array $params same that will be passed to action
     *        ignored
     *
     * @return bool
     */
    public function hasAccess($action, array &$params = array(), $just_checking=true)
    {
        switch ($action)
        {
            case 'new' :
            case 'edit' :
            case 'remove' :
            case 'restore' :
            case 'move' :
                $user_id = g()->auth->id();
                $parent = $this->getParent();
                if (is_a($parent, 'IProfileController'))
                {
                    $profile = $parent->getRow();
                    if (is_a($parent, 'UserController'))
                    {
                        if ($user_id === @$profile['id'])
                        {
                            return true;
                        }
                    }
                    else if (is_a($parent, 'GroupController'))
                    {
                        if ($user_id === @$profile['leader_id'])
                        {
                            return true;
                        }
                    }
                    // else fall down
                }

            default :
                return parent::hasAccess($action, $params);
        }
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
        $can_move    = $this->hasAccess('move');
        $can_edit    = $this->hasAccess('edit');
        $can_remove  = $this->hasAccess('remove');
        $can_restore = $this->hasAccess('restore');

        $model_class = g()->load('Paste', 'model');

        $first_id = arr(reset($boxes) ?: array('id'=>null), 'id');
        $last_id  = arr(end($boxes)   ?: array('id'=>null), 'id');

        foreach ($boxes as & $box)
        {
            $box['Pastes'] = $model_class::getByQuery(
                $box['query'],
                array(
                    'limit' => $box['limit']
                ),
                $count
            );

            $box['Actions'] = array();

            if ($can_move)
            {
                if ($box['id'] !== $first_id)
                {
                    $box['Actions']['⇧'] = array(
                        true,
                        $this->url2aInside('move', array($box['id'], 'up')),
                        'title' => $this->trans('move up'),
                        'class' => 'char-icon'
                    );
                }
                if ($box['id'] !== $last_id)
                {
                    $box['Actions']['⇩'] = array(
                        true,
                        $this->url2aInside('move', array($box['id'], 'down')),
                        'title' => $this->trans('move down'),
                        'class' => 'char-icon'
                    );
                }
            }

            if ($can_edit)
            {
                $box['Actions']['edit'] = array(
                    true,
                    $this->url2aInside('edit', array($box['id'], '#' => 'content')),
                    'title' => $this->trans('edit this list'),
                    'class' => 'modal'
                );
            }

            if ($box['removed'] === null)
            {
                if ($can_remove)
                {
                    $box['Actions']['remove'] = array(
                        true,
                        $this->url2aInside('remove', array($box['id'])),
                        'title' => $this->trans('remove this list')
                    );
                }
            }

            $all_label = $this->trans('all (%d)', $count);
            $box['Actions'][$all_label] = array(
                true,
                $this->url2c('Paste', 'search', array($box['query'])),
                'title' => $this->trans('view all pastes')
            );
        }
    }


    /**
     * Common code for add and edit actions
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        - [0] box id
     * @param bool $editing
     */
    protected function _addOrEdit(array $params, $editing = false)
    {
        $parent = $this->getParent();

        if ( ! is_a($parent, 'IProfileController'))
        {
            return $this->redirect(array('HttpErrors', 'error404'));
        }

        $profile = $parent->getRow();
        $this->assign('profile', $profile);
        $user_id = g()->auth->ident();
        if (@$profile['IsGroup'])
        {
            $own_profile = ($user_id == $profile['leader_id']);
        }
        else
        {
            $own_profile = ($user_id == $profile['Ident']);
        }
        $this->assign('own_profile', $own_profile);
        $this->assign('editing', $editing);


        if ($editing)
        {
            $box = g('Box', 'model')
                ->filter(array(
                    'id'           => $params[0],
                    'profile_type' => $profile['ProfileType'],
                    'profile_id'   => $profile['id']
                ))
                ->getRow()
            ;
            if (empty($box))
            {
                return $this->redirect(array('HttpErrors', 'error403'));
            }
        }


        $this->_setTemplate('form');

        $form_ident = 'box';

        if (empty($this->data[$form_ident]))
        {
            if ($editing)
            {
                $this->data[$form_ident] = $box;
            }
            else if (@$profile['IsGroup'])
            {
                $this->data[$form_ident] = array(
                    'title'       => $this->trans('%s\'s pastes', g()->auth->displayName()),
                    'query'       => "user:" . g()->auth->ident(),
                    'limit'       => self::DEFAULT_LIMIT,
                    'list_paster' => true
                );
            }
            else // is user
            {
                $this->data[$form_ident] = array(
                    'title'       => $this->trans('My pastes'),
                    'query'       => "user:{$profile['Ident']}",
                    'limit'       => self::DEFAULT_LIMIT,
                    'list_paster' => true
                );
            }
        }
        else if ($this->_validated[$form_ident])
        {
            if ($editing)
            {
                $update_data = array(
                    'id'           => $box['id'],
                    'profile_type' => $box['profile_type'],
                    'profile_id'   => $box['profile_id'],
                    'order'        => $box['order']
                ) + $this->data[$form_ident];
                $result = g('Box', 'model')->sync($update_data, true, 'update');
            }
            else
            {
                g()->db->startTrans();

                $current_max_order = g('Box', 'model')
                    ->whiteList(array(
                        array('order', 'max')
                    ))
                    ->getScalar()
                ;

                $insert_data = array(
                    'profile_type' => $profile['ProfileType'],
                    'profile_id'   => $profile['id'],
                    'order'        => 1 + $current_max_order
                ) + $this->data[$form_ident];

                $result = g('Box', 'model')->sync($insert_data, true, 'insert');

                g()->db->completeTrans();
            }

            if ($result !== true)
            {
                g()->debug->dump($result);
                g()->addInfo(
                    'ds fail, new box for profile ' . $profile['id'],
                    'error',
                    $this->trans('((error:DS:%s))', false)
                );
            }
            else
            {
                g()->addInfo(
                    'new box for profile ' . $profile['id'],
                    'info',
                    $this->trans('New search list added') );
                //g()->db->completeTrans();
                if (empty($post_data['_backlink']))
                    $this->redirect($parent->url2a('', $parent->getParams()));
                else
                    $this->redirect($post_data['_backlink']);
            }
        }
    }


    /**
     * Common code for remove and restore actions
     * @author m.augustynowicz
     *
     * @param array $params action params
     * @param mixed $update_removed_to new value for column "removed"
     * @param Closure $success_msg_callback callback for generating
     *        success message. It should be a function that takes two args:
     *        - @param Component $that
     *        - @param array $box
     *
     * @return void
     */
    protected function _removeOrRestore(array $params, $update_removed_to, Closure $success_msg_callback)
    {
        $backlink = g()->req->getReferer();
        $msg_id   = g()->req->getRequestUrl() . ' ';

        $filter = array('id' => $params[0]);
        if ($update_removed_to === null)
        {
            $filter[] = array('removed', '<>', null);
        }
        else
        {
            $filter['removed'] = null;
        }

        $box = g('Box', 'model')
            ->filter($filter)
            ->getRow()
        ;

        if (empty($box))
        {
            g()->addInfo(
                $msg_id . '404',
                'error',
                $this->trans('Search list does not exist.')
            );
            $this->redirect($backlink);
        }


        $update = array(
            'id'      => $box['id'],
            'removed' => $update_removed_to
        );
        $result = g('Box', 'model')->sync($update, true, 'update');
        if ($result !== true)
        {
            g()->addInfo(
                $msg_id,
                'error',
                $this->trans('((error:DS:%s))', false)
            );
            g()->debug->dump($result);
        }
        else
        {
            g()->addInfo(
                'update "removed" to ' . $update_removed_to
                    . ' for box #' . $box['id'],
                'info',
                $success_msg_callback($this, $box)
            );
        }

        $this->redirect($backlink);
    }
}

