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
    'You can <a href="%s">sign in</a> or <a href="%s">create an account</a> to be able to easily find your paste later and delete delete it, if you have to.'
        => 'Możesz <a href="%s">zalogować się</a> lub <a href="%s">założyć konto</a>, aby w przyszłości móc łatwiej znaleść swój wpis oraz usunąć go, jeśli zajdzie taka potrzeba.', // for "paster"
    'If different than paster.' => 'Jeśli inny, niż pole <i>zamieścił</i>.',
    'URL address to original context.' => 'Adres strony, z której pochodzi treść.',

    'List of comma-separated tags that will be helpful in searching this paste. You can also use them to group pastes at <a href="%s">your profile page</a>.'
        => 'Lista etykiet rozdzielonych przecinkami, które pozwolą na łatwiejsze wyszukanie wpisu. Możesz ich również użyć do grupowania wpisów na <a href="%s">swoim profilu</a>.'
    'List of comma-separated tags that will be helpful in searching this paste. After you <a href="%s">sign in</a>, you will be able to use them to group pastes at your profile page.'
        => 'Lista etykiet rozdzielonych przecinkami, które pozwolą na łatwiejsze wyszukanie wpisu. Kiedy się <a href="%s">zalogujesz</a> będziesz mógł ich również użyć do grupowania wpisów na swoim profilu.'

    'more options including tags, original author, source URL and <q>keep for&hellip;</q>'
        => 'więcej opcji; <abbr title="między innymi">m.i.</abbr> etykiety, oryginalny autor, adres źródłowy oraz <q>trzymaj przez&hellip;</q>',
    'it\'s too cluttered, hide these options' => 'za dużo tu rzeczy, ukryj te opcje',

    'privacy' => 'prywatność',
    'show options including per-user and group permissions and encryption'
        => 'pokaż więcej opcji; <abbr title="między innymi">m.i.</abbr> uprawnienia dla użytkowników i grup oraz szyfrowanie',


    // "javascript enabled is cool" advices

    'With JavaScript available, this field would become a neat editor. Try enabling it, if you can.'
        => 'Z dostępnym JavaScript-em, to pole było by przyjemnym edytorem. Włącz go, jeśli możesz.',
    'With JavaScript available, changing this would also set the scheme in the editor above. Try enabling JavaScript, if you can.'
        => 'Z dostępnym JavaScript-em, zmiana tego pola powodowała by zmianę schematu kolorów również w edytorze powyżej. Włącz JavaScript, jeśli możesz.'

    // add/edit form buttons

    'accept terms of use and add' => 'zaakceptuj regulamin i dodaj',


    // actions
    '((action:%s:%s))' => array(
        'download' => 'pobierz',
        'raw' => 'pokaż jako tekst',
        'newVer' => 'stwórz nową wersję',
    ),

    'New version(s) of this paste available. You may want to refresh the page, to see them.'
        => 'Pojawiła się nowa wersja wpisu. Odśwież stronę, aby ją zobaczyć.',
);

