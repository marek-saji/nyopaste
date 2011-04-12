<?php

$conf['locale'] = array(
    // see: http://en.wikipedia.org/wiki/IETF_language_tag
    'fallback'  => 'en',
    // on *nix: find /usr/share/zoneinfo # (or similar)
    'time zone' => 'Europe/Warsaw',

    'date format' => array(
        'default' => 35, // see class.Functions.php for values

        'days offsets' => array( // will be translated
            -0  => 'today',
            -1  => 'yesterday',
            -2  => 'two days ago',
            -3  => 'three days ago',
            -4  => 'four days ago',
            -5  => 'five days ago',
            -6  => 'six days ago',
        ),

        // strftime() format
        //'human time' => '%H:%M',
        //'human date' => '%Y-%m-%d',
        //'human date+time' => '%Y-%m-%d at %H:%M',
        //'sortable time' => '%H:%M',
        //'sortable date' => '%Y-%m-%d',
        //'sortable date+time' => '%Y-%m-%d at %H:%M',
        //'sql' => '%Y-$m-%dT%H:%M:%S%P'
    ),

    // specify regexps of accepted formats, or leave empty for all
    // parseable by strtotime
    'accepted date formats' => array(
        // e.g. '/[0-9]{4}-[0-9]{2}-[0-9]{2}/',
    ),

);

