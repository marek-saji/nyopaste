<?php
$conf['translations']['User'] = array(
    // page titles
    'edit your profile' => 'edytuj swój profil',
    'edit <em>%s\'s</em> profile' => 'edytuj profil <em>%s</em>',
    
    'Don\'t forget to read <a href="%s">terms of use</a>.' => 'Nie zapomnij zapoznać się z <a href="%s">regulaminem</a>.', // paragraph in sign-up form

    'login or e-mail' => 'login, bądź e-mail', // sign-in form field

    'I forgot my password' => 'Nie pamiętam hasła', // lost password link
    // user's attribute names
    'login' => 'login',
    'password' => 'hasło',
    'e-mail' => 'e-mail',
    'website' => 'strona',
    'something about you' => 'coś o tobie',
    'something about me' => 'coś o mnie',
    'account type' => 'rodzaj konta',
    // help texts to user's attributes fields
    'Required for password recovery' => 'Wymagane do odzyskiwania hasła',

    'Are you sure you want to remove your account?' => 'Na pewno chcesz usunąć swoje konto?', // confirming removing own account
    'Are you sure you want to remove <em>%s</em>\'s account?' => 'Na pewno chcesz usunąć konto <em>%s</em>?', // confirming removing someone else's account
    'Are you sure you want to restore <em>%s</em>\'s account?' => 'Na pewno chcesz przywrócić konto <em>%s</em>?', // confirming restoring an account
    'remove' => 'usuń', // button
    'restore' => 'przywróć', // button
    'don\'t' => 'anuluj', // cancel link

    'update profile info' => 'uaktualnij profil', // button
    'create an account' => 'załóż konto', // button
    'accept terms of use and create an account' => 'zaakceptuj regulamin i utwórz konto', // button

    // form input error messages
    'Only letters, digits and dashes (<q>-</q>) are allowed' => 'Dozwolone są wyłącznie litery, cyfry oraz myślniki (<q>-</q>)',
    'This login is already taken. If the account belongs to you, you can <a href="%s">retrieve your password</a>' => 'Ten login jest już zajęty. Jeśli konto należy do ciebie, możesz <a href="%s">odzyskać hasło</a>.',
    'Account with this e-mail address already exists. If the account belongs to you, you can use <a href="%s">retrieve your password</a>.' => 'Konto z tym adresem e-mail już istnieje. Jeśli należy ono do ciebie, możesz <a href="%s">odzyskać hasło</a>.',

    // site infos
    'New user account has been created' => 'Nowe konto zostało utworzone', // after creating new account (by another user)
    'Profile has been updated' => 'Profil został uaktualniony', // after saving profile data
    'Your account has been created. You may sign in now' => 'Twoje konto zostało utworzone. Możesz się teraz zalogować.', // after registering
    'Welcome, <em>%s</em>' => 'Witaj, <em>%s</em>', // after signing in (%s = display name)
    'Can\'t remove system user <em>%s</em>' => 'Nie można usunąć użytkownika systemowego <em>%s</em>', // tried to remove system user
    'You have removed your account. Good bye.' => 'Usunąłeś swoje konto. Do widzenia', // after removing own account (and being signed-out)
    'You have removed <em>%s\'s</em> account.' => 'Usunąłęś konto użytkownika <em>%s</em>', // after removing an account (%s = display name)
    'You have restored <em>%s\'s</em> account.' => 'Przywróciłeś konto użytkownika <em>%s</em>', // after removing an account (%s = display name)
    'Wrong login or password' => 'Niepoprawny login, bądź haslo',

    // account types
    'administrator' => 'administrator',
    'moderator' => 'moderator',
    'regular user' => 'zwykły użytkownik',


    // actions that can be performed on user object:
    '((action:%s:%s))' => array(
        'default' => 'pokaż profil',
        'edit' => 'edytuj',
        'remove' => 'usuń',
        'restore' => 'przywróć',
    ),


    // lost password
    'Enter your e-mail address and you will receive a link allowing you to reset your password.'
        => 'Podaj swój adres e-mail, a prześlemy Ci link umożliwiający zresetowanie hasła.',
    'send me the reset link'     => 'wyślij mi link resetujący',
    'Error while sending e-mail' => 'Błąd przy wysyłaniu e-mail-a',
    '%s password reset request'  => 'Prośba o zresetowanie hasła na stronie %s',
    'E-mail with a link allowing you to reset the password has been sent. You should receive it in matter of minutes.'
        => 'E-mail zawierający link pozwalający na zresetowanie hasła został wysłany. Powinieneś otrzymać go w przeciągu kilku minut.',


    // verify these:

    'You have to log in for getting access.' => 'You have to log in for getting access.',
    'Please sign in' => 'Please sign in',
    'Remember me on this computer' => 'Remember me on this computer',
    'Don\'t have account yet?' => 'Don\'t have account yet?',
    'Given code is incorrect.' => 'Given code is incorrect.',
    'I accept, create my account' => 'I accept, create my account',
    'Already have an account? Please sign in.' => 'Already have an account? Please sign in.',
    'Repeat password' => 'Repeat password',
    'Activation link is incorrect.' => 'Activation link is incorrect.',
    'Given e-mail address is already taken' => 'Given e-mail address is already taken',
    'Given passwords are different' => 'Given passwords are different',

    'Error while adding user.' => 'Error while adding user.',
    'Error while account activating.' => 'Error while account activating.',
);

