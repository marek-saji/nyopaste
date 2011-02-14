<?php
/**
 * User-related configurations
 */

$conf['users'] = array(
    // fields you can use as a login while siging-in
    'login_fields' => array(
        'login',
        'email',
    ),
    // field used when displaying user's identity
    'display_name_field' => 'login',
    // field used to identify user (has to be URL safe!)
    'ident_field' => 'login',
);

// enumerate for [type] field
// can be bitmasked, if nessesary
define('USER_TYPE_UNAUTHORIZED', 0x0000); // underhuman
define('USER_TYPE_AUTHORIZED',   0x0001); // common mortal
define('USER_TYPE_MOD',          0x0FFF); // chief
define('USER_TYPE_ADMIN',        0xEFFF); // the king
define('USER_TYPE_SUPERUSER',    0xFFFF); // godmode on

