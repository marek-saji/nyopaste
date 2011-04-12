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


    /**
     * Ajax.org's and Mozilla's ACE editor
     * @url http://github.com/ajaxorg/ace/
     */
    'ace' => array(
        'cdn_path' => 'http://ajaxorg.github.com/ace/build/textarea/src/ace-uncompressed.js',
        'min_cdn_path' => 'http://ajaxorg.github.com/ace/build/textarea/src/ace.js',
        'autoload' => false
    ),


    /**
     * ZeroClipboard (clipboard copier)
     * @url https://code.google.com/p/zeroclipboard/
     */
    'zeroclipboard' => array(
        'filename' => 'ZeroClipboard',
        'autoload' => false,
        'onload'   => 'ZeroClipboard.setMoviePath("./js/ZeroClipboard.swf"); hg.init.copyToClipboard();'
    ),

);

