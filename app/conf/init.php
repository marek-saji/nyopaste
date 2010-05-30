<?php
global $DIRS;
end($DIRS);
$dir = realpath(key($DIRS).'../hg').'/';
$DIRS[$dir] = 'hg/';

require_once $dir.'conf/init.php';

