<?php
/**
 * Technical settings
 */

// even consider the idea of debug mode being enabled
$conf['allow_debug'] = true;

$conf['controllers']['lib']['default'] = 'Main';
$conf['controllers']['debug']['sub'] = array (
    'name'=>'lib',
    'type'=>'Library',
);
$conf['controllers']['debug/lib'] = & $conf['controllers']['lib'];

// it's not obligatory, but let's show debug toolbar, when deebug is enabled
if ($conf['allow_debug'])
{
    // be awesome (debug toolbar etc)
    $conf['first_controller'] = array(
        'name'=>'debug',
        'type'=>'Debug', // does cool things.
    );
}
else
{
    // be less awesome
    $conf['first_controller'] = & $conf['controllers']['debug']['sub'];
}

// controllers loaded on every request
$conf['permanent_controllers'] = array(
);

// override hg classes with app implementations
$conf['classes_override'] = array(
    'Lang' => 'LangOne',
);


// enumerations used in datasets

define('STATUS_ACTIVE', 1);
define('STATUS_NOT_ACTIVATED', 2);
define('STATUS_DELETED', 4);
define('STATUS_DELETED_BY_OWNER', 12);
define('STATUS_BLOCKED', 20);

define('USER_TUPE_MOD', 9);
define('USER_TYPE_ADMIN', 25);

