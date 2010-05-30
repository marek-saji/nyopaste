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

    // links in global navigation, account area
    'Sign In' => 'Zaloguj się',
    'Sign Out' => 'Wyloguj się',
    'Create an Account' => 'Załóż konto',

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

    // generic error message (usually after something in DB went wrong)
    // "%s" should also be translated
    '((error:%s))' => '%s. Spróbuj jeszcze raz za kilka minut, a jeśli błąd będzie nadal występował, skontaktuj się z administratorem strony',
);

$conf['translations']['User'] = array(
    '((mail-subject:after registration))' => 'Thank you for creating account in %$2s.', // mail subject, $1=login, $2=site name
    '((mail:after registration))' => 'Thank you for creating account in %$2s.', // mail content, $1=login, $2=site name

    'Error while adding user' => 'Błąd podczad dodawania użytkownika', // generic error

    'Add user' => 'Dodaj użytkownika', // button text
    'Accept terms of use and create account' => 'Zaakceptuj regulamin i utwórz konto', // button text
    'Don\'t forget to read <a href="%s">terms of use</a>.' => 'Nie zapomnij zapoznać się z <a href="%s">regulaminem</a>.', // paragraph in sign-up form
    'New user account has been created' => 'Nowe konto zostało utworzone', // site info
    'Your account has been created. You may sign in now' => 'Twoje konto zostało utworzone. Możesz się teraz zalogować.', // site info
    'Welcome, %s' => 'Witaj, %s', // site info after signing in (%s = display name)


    // verify these:

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
    '((info after activation - student))' => '((info after activation - student))',
    '((info after activation - company))' => '((info after activation - company))',

    'Error while adding user.' => 'Error while adding user.',
    'Error while account activating.' => 'Error while account activating.',
);

