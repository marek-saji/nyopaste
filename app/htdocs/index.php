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

/**
 * Temporary function, will be unset when done defining stuff.
 */
$hg_define = function ($name, $value) {
    // don't redefine, if already definied
    if (defined($name))
    {
        return true;
    }

    // try to get from system environment variable
    $env = getenv('HG_' . $name);
    if (false !== $env)
    {
        return define($name, $env);
    }
    else
    {
        return define($name, $value);
    }
};

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
$hg_define('APP_DIR', dirname(dirname(__FILE__)).'/');

// first directory outside source repository
// contains upload, temp, local conf and such
$hg_define('LOCAL_DIR', dirname(dirname(APP_DIR)).'/');

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
end($DIRS);
$hg_define('HG_DIR', key($DIRS));

// directories outside repository. they might have been set in local init.php
$hg_define('UPLOAD_DIR', LOCAL_DIR.'upload/');


unset($hg_define);

// FINALLY!
require_once(HG_DIR.'lib/HologramCore.php');
$kernel = new Kernel();
$forest = & $kernel;
exit(/* run, */ $forest->run());

