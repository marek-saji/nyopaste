<?php
/**
 * Site Settings
 */

// site name.
$conf['site_name'] = 'n<em>y</em>opaste';

if(defined('ENVIRONMENT'))
{
    if(defined('LOCAL_ENV') && ENVIRONMENT == LOCAL_ENV)
        $conf['site_name'] .= '<small>@'.gethostname().'</small>';
    else if(defined('DEV_ENV') && ENVIRONMENT == DEV_ENV)
        $conf['site_name'] .= ' (dev)';
}

$conf['alternative base URLs'] = array(
    'local' => 'http://nyopaste/',
    //'dev'   => 'http://hg-app.dev.example.com/',
    'test'  => 'http://nyopaste.test.1cm.pl',
    //'pprod' => 'http://hg-app.pprod.example.com/',
    'prod'  => 'http://nyopaste.1cm.pl/',
);

