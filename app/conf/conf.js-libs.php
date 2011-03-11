<?php
$conf['js-libs'] = array(
    'jquery.encapsulating' => array(
        'filename' => 'jquery.encapsulating/jquery.encapsulating',
        'css' => array(
            'filename' => '../js/jquery.encapsulating/jquery.encapsulating',
            'less' => true
        ),
        'onload' => '$(".encapsulating").encapsulating()',
    ),

    /**
     * CSS2-3 selectors for IE
     * @url http://selectivizr.com/
     */
    'selectivizr' => array(
        'filename'      => 'selectivizr',
        'min_filename'  => 'selectivizr-development',
        'ie'            => '(gte IE 6)&(lte IE 8)',
    ),
);

