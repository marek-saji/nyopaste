<?php
/**
 * Common CSSes, JSes etc.
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


// set page description
if (!($desc = $v->getMeta('description')))
{
    $v->setDescription($this->trans('A little more social pastebin.'));
}



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

$v->addLess($this->file('common','less'));
$v->addLess($this->file('forms','less'));

// don't scale page on mobile devices
$v->setMeta('viewport', 'width=device-width, minimum-scale=1.0, maximum-scale=1.0');


## meanwhile,
## in debug mode..

if (g()->debug->on('js'))
{
    // for your debugging pleasure.
    // javascript console returns more helpful errors when js-es
    // get included this way.
    $v->addJs($this->file('hg.formsvalidate', 'js'));
}

