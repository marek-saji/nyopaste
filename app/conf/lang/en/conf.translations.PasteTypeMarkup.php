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
    '((parser:markdown:desc))' => '<blockquote><p>Markdown allows you to write using easy-to-read, easy-to-write plain text format.</p></blockquote>
        <p><a href="http://daringfireball.net/projects/markdown/">read more about Markdown</a></p>',


    '((parser:textile))' => 'Textile',
    '((parser:textile:desc))' => '<blockquote><p>Textile takes plain text with *simple* markup and produces valid <abbr>XHTML</abbr>.</p></blockquote>
        <p><a href="http://textile.thresholdstate.com/">read more about Textile</a></p>',

);

