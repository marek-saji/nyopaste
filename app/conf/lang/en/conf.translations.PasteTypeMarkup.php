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
    '((parser:markdown:desc))' => '
<pre><code>- *emphasis*, **strong emphasis**,  [link](http://example.com/), `code`
- use dashes to make lists
    - oh, and this is a sub-list (four space/tab indent)
        1. and this is ordered sub-sub-list
        1. you don\'t have to care about numbers,
           markdown will take care of that.</code></pre>
<p><a href="http://daringfireball.net/projects/markdown/syntax">there\'s more you can do with Markdown</a></p>
',


    '((parser:textile))' => 'Textile',
    '((parser:textile:desc))' => '
<pre><code>* _emphasis_, *strong emphasis*,  "link":http://example.com/, @code@
* use asterists to make lists
** oh, and this is a sub-list
**# and this is ordered sub-sub-list</code></pre>
<p><a href="http://textile.thresholdstate.com/">there\'s more you can do with Textile</a></p>
',

);

