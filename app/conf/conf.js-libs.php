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

    'jquery.ui.autocomplete' => array(
        'filename' => 'jquery-ui-1.8.16.custom.autocomplete.min',
        'css' => array(
            'filename' => 'ui-lightness/jquery-ui-1.8.16.custom'
        )
    ),

    // hack: change order, load live_enents after autocomplete plugin
    'hg.live_events' => array(
        'autoload' => false
    ),
    'app-hg.live_events' => array(
        'filename' => 'hg.live_events'
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
        'filename'     => 'ace/build/textarea/src/ace-uncompressed',
        'min_filename' => 'ace/build/textarea/src/ace',
        'autoload'     => false
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

