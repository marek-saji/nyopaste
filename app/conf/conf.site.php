<?php
/**
 * Site Settings
 */

// site name.
$conf['site_name'] = '<span class="nyopaste">n<span class="y">y</span>opaste</span>';

if(defined('ENVIRONMENT'))
{
    if(defined('LOCAL_ENV') && ENVIRONMENT == LOCAL_ENV)
    {
        if (function_exists('gethostname'))
        {
            $hostname = gethostname();
        }
        else
        {
            $hostname = php_uname('n');
        }
        $conf['site_name'] .= "<small>@{$hostname}</small>";
    }
    else if(defined('DEV_ENV') && ENVIRONMENT == DEV_ENV)
    {
        $conf['site_name'] .= ' (dev)';
    }
}

$conf['alternative base URLs'] = array(
    'local' => 'http://nyopaste/',
    //'dev'   => 'http://hg-app.dev.example.com/',
    'test'  => 'http://nyopaste.test.1cm.pl',
    //'pprod' => 'http://hg-app.pprod.example.com/',
    'prod'  => 'http://nyopaste.1cm.pl/',
);

