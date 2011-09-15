<?php
g()->load('Pages', 'controller');



/**
 * Heart of nyopaste, handling pastes
 * Type independent code
 * @author m.augustynowicz
 *
 * @todo default values in form
 */
class PasteController extends PagesController
{
    protected $_types = array();

    protected $_getOne_cache = null;

    public $forms = array(
        'search' => array(
            'model' => 'Paste',
            'upload' => false,
            'inputs' => array(
                'query' => array(
                    'fields' => null,
                    '_tpl'   => 'Forms/FString'
                )
            )
        ),
        'paste' => array(
            'model' => 'Paste',
            'upload' => true,
            'inputs' => array(
                'paster',
                'title',
                'author',
                'source_url',
                'url',
                'tags' => array(
                    'fields' => null,
                    '_tpl'   => 'Forms/Encapsulating'
                ),

                'parent_id' => array(
                    '_tpl' => 'Forms/hidden'
                ),
                'root_id' => array(
                    '_tpl' => 'Forms/hidden'
                ),

                'content_type' => array(
                    'fields' => null,
                    '_tpl'   => 'Forms/FRadio-single'
                ),
                'content_text' => array(
                    'fields' => null,
                    '_tpl'   => 'Forms/FMultilineString'
                ),
                'content_file' => array(
                    'fields' => null,
                    '_tpl'   => 'Forms/FFile'
                ),


                // privacy
                'privacy',
                'encode' => array('fields' => null),
                'enc_passwd',
                //'acl' =>

                'type' => array(
                    'fields' => null,
                    '_tpl'=>'Forms/FRadio-single'
                ),

                'privacy' => array(
                    '_tpl' => 'Forms/FRadio-single',
                ),

                'users' => array(
                    'fields' => null,
                    '_tpl'   => 'Forms/FString'
                ),

                'publicly_versionable',

                // + captcha
                // + [accept TOS and add]

                'store_settings' => array(
                    'fields' => null,
                    '_tpl'   => 'Forms/FBool'
                )
            ), // forms[paste][inputs]
        ), // forms[paste]
    ); // forms



    /**
     * Prepare default action.
     *
     * Add type-specific subcontrollers.
     * @author m.augustynowicz
     */
    protected function _prepareActionDefault(array & $params)
    {
        $this->_addPasteTypeSubcontrollers();
    }

    /**
     * Displaying a paste.
     *
     * @param array request params:
     *        - [0] pase's id, if none given, redirect to actionNew
     *        - [1] eigher "get" or "raw"
     *        - [v] paste version
     */
    public function actionDefault(array $params)
    {
        $url = @$params[0];
        $ver = @$params['v'];
        $display_type = @$params[1];

        $timestamp = $_SERVER['REQUEST_TIME'] or $timestamp = time();
        $this->assign('timestamp', $timestamp);

        if (!$url)
        {
            $this->redirect($this->url2a('new'));
        }

        // if no version given, redirect to newest one
        if (!$ver)
        {
            $model = g('Paste', 'model');
            $model->order('creation', 'DESC');
            $newest = $model
                ->whiteList(array('version'))
                ->getRow(array(
                    'url' => $url
                ))
            ;
            $params['v'] = $newest['version'];
            $this->redirect(array(
                $this->url(),
                $this->getLaunchedAction(),
                $params
            ));
        }


        // fetch main and paste type data
        if (!$this->getOne($url, $ver, true, $db_data))
        {
            return false;
        }


        switch ($display_type)
        {
            case 'get' :
                $filename = trim($db_data['title']);

                // add extension, if it's not already there

                $conf_modes = & g()->conf['paste_types']['source']['modes'];
                $extension  = $conf_modes[$db_data['syntax']]['extension'];
                if (!preg_match('/\.'.preg_quote($extension).'$/', $filename))
                {
                    $filename .= ".{$extension}";
                }

                $this->assign('filename', $filename);
                // no break here
            case 'raw' :
                $this->assign('content', $db_data['content']);
                g()->view = g('TextView');
                $this->_setTemplate('default.raw');
                return;
        }

        // determine which action can be launched
        $action = $this->getLaunchedAction();
        if ($this->_default_action == $action)
            $action = '';
        $removed = $db_data['status'] & STATUS_DELETED;
        $action_params = array($url) + ($ver ? array('v'=>$ver) : array());
        $all_actions = array(
            'download'   => array($this, $action,   array(1=>'get') + $action_params),
            'plain text' => array($this, $action,   array(1=>'raw') + $action_params),
            'share'      => array(true, '#share', 'class'=>'modal'),
            'newVer'     => array($this, 'new',     $action_params),
            'edit'       => array($this, 'edit',    $action_params),
            'remove'     => $removed ? false :
                            array($this, 'remove',  $action_params),
            'restore'    => !$removed ? false :
                            array($this, 'restore', $action_params),
        );
        foreach ($all_actions as $action => & $url)
        {
            if (!is_array($url) || true === $url[0])
                continue;
            $real_action = $url[1] ? $url[1] : 'default';
            if ($url[0] === $this)
            {
                if (!$this->hasAccess($real_action, $url[2]))
                    $url = false;
            }
            else
            {
                if (!g()->auth->hasAccess($url[0], $real_action, $url[2]))
                    $url = false;
            }
        }

        $basic_actions = array('download'=>1, 'plain text'=>1, 'share'=>1);
        $db_data['BasicActions'] = array_intersect_key(
            $all_actions,
            $basic_actions
        );
        $db_data['Actions'] = array_diff_key(
            $all_actions,
            $basic_actions
        );

        $type = $this->_types[$db_data['type']];
        $this->assignByRef('row', $db_data);
        $this->assign('type', $type);
        $type->assign('row', $db_data);


        // how often to check for for new version?

        // TODO latest paste in tree timestamp should be used
        $timestamp_delta = $timestamp - strtotime($db_data['creation']);
        if ($timestamp_delta < $diff = 60*60*24)
        {
            $ver_check_timeout = 5000; // 5s
        }
        else if ($timestamp_delta < $diff *= 7)
        {
            $ver_check_timeout = 60000; // 1min
        }
        else
        {
            $ver_check_timeout = 5*60000; // 5min
        }
        $this->assign('ver_check_timeout', $ver_check_timeout);
    }


    /**
     * Check if new version of a paste is available
     * @author m.augustynowicz
     *
     * @param array request params:
     *        - [0] paste id
     *        - [timestamp] check for pastes newer than timestamp
     */
    public function actionNewVerCheck(array $params)
    {
        $this->_setTemplate('empty');
        $url = @$params[0];
        $timestamp = @$params['timestamp'];

        $new_count = g('Paste', 'model')
            ->filter(array(
                'url' => $url,
                array('creation', '>', $timestamp)
            ))
            ->getCount()
        ;

        $this->assign('json', array(
            'count' => $new_count
        ));
    }


    /**
     * Prepare "search" action.
     *
     * Add paginator.
     * @author m.augustynowicz
     */
    protected function _prepareActionSearch(array & $params)
    {
        $this->addChild('Paginator', 'p');
    }

    /**
     * Search pastes.
     * @todo make it more "search", than "list"
     * @author m.augustynowicz
     *
     * @param array $param request params:
     *        - [0] search query
     */
    public function actionSearch(array $params)
    {
        $form_id = 'search';

        if (@$this->_validated[$form_id])
        {
            $this->redirect($this->url2a(
                $this->getLaunchedAction(),
                array(
                    $this->data[$form_id]['query']
                )
            ));
        }

        $query = $params[0];
        $this->data[$form_id]['query'] = $query;

        $this->assign('query', $query);

        $this->_assignSearchResults($query);
    }


    /**
     * Fetch and assign search results
     * @author m.augustynowicz
     *
     * @param string $query search query
     *
     * @return void Assigns results as $rows
     */
    protected function _assignSearchResults($query)
    {
        $model = g('Paste', 'model');

        list(
            $margin_from,
            $margin_to
        ) = $this->getChild('p')->getMargins();
        $sql_offset = (int) $margin_from;
        $sql_limit  = (int) ($margin_to - $margin_from);

        if (g()->auth->loggedIn())
        {
            // mine

            $sql_val_user_id = $model['paster_id']->dbString(g()->auth->id());
            $sql_query_or_visible_to_me = " OR {$model['paster_id']} = {$sql_val_user_id}";

            // access has been given to me

            $accessuser_model = g('PasteAccessUser', 'model');
            $sql_query_or_visible_to_me .= " OR {$sql_val_user_id} IN (SELECT user_id FROM {$accessuser_model} WHERE {$accessuser_model['paste_root_id']} = {$model['root_id']})";
        }
        else
        {
            $sql_query_or_visible_to_me = '';
        }


        $sql_val_public  = $model['privacy']->dbString('public');

        if ($query)
        {
            $replacements = array(
                // map human readable operators
                '/\bOR\b/'  => '|',
                '/\bAND\b/' => '&',
                '/\bNOT\b/' => '!',
                '/\s+-(?=[^\s])/' => ' !',
                '/"/' => "'",
                // insert AND operator between words
                '/(?<=[^!|()&])\s+(?=[^|()&])/' => ' & ',
                // map human readable weights
                '/\bpaster:([^!|()&\s]+)\b/'  => '$1:B',
                '/\btag:([^!|()&\s]+)\b/'     => '$1:A',
                '/\bgroup:([^!|()&\s]+)\b/'   => '$1:D',
            );
            $query = preg_replace(
                array_keys($replacements),
                $replacements,
                $query
            );

            $sql_ts_query = FRich::dbString($query);

            $sql_ts_vector = <<< SQL_TSV
                (
                    setweight({$model['title_tsv']},   'C')
                    ||
                    setweight({$model['paster_tsv']},  'B')
                    ||
                    setweight({$model['tags_tsv']},    'A')
                    ||
                    setweight({$model['content_tsv']}, 'C')
                    ||
                    setweight({$model['groups_tsv']},  'D')
                )
SQL_TSV
            ;

            $sql_query_select_rank = <<< SQL_SELECT_RANK
                ts_rank(
                    '{0.1, 0.5, 0.7, 1.0}', -- D, C, B, A weights
                    {$sql_ts_vector},
                    _query
                ) AS _rank
SQL_SELECT_RANK
            ;
            $headline_cfg = "'StartSel=''<strong class=query-keyword>'', StopSel=</strong>'";
            $sql_query_select_hl = <<< SQL_SELECT_HIGHLIGHTS
                ts_headline({$model['title']},   _query, $headline_cfg) AS hl_title,
                ts_headline({$model['paster']},  _query, $headline_cfg) AS hl_paster,
                ts_headline({$model['content']}, _query, $headline_cfg) AS hl_content
SQL_SELECT_HIGHLIGHTS
            ;
            $sql_query_plus_from_query = ", to_tsquery({$sql_ts_query}) AS _query";

            $sql_query_where_matches = "_query @@ {$sql_ts_vector}";

        }
        else // if $query else
        {
            $sql_query_select_rank = "{$model['creation']} AS _rank";
            $sql_query_select_hl = <<< SQL_SELECT_HIGHLIGHTS
                        {$model['title']}   AS hl_title,
                        {$model['paster']}  AS hl_paster,
                        {$model['content']} AS hl_content
SQL_SELECT_HIGHLIGHTS
            ;
            $sql_query_plus_from_query = '';
            $sql_query_where_matches = '1=1';
        }

        $sql_subquery = <<< QUERY_SQL
            (
                SELECT
                DISTINCT ON ({$model['root_id']})
                    -- basic data
                    {$model['url']},
                    {$model['version']},
                    -- result rank
                    {$sql_query_select_rank},
                    -- hightlighted data
                    {$sql_query_select_hl}
                FROM
                    {$model}
                    {$sql_query_plus_from_query}
                WHERE
                    -- visible
                    (
                        {$model['privacy']} = {$sql_val_public}
                        {$sql_query_or_visible_to_me}
                    )
                    AND
                    -- matches query
                    (
                        {$sql_query_where_matches}
                    )
                ORDER BY
                    {$model['root_id']},
                    _rank DESC
            ) pastes
QUERY_SQL
        ;

        $sql_count_query = "SELECT COUNT(1) FROM {$sql_subquery}";
        $count = g()->db->getOne($sql_count_query);
        $this->getChild('p')->config($count);

        $sql_query = "SELECT * FROM {$sql_subquery} ORDER BY _rank DESC LIMIT {$sql_limit} OFFSET {$sql_offset}";
        $rows = g()->db->getAll($sql_query);

        if (!$rows)
        {
            $rows = array();
        }


        // fetch details

        $model_class = g()->load('Paste', 'model');
        foreach ($rows as &$row)
        {
            $row += $model_class::getByUrl($row['url'], $row['version']);

            $row['title']   = $row['hl_title'];
            $row['content'] = $row['hl_content'];
            $row['paster']  = $row['hl_paster'];
            if (isset($row['Paster']['DisplayName']))
            {
                $row['Paster']['DisplayName'] = $row['hl_paster'];
            }

            preg_match("/^(.*$\s*){7}/m", $row['content'], $matches);
            $row['content_excerpt'] = rtrim($matches[0]);
        }

        $this->assignByRef('rows', $rows);
    }


    /**
     * Prepare "new" action.
     *
     * Add type-specific subcontrollers.
     * @author m.augustynowicz
     */
    protected function _prepareActionNew(array & $params)
    {
        $this->_addPasteTypeSubcontrollers();

        if (g()->auth->loggedIn())
        {
            $setting = g('UserSetting', 'model')->getRow(array(
                'user_id' => g()->auth->id(),
                'class'   => 'paste',
                'name'    => 'defaults for paste form'
            ));
            if ($setting)
            {
                $setting_value = (array) json_decode($setting['value'], true);
                $post_data_was_empty = @empty($this->data['paste']);
                $this->data['paste'] = array_merge(
                    (array) $setting_value[''],
                    (array) @$this->data['paste']
                );
                if ($post_data_was_empty)
                {
                    $this->data['paste']['_empty'] = true;
                }
                foreach ($this->_types as $type_name => &$type)
                {
                    $type->data['paste'] = array_merge(
                        (array) @$setting_value[$type_name],
                        (array) @$type->data['paste']
                    );
                }
                unset($type_name, $type);
            }
        }
    }

    /**
     * Create new paste
     * @author m.augustynowicz
     *
     * @param array $param request params:
     *        - [0] when creating new version of a paste, its the parent
     */
    public function actionNew(array $params)
    {
        $parent_url = @$params[0];
        $parent_ver = @$params['v'];

        $f = g('Functions');

        $form_id            = 'paste';
        $inputs             = & $this->forms[$form_id]['inputs'];
        $post_data          = & $this->data[$form_id];
        $post_data_is_empty = empty($post_data) || @$post_data['_empty'];
        $db_data            = array();


        $this->assign('new_version', (bool) $parent_url);


        // don't create new paste -- just store settings for future
        if (g()->auth->loggedIn() && @$this->data[$form_id]['store_settings'])
        {
            $setting = g('UserSetting', 'model');
            // used as filters and then to insert setting
            $setting_data = array(
                'user_id' => g()->auth->id(),
                'class'   => 'paste',
                'name'    => 'defaults for paste form'
            );
            $setting->filter($setting_data)->delete(true);

            // main controller data
            $setting_value = array(
                '' => $this->data[$form_id]
            );
            unset($setting_value['']['store_settings']);

            // paste types data
            foreach ($this->_types as $type_name => $type)
            {
                $setting_value[$type_name] = @$type->data[$form_id];
            }
            unset($type_name, $type);

            // unset underscore fields
            foreach ($setting_value as $setting_form_id => &$setting_form)
            {
                foreach ($setting_form as $key => &$value)
                {
                    if ('_' == $key[0])
                    {
                        unset($setting_form[$key]);
                    }
                }
            }
            unset($value);

            $setting_data['value'] = json_encode($setting_value);
            $result = $setting->sync($setting_data, true, 'insert');

            if (true === $result)
            {
                // (dirty) unset all form-related error messages
                unset(g()->infos['forms']);

                g()->addInfo(
                    'Paste_paste form settings stored',
                    'info',
                    $this->trans('Settings saved as new form defaults')
                );
            }
            else
            {
                g()->addInfo(
                    'Paste_paste form settings not stored',
                    'error',
                    $this->trans('Error occurred while storing form defaults.')
                );
            }

            $this->redirect(g()->req->getRequestUrl());
        }


        $this->assign(
            'max_upload_size_mb',
            min(
                $f->parseBytes(ini_get('upload_max_filesize')),
                $f->parseBytes(ini_get('post_max_size'))
            ) / 1024 / 1024
        );

        // list of static fields -- text gets rendered instead of input
        // key: name, value: text
        $static_fields = array();

        // creating new version
        if ($parent_url)
        {
            $this->getOne($parent_url, $parent_ver, false, $db_data);

            $static_fields = array(
                'title'                => &$db_data['title'],
                'url'                  => &$db_data['url'],
                //'author'               => &$db_data['author'],
                //'source_url'           => &$db_data['source_url'],
                'tags'                 => &$db_data['tags'],
                'type'                 => &$db_data['type'],
                'publicly_versionable' => &$db_data['publicly_versionable']
            );

            // limit to one type

            $one_type = &$this->_types[$static_fields['type']];
            $this->_types = array();
            $this->_types[$static_fields['type']] = $one_type;

            // fill up form data

            if ($post_data_is_empty)
            {
                $post_data = $db_data;
                unset($post_data['paster']);
                $post_data['content_text'] = & $post_data['content'];
            }
            $post_data['parent_id'] = $db_data['id'];
            $post_data['root_id']   = $db_data['root_id'];

            // fill up types form data too
            foreach ($this->_types as $type_name => $type)
            {
                $type->data[$form_id] =
                    (array) $post_data
                    +
                    (array) (@$type->data[$form_id])
                    +
                    (array) g()->conf['paste_types'][$type_name]['defaults']
                ;
            }
        }


        // assign reCAPTCHA settings

        $use_captcha = !g()->auth->loggedIn() && !g()->debug->on('disable', 'captcha');
        if ($use_captcha)
        {
            g()->load('recaptcha-php-1.10/recaptchalib', null);
            $this->assign(
                'recaptcha_publickey',
                g()->conf['keys']['recaptcha']['public']
            );
        }
        $this->assign('use_captcha', $use_captcha);


        /** @todo handle editing? */
        if ($post_data_is_empty)
        {
            $post_data = (array)$post_data + g()->conf['paste']['defaults'];
            $post_data['content_type'] = 'content_text';
        }
        else
        {
            // verify captcha
            if ($use_captcha)
            {
                $recaptcha_response = recaptcha_check_answer(
                    g()->conf['keys']['recaptcha']['private'],
                    $_SERVER['REMOTE_ADDR'],
                    $_POST['recaptcha_challenge_field'],
                    $_POST['recaptcha_response_field']
                );
                if (!$recaptcha_response->is_valid)
                {
                    g()->addInfo(
                        'wrong captcha, paste adding',
                        'error',
                        $this->trans('Entered CAPTCHA code is incorrect, try again.')
                    );
                    $this->_validated = false;
                }
            }

            if (null === @$post_data['publicly_versionable'])
            {
                $post_data['publicly_versionable'] = true;
            }

            if (@$this->_validated[$form_id])
            {
                $insert_data = $static_fields + $this->data[$form_id];
                g()->db->startTrans();
                do // just so we can break on errors
                {
                    /*
                    $db_data['last_edit'] = strtotime($db_data['last_edit']);
                    if ($post_data['_timestamp'] > $db_data['last_edit'])
                    {
                        $post_data['last_edit'] = & $post_data['_timestamp'];
                    }
                    else
                    {
                        g()->addInfo('post confict, updating user'.$id, 'error',
                            $this->trans('((error:POST conflict))') );
                        break;
                    }
                     */

                    $paste = g('Paste', 'model');


                    // insert main data

                    // url field must be not null and unique.
                    if (!@$static_fields['url'])
                    {
                        $insert_data['url'] = $paste->uniquifyURL(
                            @$insert_data['url'], @$insert_data['title']
                        );
                    }

                    $insert_data = $paste->getNewTreeData(@$db_data) + $insert_data;

                    $tags = preg_split('/[\n\r,]+/', trim($insert_data['tags']));

                    if (g()->auth->loggedIn())
                    {
                        $paster_nick = g()->auth->displayName();
                    }
                    else if ($insert_data['paster'])
                    {
                        $paster_nick = $insert_data['paster'];
                    }
                    else
                    {
                        $paster_nick = 'anonymous';
                    }

                    if (true !== $err = $paste->sync($insert_data, true, 'insert'))
                    {
                        g()->addInfo('ds fail, inserting paste', 'error',
                            $this->trans('((error:DS:%s))', false) );
                        g()->debug->dump($err);
                        break;
                    }

                    $paste_id = $paste->getData('id');

                    // update text search vectors and root_id

                    $paste_update = g('Paste', 'model');
                    $update_data = array(
                        'id'          => $paste_id,
                        'title_tsv'   => $paste_update->getField('title'),
                        'paster_tsv'  => $paster_nick,
                        'content_tsv' => $paste_update->getField('content'),
                        'tags_tsv'    => join(', ', $tags),
                        'groups_tsv'  => '' // TODO
                    );
                    if (!$post_data['root_id'])
                    {
                        $update_data['root_id'] = $paste_id;
                    }
                    if (true !== $err = $paste_update->sync($update_data, true, 'update'))
                    {
                        g()->addInfo('ds fail, inserting paste, updating root_id', 'error',
                            $this->trans('((error:DS:%s))', false) );
                        g()->debug->dump($err);
                        break;
                    }


                    // insert tags

                    if ($insert_data['tags'])
                    {
                        $paste_tag = g('PasteTag', 'model');
                        $tags_insert = array();
                        foreach ($tags as $tag)
                        {
                            $tag_insert[] = array(
                                'paste_id' => $paste_id,
                                'tag' => $tag
                            );
                        }
                        if (true !== $err = $paste_tag->sync($tag_insert, true, 'insert'))
                        {
                            g()->addInfo('ds fail, inserting paste tag', 'error',
                                $this->trans('((error:DS:%s))', false) );
                            g()->debug->dump($err);
                            break;
                        }
                    }


                    // insert users that have access to the paste

                    if ($insert_data['privacy'] === 'private' && $insert_data['users'])
                    {
                        $users_logins = preg_split('/[\s,]+/m', $insert_data['users']);
                        $users = g('User', 'model')
                            ->filter(array('login' => $users_logins))
                            ->whiteList(array('id'))
                            ->exec();
                        $useraccess_insert =& $users;
                        array_walk($users, function (& $row, $key, & $root_id) {
                            $row = array(
                                'user_id'       => $row['id'],
                                'paste_root_id' => $root_id
                            );
                        }, $update_data['root_id']);
                        $paste_useracceess = g('PasteAccessUser', 'model');
                        if (true !== $err = $paste_useracceess->sync($useraccess_insert, true, 'insert'))
                        {
                            g()->addInfo('ds fail, inserting paste user access', 'error',
                                $this->trans('((error:DS:%s))', false) );
                            g()->debug->dump($err);
                            break;
                        }
                    }


                    // insert type-specific data

                    $type = $this->_types[$paste->getData('type')];
                    if (true !== $err = $type->sync($paste, 'insert'))
                    {
                        g()->addInfo('ds fail, inserting paste type', 'error',
                            $this->trans('((error:DS:%s))', false) );
                        g()->debug->dump($err);
                        break;
                    }


                    // if we got here, everything's must have gone fine

                    g()->addInfo(null, 'info',
                                 $this->trans('Paste created') );
                    g()->db->completeTrans();
                    $this->redirect($this->url2a(
                        '',
                        array(
                            $paste->getData('url'),
                            'v' => $paste->getData('version')
                        )
                    ));
                }
                // just so we can break on errors
                while ('past' > 'future');

                // if we are here, we have failed
                g()->db->failTrans();
                g()->db->completeTrans();
            } // is validated
        } // if post data is empty else

        if (g()->auth->loggedIn())
        {
            $static_fields['paster'] = $post_data['paster']
                    = g()->auth->displayName();
        }
        else
        {
            unset($static_fields['paster']);
        }

        // switch templates of static fields

        foreach ($static_fields as $input_name => $static_value)
        {
            $this->forms[$form_id]['inputs'][$input_name]['tpl'] = 'Forms/static';
        }
        $this->assignByRef('static_fields', $static_fields);
    }


    /**
     * Error 404: paste not found.
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        - [0] url of a missing paste
     * @return void
     */
    public function actionError404(array $params)
    {
        $this->assign('url', @$params[0]);
    }


    /**
     * Common code for fetching one paste
     * @author m.augustynowicz
     *
     * @param mixed $id from URL
     * @param array $result rerence result will be stored to
     * @param bool $redirect if set to true, will redirect to error page,
     *        when no user fetched
     * @return bool success on fetching data
     */
    public function getOne($url, $ver, $with_tree=false, &$result, $redirect=true)
    {
        if (isset($this->_getOne_cache[$url][$ver][$with_tree]))
        {
            $result = $this->_getOne_cache[$url][$ver][$with_tree];
            return false !== $result;
        }

        $model_class = g()->load('Paste', 'model');
        $result = $model_class::getByUrl($url, $ver);

        if (!$result)
        {
            if ($redirect)
            {
                $params = $this->getParams();
                $this->delegateAction('error404', $params);
                return false;
            }
            else
            {
                return false;
            }
        }

        $this->_types[$result['type']]
            ->getOne($result['id'], $result);

        if ($with_tree)
        {
            $model = g('Paste', 'model');

            $model->order('creation', 'ASC');
            $model->order('id', 'ASC');
            $result['Tree'] = $model
                ->whiteList(array(
                    'parent_id',
                    'id',
                    'url',
                    'version',
                    'title',
                    'paster',
                    'paster_id',
                    'creation',
                ))
                ->getTree($result['root_id']);
        }

        $this->_getOne_cache[$url][$ver][$with_tree] = $result;
        return true;
    }


    /**
     * Add type-specific subcontrollers.
     * @author m.augustynowicz
     */
    protected function _addPasteTypeSubcontrollers()
    {
        $paste_types = g()->conf['paste_types'];
        foreach ($paste_types as $type_name => & $type_conf)
        {
            $type = $this->addChild('PasteType'.ucfirst($type_name), $type_name);
            $type->data['paste'] =
                (array) @$type->data['paste']
                + (array) @$this->data['paste']
            ;
            $this->_types[$type_name] = $type;
            $type->_action_to_launch = $this->_action_to_launch;
        }
        unset($type_conf);
        ksort($this->_types);
    }


    /**
     * Validating paste form
     *
     * Decides what content to use
     * @see validatePostContentText()
     * @see validatePostContentFile()
     * @author m.augustynowicz
     *
     * @param mixed @value set to false prevents the validation
     * @return array errors
     */
    public function validatePaste(array &$post)
    {
        if ('content_file' === $post['content_type'])
        {
            $post['content'] = & $post['content_file'];
            $post['content_text'] = false;
        }
        else
        {
            $post['content'] = & $post['content_text'];
            $post['content_file'] = false;
        }

        return array();
    }


    /**
     * Validating paste[content_text] form field
     *
     * Is validated as Paste model's content field, when not marked as dummy
     * @see validatePost()
     * @author m.augustynowicz
     *
     * @param mixed @value set to false prevents the validation
     * @return array errors
     */
    public function validatePasteContentText(& $value)
    {
        if (false === $value || g()->req->isAjax())
        {
            return array();
        }

        return g('Paste','model')->getField('content')->invalid($value);
    }


    /**
     * Validating paste[content_file] form field
     *
     * Reads uploaded file and put it's content into $value
     * @see validatePost()
     * @author m.augustynowicz
     *
     * @param mixed @value set to false prevents the validation
     * @return array errors
     */
    public function validatePasteContentFile(& $value)
    {
        if (false === $value || g()->req->isAjax())
        {
            return array();
        }

        switch (@$value['error'])
        {
            case UPLOAD_ERR_NO_FILE :
                return array('no_file' => $this->trans('No file uploaded'));
                break;
            case UPLOAD_ERR_FORM_SIZE :
            case UPLOAD_ERR_INI_SIZE :
                return array('too_big' => $this->trans('File too big'));
                break;
        }

        $value = file_get_contents($value['tmp_name']);
        $ret = g('Paste','model')->getField('content')->invalid($value);
        return $ret;
    }

    public function validatePasteUrl(&$value)
    {
        return array('stop_validation' => true);
    }


    /**
     * @todo implement this
     */
    /*
    public function validatePasteType(& $value)
    {
        $err = array();
        var_dump($value, $this->_types);
        if (!$value)
        {
            $err['notnull'] = 'No paste type chosen';
        }
        else if (!isset($this->_types[$value]))
        {
            $err['invalid'] = 'Incorrect paste type chosen';
        }
        return $err;
    }
     */


    /**
     * Checks access to "new" action
     * @todo implement me (for $params[0]
     * @author m.augustynowicz
     *
     * @param array $params request params
     *
     * @return bool
     */
    public function hasAccessToNew(array &$params)
    {
        $url = @$params[0];
        $ver = @$params['v'];

        // new paste

        if (!$url)
        {
            return true;
        }


        // new version of a existing paste

        if (!$this->getOne($url, $ver, true, $result))
        {
            return false;
        }

        $publicly_versionable = g('Functions')->anyToBool($result['publicly_versionable']);

        if ($publicly_versionable)
        {
            return true;
        }
        else if ($result['paster_id'])
        {
            return $result['paster_id'] == g()->auth->id();
        }
        else
        {
            return false;
        }

    }

}

