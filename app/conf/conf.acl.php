<?php
/**
 * First match ends 
 * @see $conf[enum][user_type] for available user types, additionally
 *      unauthorized and authorized work
 */

$conf['acl'] = array(
    // permanent controllers
    'usernav'            => true,
    'menu'               => true,

    // common subcomponents
    'p'                  => true,

    '/HttpErrors'        => true,
    '/UnderConstruction' => true,


    '/Main/default'      => true,
    '/User/default'      => true,

    '/Ajaj/users'        => true,

    '/User/boxes/default' => true,
    '/User/boxes/remove'  => true,

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

    '/User/logout'       => array('authorized' => true),

    '/User/delete'       => array('superadmin' => true, 'admin' => true),
    '/UserUser/edit'     => array('superadmin' => true, 'admin' => true),

    '/Log'               => array('superadmin' => true, 'admin' => true, 'mod' => true),


    // if nothing matches, deny
    '*'                  => false,
);

