<?php
/**
 * Rules for highlighting in Coffee mode.
 * @author m.augustynowicz ported from javascript
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/coffee_highlight_rules.js
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
 *      Satoshi Murakami <murky.satyr AT gmail DOT com>
 */

$identifier = "[\$A-Za-z_\\x7f-￿][$\\w\\x7f-￿]*";
$keywordend = "(?![$\\w]|\\s*:)";
$stringfill = array(
    'token' => "string",
    'regex' => ".+"
);

$conf['source_code_highlighter']['modes'] = array(

    'coffee' => array(

        'rules' => array(

            'start' => array(

                array(
                    'token' => "identifier",
                    'regex' => "(?:@|(?:\\.|::)\\s*)" . $identifier
                ),
                array(
                    'token' => "keyword",
                    'regex' => "(?:t(?:h(?:is|row|en)|ry|ypeof)|s(?:uper|witch)|return|b(?:reak|y)|c(?:ontinue|atch|lass)|i(?:n(?:stanceof)?|s(?:nt)?|f)|e(?:lse|xtends)|f(?:or (?:own)?|inally|unction)|wh(?:ile|en)|n(?:ew|ot?)|d(?:e(?:lete|bugger)|o)|loop|o(?:ff?|[rn])|un(?:less|til)|and|yes)" . $keywordend
                ),
                array(
                    'token' => "constant.language",
                    'regex' => "(?:true|false|null|undefined)" . $keywordend
                ),
                array(
                    'token' => "invalid.illegal",
                    'regex' => "(?:c(?:ase|onst)|default|function|v(?:ar|oid)|with|e(?:num|xport)|i(?:mplements|nterface)|let|p(?:ackage|r(?:ivate|otected)|ublic)|static|yield|__(?:hasProp|extends|slice|bind|indexOf))" . $keywordend
                ),
                array(
                    'token' => "language.support.class",
                    'regex' => "(?:Array|Boolean|Date|Function|Number|Object|R(?:e(?:gExp|ferenceError)|angeError)|S(?:tring|yntaxError)|E(?:rror|valError)|TypeError|URIError)" . $keywordend
                ),
                array(
                    'token' => "language.support.function",
                    'regex' => "(?:Math|JSON|is(?:NaN|Finite)|parse(?:Int|Float)|encodeURI(?:Component)?|decodeURI(?:Component)?)" . $keywordend
                ),
                array(
                    'token' => "identifier",
                    'regex' => $identifier
                ),
                array(
                    'token' => "constant.numeric",
                    'regex' => "(?:0x[\\da-fA-F]+|(?:\\d+(?:\\.\\d+)?|\\.\\d+)(?:[eE][+-]?\\d+)?)"
                ),
                array(
                    'token' => "string",
                    'regex' => "'''",
                    'next'  => "qdoc"
                ),
                array(
                    'token' => "string",
                    'regex' => '"""',
                    'next'  => "qqdoc"
                ),
                array(
                    'token' => "string",
                    'regex' => "'",
                    'next'  => "qstring"
                ),
                array(
                    'token' => "string",
                    'regex' => '"',
                    'next'  => "qqstring"
                ),
                array(
                    'token' => "string",
                    'regex' => "`",
                    'next'  => "js"
                ),
                array(
                    'token' => "string.regex",
                    'regex' => "///",
                    'next'  => "heregex"
                ),
                array(
                    'token' => "string.regex",
                    'regex' => "/(?!\\s)[^[/\\n\\\\]*(?: (?:\\\\.|\\[[^\\]\\n\\\\]*(?:\\\\.[^\\]\\n\\\\]*)*\\])[^[/\\n\\\\]*)*/[imgy]{0,4}(?!\\w)"
                ),
                array(
                    'token' => "comment",
                    'regex' => "###(?!#)",
                    'next'  => "comment"
                ),
                array(
                    'token' => "comment",
                    'regex' => "#.*"
                ),
                array(
                    'token' => "lparen",
                    'regex' => "[({[]"
                ),
                array(
                    'token' => "rparen",
                    'regex' => "[\\]})]"
                ),
                array(
                    'token' => "keyword.operator",
                    'regex' => "\\S+"
                ),
                array(
                    'token' => "text",
                    'regex' => "\\s+"
                )
            ), // start

            'qdoc' => array(
                array(
                    'token' => "string",
                    'regex' => ".*?'''",
                    'next'  => "start"
                ),
                $stringfill

            ), // qdoc

            'qqdoc' => array(
                array(
                    'token' => "string",
                    'regex' => '.*?"""',
                    'next'  => "start"
                ),
                $stringfill
            ), // qqdoc

            'qstring' => array(
                array(
                    'token' => "string",
                    'regex' => "[^\\\\']*(?:\\\\.[^\\\\']*)*'",
                    'next'  =>  "start"
                ),
                $stringfill
            ), // qstring

            'qqstring' => array(
                array(
                    'token' => "string",
                    'regex' => '[^\\\\"]*(?:\\\\.[^\\\\"]*)*"',
                    'next'  => "start"
                ),
                $stringfill
            ), // qqstring

            'js' => array(
                array(
                    'token' => "string",
                    'regex' => "[^\\\\`]*(?:\\\\.[^\\\\`]*)*`",
                    'next'  => "start"
                ),
                $stringfill
            ), // js

            'heregex' => array(
                array(
                    'token' => "string.regex",
                    'regex' => '.*?///[imgy]{0,4}',
                    'next'  => "start"
                ),
                array(
                    'token' => "comment.regex",
                    'regex' => "\\s+(?:#.*)?"
                ),
                array(
                    'token' => "string.regex",
                    'regex' => "\\S+"
                ),
            ), //heregex

            'comment' => array(
                array(
                    'token' => "comment",
                    'regex' => '.*?###',
                    'next'  => "start"
                ),
                array(
                    'token' => "comment",
                    'regex' => ".+"
                ),
            ), //comment

        ), // rules

    ), // xml
);

