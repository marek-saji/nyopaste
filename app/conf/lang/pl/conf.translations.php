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
    'a little more social pastebin' => 'nieco bardziej społecznościowy pastebin', // site motto

    'skip to content' => 'przejdź do treści',

    // links in global navigation, account area
    'sign in' => 'zaloguj się',
    'sign out' => 'wyloguj się',
    'create an account' => 'załóż konto',

    'cancel' => 'anuluj', // close modal XOR go back
    'field required' => 'pole wymagane',
    'Only fields marked with asterisk (<strong class="required">*</strong>) are required.' =>
        'Tylko pola oznaczone gwiazdką (<strong class="required">*</strong>) są wymagane.',

    // form input error messages
    'Field required' => 'Pole wymagane',
    'Invalid value' => 'Niepoprawna wartość',
    'Value too short' => 'Wartość zbyt krótka',
    'Value too long' => 'Wartość zbyt długa',
    'Invalid e-mail address' => 'Nieprawidłowy adres e-mail',
    'Invalid floating point value' => 'Niepoprawna liczba zmiennoprzecinkowa',
    'Incorrect URL' => 'Niepoprawny adres strony WWW',
    'Unsupported protocol' => 'Niepoprawny protokuł. Podaj adres zaczynający się od <code>http://</code>, bądź <code>https://</code>',
    'Number is to small' => 'Liczba zbyt mała',
    'Number is to big' => 'Liczba zbyt duża',
    'Passwords do not match' => 'Powtórzone hasło jest inne',

    'Entered CAPTCHA code is incorrect, try again' => 'Wpisany kod CAPTCHA jest nie poprawny, spróbuj ponownie',

    // text in the footer
    'powered by %s' => 'napędzane przez %s',
    'application version' => 'wersja aplikacji',


    // generic error messages

    // after something with DS went wrong
    // remember that "%s" should also be translated (if not FALSE)
    '((error:DS:%s))' => array(
        false => 'Wystąpił błąd. Spróbuj jeszcze raz za kilka minut, a jeśli błąd będzie nadal występował, skontaktuj się z administratorem strony',
        '' => '%s. Spróbuj jeszcze raz za kilka minut, a jeśli błąd będzie nadal występował, skontaktuj się z administratorem strony',
    ),

    // when form submission conflict has been detected
    '((error:POST conflict))' => 'Ktoś wysłał ten formularz, podczas kiedy go edytowałeś. Twoje dane <strong>nie</strong> zostały teraz zapisane. Jeśli chcesz nadpisać, wyślij ponownie formularz',
);

