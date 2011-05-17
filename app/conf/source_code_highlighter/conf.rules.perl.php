<?php
/**
 * Rules for highlighting in Perl mode.
 * @author m.augustynowicz ported from javascript
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/perl_highlight_rules.js
 *
 * The contents of this file are subject to the GNU Lesser General Public
 * License 2.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.opensource.org/licenses/lgpl-2.1.html
 *
 * The Original Code is Ajax.org Code Editor (ACE).
 *
 * The Initial Developer of the Original Code is
 * Ajax.org B.V.
 * Portions created by the Initial Developer are Copyright (C) 2010
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *      Panagiotis Astithas <pastith AT gmail DOT com>
 */
$conf['source_code_highlighter']['modes'] = array(

    'perl' => array(

        'rules' => array(

            'start' => array(
                array(
                    'token' => "comment",
                    'regex' => "#.*"
                ), array(
                    'token' => "string.regexp",
                    'regex' => "[/](?:(?:\\[(?:\\\\]|[^\\]])+\\])|(?:\\\\/|[^\\]/]))*[/]\\w*\\s*(?=[).,;]|$)"
                ), array(
                    'token' => "string", // single line
                    'regex' => '["](?:(?:\\\\.)|(?:[^"\\\\]))*?["]'
                ), array(
                    'token' => "string", // multi line string start
                    'regex' => '["].*\\\\$',
                    'next' => "qqstring"
                ), array(
                    'token' => "string", // single line
                    'regex' => "['](?:(?:\\\\.)|(?:[^'\\\\]))*?[']"
                ), array(
                    'token' => "string", // multi line string start
                    'regex' => "['].*\\\\$",
                    'next' => "qstring"
                ), array(
                    'token' => "constant.numeric", // hex
                    'regex' => "0x[0-9a-fA-F]+\\b"
                ), array(
                    'token' => "constant.numeric", // float
                    'regex' => "[+-]?\\d+(?:(?:\\.\\d*)?(?:[eE][+-]?\\d+)?)?\\b"
                ), array(
                    'token' => "keyword",
                    'regex' => "base|constant|continue|else|elsif|for|foreach|format|goto|if|last|local|my|next|no|package|parent|redo|require|scalar|sub|unless|until|while|use|vars"
                ), array(
                    'token' => "constant.language",
                    'regex' => "ARGV|ENV|INC|SIG"
                ), array(
                    'token' => "support.function",
                    'regex' => "getprotobynumber|getprotobyname|getservbyname|gethostbyaddr|gethostbyname|getservbyport|getnetbyaddr|getnetbyname|getsockname|getpeername|setpriority|getprotoent|setprotoent|getpriority|endprotoent|getservent|setservent|endservent|sethostent|socketpair|getsockopt|gethostent|endhostent|setsockopt|setnetent|quotemeta|localtime|prototype|getnetent|endnetent|rewinddir|wantarray|getpwuid|closedir|getlogin|readlink|endgrent|getgrgid|getgrnam|shmwrite|shutdown|readline|endpwent|setgrent|readpipe|formline|truncate|dbmclose|syswrite|setpwent|getpwnam|getgrent|getpwent|ucfirst|sysread|setpgrp|shmread|sysseek|sysopen|telldir|defined|opendir|connect|lcfirst|getppid|binmode|syscall|sprintf|getpgrp|readdir|seekdir|waitpid|reverse|unshift|symlink|dbmopen|semget|msgrcv|rename|listen|chroot|msgsnd|shmctl|accept|unpack|exists|fileno|shmget|system|unlink|printf|gmtime|msgctl|semctl|values|rindex|substr|splice|length|msgget|select|socket|return|caller|delete|alarm|ioctl|index|undef|lstat|times|srand|chown|fcntl|close|write|umask|rmdir|study|sleep|chomp|untie|print|utime|mkdir|atan2|split|crypt|flock|chmod|BEGIN|bless|chdir|semop|shift|reset|link|stat|chop|grep|fork|dump|join|open|tell|pipe|exit|glob|warn|each|bind|sort|pack|eval|push|keys|getc|kill|seek|sqrt|send|wait|rand|tied|read|time|exec|recv|eof|chr|int|ord|exp|pos|pop|sin|log|abs|oct|hex|tie|cos|vec|END|ref|map|die|uc|lc|do"
                ), array(
                    'token' => "identifier",
                    'regex' => "[a-zA-Z_$][a-zA-Z0-9_$]*\\b"
                ), array(
                    'token' => "keyword.operator",
                    'regex' => "\\.\\.\\.|\\|\\|=|>>=|<<=|<=>|&&=|=>|!~|\\^=|&=|\\|=|\\.=|x=|%=|\\/=|\\*=|\\-=|\\+=|=~|\\*\\*|\\-\\-|\\.\\.|\\|\\||&&|\\+\\+|\\->|!=|==|>=|<=|>>|<<|,|=|\\?\\:|\\^|\\||x|%|\\/|\\*|<|&|\\\\|~|!|>|\\.|\\-|\\+|\\-C|\\-b|\\-S|\\-u|\\-t|\\-p|\\-l|\\-d|\\-f|\\-g|\\-s|\\-z|\\-k|\\-e|\\-O|\\-T|\\-B|\\-M|\\-A|\\-X|\\-W|\\-c|\\-R|\\-o|\\-x|\\-w|\\-r|\\b(?:and|cmp|eq|ge|gt|le|lt|ne|not|or|xor)"
                ), array(
                    'token' => "lparen",
                    'regex' => "[[({]"
                ), array(
                    'token' => "rparen",
                    'regex' => "[\\])}]"
                ), array(
                    'token' => "text",
                    'regex' => "\\s+"
                )
            ),

            "qqstring" => array(
                array(
                    'token' => "string",
                    'regex' => '(?:(?:\\\\.)|(?:[^"\\\\]))*?"',
                    'next' => "start"
                ), array(
                    'token' => "string",
                    'regex' => '.+'
                )
            ),

            "qstring" => array(
                array(
                    'token' => "string",
                    'regex' => "(?:(?:\\\\.)|(?:[^'\\\\]))*?'",
                    'next' => "start"
                ), array(
                    'token' => "string",
                    'regex' => '.+'
                )
            )

        ) // rules

    ) // perl

); // modes
