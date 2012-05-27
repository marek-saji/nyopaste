<?php
g()->load('Pages', 'controller');
g()->load('IProfile', 'controller');

/**
 * User groups: searching, viewing, joining, creating...
 * @author m.augustynowicz
 */
class GroupController extends PagesController implements IProfileController
{
    /**
     * @var array cache for `_getGroup()`, idexed by id
     */
    protected $_row_cache = array();


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
                'invite_only' => array(
                    'fields' => false,
                    '_tpl' => 'Forms/FBool'
                ),
                'hidden'
            ),
        ), // form

        'invite' => array(
            'model' => 'Group',
            'inputs' => array(
                'members'
            )
        ),

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
            $group = $this->_getGroup();

            $this->addChild('ProfileBoxes', 'Boxes')
                ->assign('owner_is_group', true)
            ;
            $this->addChild('User', 'Users')
                ->assign('leader_id', $group['leader_id'])
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
        $boxes = $this->getChild('Boxes');
        if ($boxes->getLaunchedAction() !== '')
        {
            $this->_passRenderingTo($boxes);
            return;
        }


        $this->_setTemplate('profile');


        // assign row data
        $row = $this->_getGroup(); // handles 404

        // assign group actions

        $actions = array(
            'edit'   => true,
            'remove' => true,
            'invite' => true,
            'join'   => true,
            'leave'  => true
        );

        foreach ($actions as $action => & $access)
        {
            if ( ! $access )
            {
                continue;
            }

            $action_params = $params;
            if ($this->hasAccess($action, $action_params))
            {
                if ('remove' !== $action && 'invite' !== $action)
                {
                    $action_params['#'] = 'content';;
                }
                $actions[$action] = array(
                    true,
                    $this->url2aInside($action, $action_params)
                );
                if ('remove' !== $action && 'invite' !== $action)
                {
                    $actions[$action]['class'] = 'modal';
                }
            }
            else
            {
                unset($actions[$action]);
            }
            unset($action_params);
        }
        unset($action, $access);

        $row['Actions'] =& $actions;


        $this->assignByRef('row', $row);
        $this->assign('profile_owner', g()->auth->id() == $row['leader_id']);
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
        $group = $this->_getGroup(@$params[0], false);

        return (g()->auth->id() == $group['leader_id']);
    }



    /**
     * Join a group
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        [0] group id
     * @return void
     */
    public function actionJoin(array $params)
    {
        $group = $this->_getGroup();
        $group_id = $group['id'];


        $form_id = 'confirm';
        $post_data =& $this->data[$form_id];

        if (@$this->_validated[$form_id])
        {
            $new_data = array(
                'group_id' => $group_id,
                'user_id'  => g()->auth->id()
            );

            $result = g('GroupMembership', 'model')->sync($new_data, true, 'insert');

            if (true !== $result)
            {
                g()->addInfo('ds fail, joining roup'.$group_id, 'error',
                    $this->trans('((error:DS:%s))', false) );
                g()->debug->dump($result);
            }
            else
            {
                g()->addInfo('joined group'.$group_id, 'info',
                    $this->trans('You have joined %s.', $group['DisplayName']) );

                $backlink =& $post_data['_backlink'];
                if (empty($backlink))
                {
                    $backlink = $this->url2a('', $params);
                }
                $this->redirect($backlink);
            }
        }

        $this->assign(array(
            'question' => $this->trans('Do you want to join %s?',
                                $group['DisplayName'] ),
            'yes' => 'join',
            'no'  => 'don\'t'
        ));
        $this->_setTemplate('confirm');
    }

    /**
     * Grant access for non-members
     * @author m.augustynowicz
     *
     * @param array $params request params
     *        [0] group id
     *
     * @return bool
     */
    protected function hasAccessToJoin(array & $params)
    {
        if ( ! g()->auth->loggedIn() )
        {
            return false;
        }

        $group = $this->_getGroup(@$params[0], false);
        $group_id = $group['id'];
        $user_id  = g()->auth->id();

        if ($group['leader_id'] == $user_id)
        {
            return false;
        }

        $membership_class = g()->load('GroupMembership', 'model');
        if (true === $membership_class::isMember($group_id, $user_id))
        {
            return false;
        }

        return g('Functions')->anyToBool($group['open']);
    }



    /**
     * Invite users to a group
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        [0] group id
     * @return void
     */
    public function actionInvite (array $params)
    {
        $group = $this->_getGroup();
        $group_id = $group['id'];
        $form_id = 'invite';

        if ($this->_validated[$form_id])
        {
            $members_field = g('Users', 'field', array('dummy'));
            $errors = $members_field->invalid($this->data[$form_id]['members']);
            if (false === $errors)
            {
                $membership_class = g()->load('GroupMembership', 'model');
                $user_model = g('User', 'model');
                $mailer = g('Mail', array($this));
                $mail_vars = array(
                    'group' => & $group
                );

                $new_members_count = 0;
                $mails_sent_to = array();
                $logins = $members_field->getLogins();
                foreach ($logins as $user_id => $login)
                {
                    $result = $membership_class::invite($user_id, $group_id);
                    $new_members_count += (int) $result;

                    if ($result)
                    {
                        // notify user

                        $user = $user_model->getRow($user_id);
                        if ($user['email'])
                        {
                            $mail_vars['user'] =& $user;
                            $result = $mailer->send($user['email'], 'invitation', $mail_vars);

                            if (true !== $result)
                            {
                                g()->addInfo(
                                    'mail error',
                                    'error',
                                    $this->trans(
                                        'Failed to send invitation mail to <em>%s</em>, we are sorry.',
                                        $login
                                    )
                                );
                            }
                            else
                            {
                                $mails_sent_to[] = $user['DisplayName'];
                            }
                        }
                    }
                }

                g()->addInfo(
                    "new members in {$group_id}",
                    'info',
                    $this->trans(
                        'Added %d new member(s) to %s',
                        $new_members_count,
                        $group['DisplayName']
                    )
                );
                if ( ! empty($mails_sent_to) )
                {
                    g()->addInfo(
                        "mails sent to new members in {$group_id}",
                        'info',
                        $this->trans(
                            'Mail(s) sent to: %s',
                            join(', ', $mails_sent_to)
                        )
                    );
                }
                $this->redirect($this->url2c('Group', '', $params));
            }
        }

        $this->assign('row', $group);
    }

    /**
     * Grant access to group leader
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        [0] group id
     * @return bool
     */
    public function hasAccessToInvite(array & $params)
    {
        return $this->hasAccessToEdit($params);
    }



    /**
     * Leave a group
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        [0] group id
     * @return void
     */
    public function actionLeave(array $params)
    {
        $group = $this->_getGroup();
        $group_id = $group['id'];;
        $user_id  = g()->auth->id();


        $form_id = 'confirm';
        $post_data =& $this->data[$form_id];


        $backlink =& $post_data['_backlink'];
        if (empty($backlink))
        {
            $backlink = $this->url2a('', $params);
        }


        if (@$this->_validated[$form_id])
        {
            $old_data = array(
                'group_id' => $group_id,
                'user_id'  => g()->auth->id()
            );

            $result = g('GroupMembership', 'model')
                ->filter($old_data)
                ->delete(true)
            ;

            if (g()->db->lastErrorMsg())
            {
                g()->addInfo('ds fail, leaving group'.$group_id, 'error',
                    $this->trans('((error:DS:%s))', false) );
                g()->debug->dump($result);
            }
            else
            {
                g()->addInfo('leaving group'.$group_id, 'info',
                    $this->trans('You have left %s.', $group['DisplayName']) );

                $this->redirect($backlink);
            }
        }


        $this->assign(array(
            'question' => $this->trans('Do you want to leave %s?',
                                $group['DisplayName'] ),
            'yes' => 'leave',
            'no'  => 'don\'t'
        ));
        $this->_setTemplate('confirm');
    }

    /**
     * Grant access for members
     * @author m.augustynowicz
     *
     * @param array $params request params
     *        [0] group id
     *
     * @return bool
     */
    protected function hasAccessToLeave(array & $params)
    {
        $group_id = (int) @$params[0];
        $user_id  = g()->auth->id();

        $membership_class = g()->load('GroupMembership', 'model');
        if (false === $membership_class::isMember($group_id, $user_id))
        {
            return false;
        }

        $group = $this->_getGroup($group_id, false);
        if ($group['leader_id'] === $user_id)
        {
            return false;
        }

        return true;
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

        $group = $this->_getGroup($id, false);
        return ! empty($group);
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
        $f = g('Functions');


        if ($editing)
        {
            $row = $this->_getGroup();
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
            $new_data['open'] = ! $f->anyToBool($new_data['invite_only']);


            $backlink =& $this->data[$form_ident]['_backlink'];
            if (empty($backlink))
            {
                $backlink = $this->url2a('', $params);
            }

            if ($editing)
            {
                $db_action = 'update';
                $new_data['id'] = $row['id'];
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



    /**
     * Remove a group
     * @author m.augustynowicz
     *
     * @param array $params URL params
     *        [0] group id
     */
    public function actionRemove(array $params)
    {
        $db_data = $this->_getGroup();

        $data_update = array(
            'id'      => $db_data['id'],
            'removed' => time()
        );

        $result = g('Group', 'model')->sync($data_update, true, 'update');

        if (true !== $result)
        {
            g()->addInfo('ds fail, deleting group'.$db_data['id'], 'error',
                $this->trans('((error:DS:%s))', false) );
            g()->debug->dump($result);
        }
        else
        {
            $restore_params = $params;
            $undo_link = $this->l2a(
                $this->trans('Undo'),
                'restore',
                $restore_params
            );
            g()->addInfo(
                'removed group'.$db_data['id'],
                'info',
                $this->trans(
                    'You have removed %s. (%s)',
                    $db_data['DisplayName'],
                    $undo_link
                )
            );
        }

        $backlink = $this->url2c('Group', '');
        $this->redirect($backlink);
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
    protected function hasAccessToRemove(array & $params)
    {
        return $this->hasAccess('edit', $params);
    }


    /**
     * Restore removed group
     * @author m.augustynowicz
     *
     * @param array $params request params
     *        [0] group id
     */
    public function actionRestore(array $params)
    {
        $row = $this->_getGroup(null, false, true);

        $row['removed'] = null;

        $result = g('Group', 'model')->sync($row, true, 'update');

        if (true !== $result)
        {
            g()->addInfo('ds fail, undeleting group'.$row['id'], 'error',
                $this->trans('((error:DS:%s))', false) );
            g()->debug->dump($result);

            $this->redirect($this->url2c('Group', ''));
        }
        else
        {
            g()->addInfo(
                'undeleted group'.$row['id'],
                'info',
                $this->trans('Group restored')
            );
            $this->redirect($this->url2c('Group', '', $params));
        }
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
    protected function hasAccessToRestore(array & $params)
    {
        $row = $this->_getGroup(null, false, true);
        return (g()->auth->id() == $row['leader_id']);
    }


    /**
     * Get group that's id is in request params
     * @author m.augustynowicz
     *
     * @return array
     */
    public function getRow()
    {
        return $this->_getGroup(null, false);
    }


    /**
     * Fetch group data
     * @author m.augustynowicz
     *
     * @param int|null $id group id. null gets id from request params
     * @param bool $redirect redirect to 404, when group not found
     * @param bool $include_removed
     *
     * @return array
     */
    protected function _getGroup ($id = null, $redirect = true, $include_removed = false)
    {
        if (null === $id)
        {
            $id = $this->getParam(0);
        }

        if (isset($this->_row_cache[$include_removed][$id]))
        {
            return $this->_row_cache[$include_removed][$id];
        }

        $model = g('Group', 'model');

        $filter = $model->getAccessibleFilter($include_removed)
            ->also(new FoBinary($model['id'], '=', $id))
        ;
        $row = $model->filter($filter)->getRow();

        if ( ! $row )
        {
            if ($redirect)
            {
                return $this->redirect(array('HttpErrors', '', array(404)));
            }
            else
            {
                return array();
            }
        }

        $row['invite_only'] = ! g('Functions')->anyToBool($row['open']);

        $row['ProfileType'] = 'group';
        $row['IsGroup'] = true;

        $model->enchance($row);

        $this->_row_cache[$include_removed][$id] =& $row;

        return $row;
    }

}

