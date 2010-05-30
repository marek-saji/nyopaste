<?php
/**
 * Polish translations
 *
 * @author everyone, run git-blame(1) for details
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

    // links in global navigation, account area
    'Sign In' => 'Zaloguj się',
    'Sign Out' => 'Wyloguj się',
    'Create an account' => 'Załóż konto',

    // form input error messages
    'Field required' => 'Pole wymagane',
    'Invalid value' => 'Niepoprawna wartość',
    'Value too short' => 'Wartość zbyt krótka',
    'Value too long' => 'Wartość zbyt długa',
    'Invalid e-mail address' => 'Nieprawidłowy adres e-mail',
    'Invalid floating point value' => 'Niepoprawna liczba zmiennoprzecinkowa',
    'Number is to small' => 'Liczba zbyt mała',
    'Number is to big' => 'Liczba zbyt duża',

    // text in the footer
    'powered by %s' => 'powered by %s',
    'application version' => 'wersja aplikacji',
);

$conf['translations']['User'] = array(
    'You have to log in for getting access.' => 'You have to log in for getting access.',
    'Please sign in' => 'Please sign in',
    'Remember me on this computer' => 'Remember me on this computer',
    'I forgot my password' => 'I forgot my password',
    'Don\'t have account yet?' => 'Don\'t have account yet?',
    'Create account now' => 'Create account now',
    'Given code is incorrect.' => 'Given code is incorrect.',
    'I accept, create my account' => 'I accept, create my account',
    'Already have an account? Please sign in.' => 'Already have an account? Please sign in.',
    'Repeat password' => 'Repeat password',
    'Activation link is incorrect.' => 'Activation link is incorrect.',
    'I\'m student' => 'I\'m student',
    'I\'m representing company' => 'I\'m representing company',
    'Given e-mail address is already taken' => 'Given e-mail address is already taken',
    'Given passwords are different' => 'Given passwords are different',
    'Registration confirmation on %s' => 'Registration confirmation on %s',    //registration e-mail subject
    '((company registration e-mail text))' => '<a href="%s">%s</a>',
    '((student registration e-mail text))' => '<a href="%s">%s</a>',
    '((text after registration - student))' => '((text after registration - student))',
    '((text after registration - company))' => '((text after registration - company))',
    '((info after activation - student))' => '((info after activation - student))',
    '((info after activation - company))' => '((info after activation - company))',

    'Error while adding user.' => 'Error while adding user.',
    'Error while account activating.' => 'Error while account activating.',
);

