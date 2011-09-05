requirements
------------

1. Postgres SQL 8.3+ with Text Search
1. PHP 5.3+ with Postgres SQL module
1. `mail()` working in PHP



setup
-----

1. checkout app:

        git clone git://github.com/marek-saji/nyopaste.git src/

1. checkout submodules:

        git submodule init
        git submodule update

1. prepare upload directory:

        mkdir -m 0700 upload/
        chown www-data:www-data upload/

1. local configuration

    - prepare local conf directory

            mkdir conf/
            chown www-data:www-data conf/

    - database credentials

            cp src/app/conf/conf.db.php conf/
            edit conf/conf.db.php

    - API keys

            cp src/app/conf/conf.keys.php conf/
            edit conf/conf.keys.php

1. configure apache
   - create vhost pointing to `src/app/htdocs/`
   - create alias `hg/` pointing to `src/hg/htdocs/`
   - allow [.htaccess][] magic tricks
   - make sure [mod_rewrite][] is on
   - set HG_ENVIRONMENT to one of LOCAL, DEV, TEST or PROD

1. open your site in the browsers, enable favourite debugs

1. head to `/DataSet/list` and create all models

1. head to `/Dev` and launch these actions:
   - addDefaults (updated 2010-06-17)

### on production

1. switch to production `robots.txt`

        mv src/app/htdocs/robots{.prod,}.txt



[.htaccess]:             http://httpd.apache.org/docs/current/howto/htaccess.html
[mod_rewrite]:           http://httpd.apache.org/docs/current/mod/mod_rewrite.html

