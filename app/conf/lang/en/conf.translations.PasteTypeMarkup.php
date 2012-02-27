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
<p>
    <span class="avoid-br"><code>*emphasis*</code>,</span>
    <span class="avoid-br"><code>**strong emphasis**</code>,</span>
    <span class="avoid-br"><code>[link](http://example.com/)</code>,</span>
    <span class="avoid-br"><code>`code`</code></span>
</p>

<p><a target="_blank" href="http://daringfireball.net/projects/markdown/syntax">there\'s much more you can do with Markdown</a></p>
',


    '((parser:textile))' => 'Textile',
    '((parser:textile:desc))' => '
<p>
    <span class="avoid-br"><code>_emphasis_</code>,</span>
    <span class="avoid-br"><code>*strong emphasis*</code>,</span>
    <span class="avoid-br"><code>"link":http://example.com/</code>,</span>
    <span class="avoid-br"><code>@code@</code></span>
</p>
<p><a target="_blank" href="http://textile.thresholdstate.com/">there\'s much more you can do with Textile</a></p>
',

);

