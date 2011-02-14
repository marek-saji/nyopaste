setup
-----

1. checkout app:

        git clone git://github.com/marek-saji/nyopaste.git src/

2. update local, dev and test IPs/hostnames

        vim src/app/htdocs/index.php

   Don't commit these, if you did modify!

3. checkout hologram:

        git submodule init
        git submodule update

4. prepare upload directory:

        mkdir -m 0700 upload/
        chown www-data:www-data upload/

5. local configuration

    - prepare local conf directory
   
            mkdir conf/
            chown www-data:www-data conf/

    - database credentials

            cp src/app/conf/conf.db.php conf/
            edit conf/conf.db.php

    - API keys

            cp src/app/conf/conf.keys.php conf/
            edit conf/conf.keys.php

6. configure apache
   - create vhost pointing to `src/app/htdocs/`
   - create alias `hg/` poining to `src/hg/htdocs/`
   - allow [.htaccess][] magic tricks
   - make sure [mod_rewrite][] is on

7. http://host.name/, enable favourite debugs

8. head to /DataSet/list and create all models

9. head to /Dev and launch these actions:
   - addDefaults, updated 2010-06-17

on production
-------------

1. switch to production `robots.txt`

        mv src/app/htdocs/robots{.prod,}.txt

--------

[.htaccess]:             http://httpd.apache.org/docs/current/howto/htaccess.html
[mod_rewrite]:           http://httpd.apache.org/docs/current/mod/mod_rewrite.html
