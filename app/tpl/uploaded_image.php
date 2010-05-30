<?php
/**
 * Displays uploaded image.
 * As an option, it makes it nyroModal link.
 * @author m.augustynowicz
 *
 * @todo MAKE IT WORK (copied 1:1 from bdb)
 *
 * (params passed as local variables) 
 * @param string|array [0] or $id image hash or array with model, id, extension, REQUIRED
 * @param string [1] or $extension extension, REQUIRED if not given in [0]
 * @param string [2] or $model model. if not given -- $this->getName() is used
 * @param string $size size of image
 * @param string $title title text
 * @param string $alt alternative text
 * @param boolean $modal render link to original image and add modal class
 * @param string $gal when $modal -- place image in this gallery
 * @param string $class html class to add to <img> tag
 * @param array $attrs html attributes to add to <img> tag
 * @param boolean $return return instead of displaying
 *
 * @return array with merged attributes of <img /> and <a /> tags
 */
extract(array_merge(
        array(
            'id'       => null,
            'extension'=> null,
            'model'    => $t->getName(),
            'size'     => 'original',
            'title'    => null,
            'alt'      => '',
            'modal'    => true,
            'gal'      => null,
            'attr'     => array(),
            'class'    => null,
            'return'   => false,
        ),
        (array) $____local_variables
    ), EXTR_PREFIX_INVALID, 'param');

if (!isset($param_0))
    $param_0 = $id;
if (!isset($param_1))
    $param_1 = $extension;
if (!isset($param_2))
    $param_2 = $model;

if (is_array(@$param_0))
{
    $param_1 = $param_0['extension'];
    $param_2 = $param_0['model'];
    $title   = @$param_0['title'];
    $param_0 = $param_0['id'];
}
else if (!@$param_2)
    $param_2 = $t->getName();

if (!@$param_0 || !@$param_1)
{
    trigger_error('Wrong parameters passed to tpl/upload_image', E_USER_NOTICE);
    return;
}

$url_f = sprintf('%1$supload/images/%2$s/%3$s/%%s.%5$s', g()->req->getBaseUri(), $param_2, $param_0, $size, $param_1, $title, $gal);
$attr['src'] = sprintf($url_f, $size);
if (!isset($attr['alt']))
    $attr['alt'] = & $alt;
if ($title)
    $attr['title'] = '%6$s';
if ($class)
    @$attr['class'] .= ' '.$class;
$out = $f->tag('img', $attr);

if (!$modal)
    $a_attr = array();
else
{
    $a_attr = array(
            'href' => sprintf($url_f, 'original'),
            'class' => 'modal'
        );
    if ($gal)
        $a_attr['rel'] = $gal;
    $out = $f->tag('a', $a_attr, $out);
}

if ($return)
    return $out;
else
{
    echo $out;
    return array_merge($a_attr, $attr);
}

