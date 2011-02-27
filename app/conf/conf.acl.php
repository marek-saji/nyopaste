<?php
/**
 * First match ends 
 * @see $conf[enum][user_type] for available user types, additionally
 *      unauthorized and authorized work
 */

$conf['acl'] = array(
    // permanent controllers
    'usernav' => true,
    'menu' => true,

    // common subcomponents
    'p' => true,

    '/HttpErrors'           => true,


    '/Main/default'         => true,
    '/User/default'         => true,

    '/Paste/new'            => true,
    '/Paste/default'        => true,
    '/Paste/list'           => true,
    '/Paste/error404'       => true,

    '/User/login'           => array('unauthorized'=>true),
    '/User/new'             => array('unauthorized'=>true),
    '/User/activate'        => array('unauthorized'=>true),
    '/User/lostPasswd'      => array('unauthorized'=>true),
    '/User/resetPasswd'     => array('unauthorized'=>true),

    '/User/logout'          => array('authorized'=>true),

    '/User/delete'          => array('admin'=>true, 'superadmin'=>true),
    '/UserUser/edit'        => array('admin'=>true, 'superadmin'=>true),

    '/Log' => array('mod'=>true, 'admin'=>true, 'superadmin'=>true),
    

    // if nothing matches, deny
    '*' => false,
);

