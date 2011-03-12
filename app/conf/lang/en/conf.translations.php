<?php
/**
 * Polish translations
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

$conf['translations']['_global'] = array(

    '((default site name format))' => '%s - %s', // default page title format: "page title - site name"


    // generic error messages

    // after something with DS went wrong
    // remember that "%s" should also be translated (if not FALSE)
    '((error:DS:%s))' => array(
        false => 'Error occured. Try again in few minutes and if error still exists, contact site\'s administrator',
        '' => '%s. Try again in few minutes and if error still exists, contact site\'s administrator',
    ),

    // when form submission conflict has been detected
    '((error:POST conflict))' => 'Someone posted this form, while you were editing it. Your data has <strong>not</em> been saved now. If you want to overwrite these changes, submit the form once again',

    'Fields marked with asterisk are required.'
        => 'Fields marked with <q class="required"><span class="inside"><small class="required">(field required)</small></span></q> are required.',
);


$conf['translations']['user'] = array(
    // actions that can be performed on user object:
    '((action:%s:%s))' => array(
        'default' => 'show profile',
    ),
);

$conf['translations']['paste'] = array(
    '((action:%s:%s))' => array(
        'newVer' => 'create new version',
    ),
);

