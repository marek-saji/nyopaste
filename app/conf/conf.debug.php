<?php
/**
 * Debug config.
 * @author m.augustynowicz
 */

$conf['debug'] = array(

    // debugs to turn on when enabling "fav"
    'favorite' => 'db, js, mail, view, debug, user, paste',

    'alternative base URLs' => array(
        'test' => 'http://nyopaste.test.1cm.pl/',
        'prod' => 'http://nyopaste.1cm.pl/'
    ),

    'shortcuts' => array(
        '<img src="https://github.com/favicon.ico" /> issues'      => 'https://github.com/marek-saji/nyopaste/issues',
        '<img src="https://github.com/favicon.ico" /> source code' => 'https://github.com/marek-saji/nyopaste/',
        '<img src="http://media.tumblr.com/tumblr_ltj7t9qk9G1qbis4g.png" height=16 /> js doc'      => 'js/',
        '<img src="http://www.uservoice.com/favicon.ico" height=16 /> uservoice'   => 'http://nyopaste.uservoice.com/',
        '<img src="https://www.google.com/images/icons/product/analytics-16.png" /> ganalytics'  => 'https://www.google.com/analytics/web/?pli=1#report/visitors-overview/a1058361w59147407p60407824/'
    )

);

