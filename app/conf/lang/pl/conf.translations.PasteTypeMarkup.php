<?php
/**
 * English translations
 *
 * BASICS (by example)
 *
 * conf[translations][_global] contains global translations
 * conf[translations][Foo] contains translations for Foo controller/model/etc
 *      as well as classes extending it.
 *
 *
 * IN DEPTH (by example)
 *
 * CustomFooComponent extends FooController extends Component extends Controller
 * extends HgBase
 * and let's assume that CustomFooComponent has been created with name "Bar"
 * Will check for translations in this order:
 *
 * conf[translations][bar] // lowercased name
 * conf[translations][CustomFoo] // class name minus "Component"
 * conf[translations][CustomFooComponent] // class name
 * conf[translations][Foo] // parent class name minus "Controller"
 * conf[translations][FooComponent] // parent class name
 * conf[translations][Component] // parent class name
 * conf[translations][Controller] // parent class name
 * conf[translations][HgBase] // parent class name
 * conf[translations][_global] // common ground
 */

$conf['translations']['PasteTypeMarkup'] = array(

    '((parser:markdown))' => 'Markdown',
    '((parser:markdown:desc))' => '<blockquote><p><i lang="en">Markdown</i> pozwala na tworzenie łatwego w czytaniu i pisaniu tekstu.</p></blockquote>
        <p><a href="http://pl.wikipedia.org/wiki/Markdown">dowiedz się więcej o <i lang="en">Markdown</i></a></p>',


    '((parser:textile))' => 'Textile',
    '((parser:textile:desc))' => '<blockquote><p><i lang="en">Textile</i> działa na tekście z *prostym* systemem znaczników i wytwarza poprawny <abbr lang="en">XHTML</abbr>.</p></blockquote>
        <p><a href=http://pl.wikipedia.org/wiki/Textile">dowiedz się więcej o <i lang="en">Textile</i></a></p>',

);

