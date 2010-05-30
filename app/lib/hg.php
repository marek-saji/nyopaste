<?php
/**
 * Code snippet to provide access to Hologram's Kernel,
 * including Debug, Request and Auth classes.
 * 
 * It creates global $kernel and $DIRS variables.
 *
 * It MUST be called from APP_DIR/lib, *not* HG_DIR/lib
 * @author m.augustynowicz
 */

global $DIRS;
global $kernel;

// where am I?

define('APP_DIR', realpath(dirname(__FILE__).'/..').'/');
define('LOCAL_DIR', realpath(APP_DIR.'/../..').'/');
if (is_readable(LOCAL_DIR.'conf/init.php'))
    require_once LOCAL_DIR.'conf/init.php';
$DIRS[APP_DIR] = '';
require_once APP_DIR.'conf/init.php';
if (!defined('HG_DIR'))
{
    end($DIRS);
    define('HG_DIR', key($DIRS));
}

// ach, ok. then let's the party begin!

require_once(HG_DIR.'lib/HologramCore.php');
$kernel = new Kernel();
$kernel->debug = $kernel->get('Debug');
$kernel->req = $kernel->get('Request');
$kernel->auth = $kernel->get('Auth');

