<?php
/**
 * Translations for Paste controller
 * @author m.augustynowicz
 */
$conf['translations']['Paste'] = array(
    // page titles
    'add new paste' => 'dodaj wpis',

    // attributes
    'title' => 'tytuł',
    'paster' => 'zamieścił',
    'author / source' => 'autor / źródło',
    'the paste' => 'treść', // group for "upload" and "type in"
    'upload' => 'wgraj plik',
    'type in' => 'wpisz',

    // add/edit form help texts
    'Pasting anonymously.' => 'Wklejasz anonimowo.', // for "paster"
    'You can <a href="%s">sign-in</a> or <a href="%s">create an account</a> to be able to easily find your paste later and delete delete it, if you have to.'
        => 'Możesz <a href="%s">zalogować się</a> lub <a href="%s">założyć konto</a>, aby w przyszłości móc łatwiej znaleść swój wpis oraz usunąć go, jeśli zajdzie taka potrzeba.', // for "paster"
    'If different than paster. Can also be URL to original context.'
        => 'Jeśli inny, niż pole <i>zamieścił</i>. Może to być adres strony, z której pochodzi treść.'

    // add/edit form buttons
    'accept terms of use and add' => 'zaakceptuj regulamin i dodaj',

    // actions
    '((action:%s:%s))' => array(
        'get' => 'pobierz',
        'plain' => 'pokaż jako tekst',
    ),
);

