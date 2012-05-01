<?php
/**
 * First match ends 
 * @see $conf[enum][user_type] for available user types, additionally
 *      unauthorized and authorized work
 */

$conf['acl'] = array(
    // permanent controllers
    'Usernav'            => true,
    'Menu'               => true,

    // common subcomponents
    'P'                  => true,

    '/HttpErrors'        => true,
    '/RobotsTxt'         => true,
    '/UnderConstruction' => true,


    '/Main/default'      => true,
    '/User/default'      => true,

    '/Ajaj/users'        => true,

    // can be accessed, when parent can
    '/User/Boxes/default'  => true,
    '/Group/Boxes/default' => true,

    '/Paste/new'         => false, // grand with callback
    '/Paste/default'     => true,
    '/Paste/newVerCheck' => true,
    '/Paste/search'      => true,
    '/Paste/error404'    => true,

    '/User/login'        => array('unauthorized' => true),
    '/User/new'          => array('unauthorized' => true),
    '/User/activate'     => array('unauthorized' => true),
    '/User/lostPasswd'   => array('unauthorized' => true),
    '/User/resetPasswd'  => array('unauthorized' => true),

    '/User/edit'         => array('superadmin' => true, 'admin' => 'true'),
    '/User/remove'       => array('superadmin' => true),
    '/User/restore'      => array('superadmin' => true),

    '/User/logout'       => array('authorized' => true),

    '/User/delete'       => array('superadmin' => true, 'admin' => true),
    '/UserUser/edit'     => array('superadmin' => true, 'admin' => true),

    '/Group/default'     => false, // grant with callback
    '/Group/new'         => array('authorized' => true),
    '/Group/edit'        => false,

    '/Log'               => array('superadmin' => true, 'admin' => true, 'mod' => true),


    // if nothing matches, deny
    '*'                  => false,
);

