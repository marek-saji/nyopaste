<?php
/**
 * Main front end,
 * Usually you don't need to change anything here.
 * Use config files instead.
 * Or, if you really have to -- some of conf/init.php
 *
 * @author m.augustynowicz
 */

// defining the environment (where are we?)
defined('LOCAL_ENV') or define('LOCAL_ENV', 0);
defined('DEV_ENV')   or define('DEV_ENV',   1);
defined('TEST_ENV')  or define('TEST_ENV',  2);
defined('PROD_ENV')  or define('PROD_ENV',  3);

if (!defined('ENVIRONMENT'))
{
    switch (true)
    {
        # out beloved dev machines
        case '83.30.159.39' == $_SERVER['REMOTE_ADDR'] :
        case preg_match('/^192\.168\.0\.252/', $_SERVER['REMOTE_ADDR']) :
            define('ENVIRONMENT', DEV_ENV);
            break;
        # "there's no place like 127.0.0.1"
        case '127.0.0.1' == $_SERVER['REMOTE_ADDR'] :
        # local network
        case preg_match('/^192\.168\.0\./', $_SERVER['REMOTE_ADDR']) :
            define('ENVIRONMENT', LOCAL_ENV);
            break;
        # test.* hostnames
        case preg_match('/^test\.|test\.1cm\.pl$/', $_SERVER['HTTP_HOST']) :
            define('ENVIRONMENT', TEST_ENV);
            break;
        # production environment
        default :
            define('ENVIRONMENT', PROD_ENV);
    }
}

if (ENVIRONMENT >= TEST_ENV)
    error_reporting(0);
else
    error_reporting(E_ALL|E_STRICT);

#
# NOTICE:
# all defined paths should end with a slash!!
#  ALWAYS.
#
// this application's location
define('APP_DIR', realpath(dirname(__FILE__).'/..').'/');

// first directory outside source repository
// contains upload, temp, local conf and such
define('LOCAL_DIR', realpath(APP_DIR.'../..').'/');

// any local initialization? (should not be needed)
if (is_readable(LOCAL_DIR.'conf/init.php'))
    require_once LOCAL_DIR.'conf/init.php';

// collect directories with app, hg and anything between
global $DIRS;
$DIRS[APP_DIR] = '';
require_once APP_DIR.'conf/init.php';

// APP_DIR is first of $DIRS, HG_DIR is the last one
if (!defined('HG_DIR'))
{   
    end($DIRS);
    define('HG_DIR', key($DIRS));
}

// directories outside repository. they might have been set in local init.php
if (!defined('UPLOAD_DIR'))
    define('UPLOAD_DIR', realpath(LOCAL_DIR.'upload').'/');
if (!defined('TEMP_DIR'))
    define('TEMP_DIR', realpath(LOCAL_DIR.'tmp').'/');


// FINALLY!
require_once(HG_DIR.'lib/HologramCore.php');
$kernel = new Kernel();
$forest = & $kernel;
/* run, */ $forest->run();
