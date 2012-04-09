<?php
global $DIRS;
end($DIRS);
$dir = dirname(key($DIRS)).'/hg/';
$DIRS[$dir] = 'hg/';

require_once $dir.'conf/init.php';

