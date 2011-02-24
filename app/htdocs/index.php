<?php
/**
 * Main front end,
 * Usually you don't need to change anything here.
 * Use config files instead.
 * Or, if you really have to -- some of conf/init.php
 *
 * @author m.augustynowicz
 */

define('LOCAL_ENV', 0);
define('DEV_ENV',   1);
define('TEST_ENV',  2);
define('PROD_ENV',  3);

$env_const = strtoupper(getenv('HG_ENVIRONMENT')).'_ENV';
define('ENVIRONMENT', defined($env_const) ? constant($env_const) : PROD_ENV);

if (ENVIRONMENT >= TEST_ENV)
    error_reporting(0);
else
    error_reporting(E_ALL|E_STRICT);

#
# NOTICE:
# all defined paths should end with a slash!!
#  OR ELSE!
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
/**
 * $DIRS has paths as keys and httpd's aliases as values
 * @var array
 */
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


// FINALLY!
require_once(HG_DIR.'lib/HologramCore.php');
$kernel = new Kernel();
$forest = & $kernel;
exit(/* run, */ $forest->run());

