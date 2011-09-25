<?php
/**
 * Technical settings
 */

// even consider the idea of debug mode being enabled?
switch (getenv('HG_ENVIRONMENT'))
{
    case 'LOCAL' :
    case 'DEV' :
    //case 'TEST' :
        $conf['allow_debug'] = true;
        break;
    default :
        $conf['allow_debug'] = false;
}

$conf['controllers']['Lib']['default'] = 'Main';
$conf['controllers']['Debug']['sub'] = array (
    'name' => 'Lib',
    'type' => 'Library',
);
$conf['controllers']['Debug/Lib'] = & $conf['controllers']['Lib'];

// it's not obligatory, but let's show debug toolbar, when debug is enabled
if ($conf['allow_debug'])
{
    // be awesome (debug toolbar etc)
    $conf['first_controller'] = array(
        'name'=>'Debug',
        'type'=>'Debug', // does cool things.
    );
}
else
{
    // be less awesome
    $conf['first_controller'] = & $conf['controllers']['Debug']['sub'];
}

// controllers loaded on every request
$conf['permanent_controllers'] = array(
    'usernav' => 'UserNav',
    'menu' => 'Menu',
);

// override hg classes with app implementations
$conf['classes_override'] = array(
    'Lang' => 'LangOne',
);


// enumerations used in datasets

// status.
// can be bitmasked, if nessesary
define('STATUS_ACTIVE', 1);
define('STATUS_WAITING_FOR_ACTIVATION', 2);
define('STATUS_DELETED', 4);
define('STATUS_DELETED_BY_OWNER', 12);
define('STATUS_BLOCKED', 32);

