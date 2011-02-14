<?php
/**
 * Common CSSes, JSes etc.
 *
 * @author m.augustynowicz
 */

// make sure lang is set
$v->setLang();


// set page title

$site_name = g()->conf['site_name'];
if (!($title = $v->getTitle()))
    $title = $site_name; // site name only
else if (false !== strpos($title, '%s'))
    $title = sprintf($title, $site_name); // sprintf-a-like title
else
    $title = $this->trans('((default site name format))', $title, $site_name); // "title - site name"
$v->setTitle($title);



// no need to go any further, when processing AJAX request
// below this point are thing shat should already be there
if (g()->req->isAjax())
    return;


//$v->addKeyword('');
//$v->setDescription('');

//$v->addLink('favicon',array('rel'=>'shortcut icon','href'=>$t->file('favicon.ico','gfx'),'type'=>'image/x-icon'));
//$v->addLink('favicon',array('rel'=>'shortcut icon','href'=>$t->file('favicon.png','gfx'),'type'=>'image/png'));

// combined profile for microformats.
// visit this URL for more information
$v->addProfile('http://purl.org/uF/2008/03/');

$v->addCss($this->file('common','css'));


// stylesheet for jquery-uniform
// without this forms will not be usable!
if (!g()->debug->on('disable', 'uniform'))
{
    $uniform_version = 1.5;
    $v->addCss($this->file("jquery.uniform-{$uniform_version}.default",'css'));
}


##
## jquery plugins
##

// use uncompressed/unminimized versions, when js debug is on.
$js_debug = g()->debug->on('js');
$pack = $js_debug ? '.pack' : '';
$min  = $js_debug ? '.min' : '';


/*
// jQuery UI: widgets and stuff.
$jqueryui_version = '1.7.2';
if (g()->debug->on('disable','externalcdn'))
{
    $v->addJs($t->file('jquery-ui-'.$jqueryui_version.$min,'js'));
    $v->addCss($t->file('jquery-ui','css'));
    $v->addJs($t->file('jquery-ui-i18n','js'));
}
else
{
    $v->addJs('http://ajax.googleapis.com/ajax/libs/jqueryui/'.$jqueryui_version.'/jquery-ui'.$min.'.js');
    $v->addCss('http://ajax.googleapis.com/ajax/libs/jqueryui/'.$jqueryui_version.'/themes/overcast/jquery-ui.css');
    $v->addJs('http://jquery-ui.googlecode.com/svn/tags/latest/ui/minified/i18n/jquery-ui-i18n'.$min.'.js');
}
 */

/*
// DD roundies: round corners for non-awesome browsers
$v->addJs($this->file('DD_roundies_0.0.2a'.($min?'-min':''), 'js'));
 */
# USAGE:
# div.foo {
#   border-radius: 1em;
#   -moz-border-radius: 1em;
#   -khtml-border-radius: 1em;
#   -webkit-border-radius: 1em;
#   -ie-border-radius: expression(DD_roundies('div.foo', '10px'));
#   /* DD_roundies accepts only px! (and "0") */
# }

// nyroModal: really neat modal windows
$nyromodal_version = '1.6.2';
if ($using_jquery_nyromodal = true)
{
    // note, packed version is currenly unsupported, while we modified the file
    $v->addJs($this->file('jquery.nyroModal-'.$nyromodal_version, 'js'));
    $v->addCss($this->file('jquery.nyroModal', 'css'));
}

/*
//cluetip
$v->addJs($this->file('jquery.cluetip-1.0.3','js'));
$v->addCss($this->file('jquery.cluetip','css'));
 */


##
## HoloGram javascript sweetness.
##

// bind events (preferably with $().live()) etc.
$v->addJs($this->file('hg.live_events', 'js'));

## meanwhile,
## in debug mode..

if (g()->debug->on('js'))
{
    if ($using_jquery_nyromodal)
        $v->addOnLoad('$.nyroModalSettings({debug: true})');
    // for your debugging pleasure.
    // javascript console returns more helpful errors when js-es
    // get included this way.
    $v->addJs($this->file('hg.formsvalidate', 'js'));
}

