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
     *
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
                    'type'   => USER_TYPE_ADMIN,
                    'status' => STATUS_ACTIVE,
                ) + g()->conf['users']['admin']
            )
        );


        $paster = g()->conf['users']['admin']['login'];
        $tos_content = file_get_contents(APP_DIR.'/conf/default_pastes/TOS.markdown');
        $this->_insertSomething(
            'Paste',
            array(
                array(
                    'id'                   => -1,
                    'root_id'              => -1,
                    'paster_id'            => -1,
                    'paster'               => $paster,
                    'paster_tsv'           => $paster,
                    'url'                  => 'TOS',
                    'title'                => 'Terms of Service',
                    'title_tsv'            => 'Terms of Service',
                    'content'              => $tos_content,
                    'content_tsv'          => $tos_content,
                    'type'                 => 'markup',
                    'privacy'              => 'not listed',
                    'publicly_versionable' => false,
                    'tags_tsv'             => '',
                    'groups_tsv'           => ''
                )
            )
        );


        $this->_insertSomething(
            'PasteTypeMarkup',
            array(
                array(
                    'id'     => -1,
                    'parser' => 'markdown'
                ),
            )
        );


        $this->_devActionEnd($params, __FUNCTION__);
    }

}
