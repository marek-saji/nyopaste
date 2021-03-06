<?php
/**
 * Rules for highlighting in C/C++ mode.
 * @author m.augustynowicz ported from javascript
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/c_cpp_highlight_rules.js
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
 *      Fabian Jakobs <fabian AT ajax DOT org>
 *      Gastón Kleiman <gaston.kleiman AT gmail DOT com>
 *
 * Based on Bespin's C/C++ Syntax Plugin by Marc McIntyre.
 */

$doc_comment = require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'doc_comment.php');

$conf['source_code_highlighter']['modes'] = array(

    'c_cpp' => array(

        'rules' => array(

            'start' => array(

                array(
                    'token' => "comment",
                    'regex' => "//.*"
                ),

                $doc_comment['start_rule'],

                array(
                    'token' => "comment", // multi line comment
                    'regex' => "/\\*",
                    'next' => "comment"
                ),
                array(
                    'token' => "string", // single line
                    'regex' => '["](?:(?:\\\\.)|(?:[^"\\\\]))*?["]'
                ),
                array(
                    'token' => "string", // multi line string start
                    'regex' => '["].*\\\\$',
                    'next' => "qqstring"
                ),
                array(
                    'token' => "string", // single line
                    'regex' => "['](?:(?:\\\\.)|(?:[^'\\\\]))*?[']"
                ),
                array(
                    'token' => "string", // multi line string start
                    'regex' => "['].*\\\\$",
                    'next' => "qstring"
                ),
                array(
                    'token' => "constant.numeric", // hex
                    'regex' => "0[xX][0-9a-fA-F]+\\b"
                ),
                array(
                    'token' => "constant.numeric", // float
                    'regex' => "[+-]?\\d+(?:(?:\\.\\d*)?(?:[eE][+-]?\\d+)?)?\\b"
                ),
                array(
                    'token' => "constant", // <CONSTANT>
                    'regex' => "<[a-zA-Z0-9.]+>"
                ),
                array(
                    'token' => "keyword", // pre-compiler directivs
                    'regex' => "(?:#include|#pragma|#line|#define|#undef|#ifdef|#else|#elif|#endif|#ifndef)"
                ),
                array(
                    'token' => "variable.language",
                    'regex' => 'this'
                ),
                array(
                    'token' => "keyword",
                    'regex' => "and|double|not_eq|throw|and_eq|dynamic_cast|operator|true|asm|else|or|try|auto|enum|or_eq|typedef|bitand|explicit|private|typeid|bitor|extern|protected|typename|bool|false|public|union|break|float|register|unsigned|case|fro|reinterpret-cast|using|catch|friend|return|virtual|char|goto|short|void|class|if|signed|volatile|compl|inline|sizeof|wchar_t|const|int|static|while|const-cast|long|static_cast|xor|continue|mutable|struct|xor_eq|default|namespace|switch|delete|new|template|do|not|this|for"
                ),
                array(
                    'token' => "constant.language",
                    'regex' => "NULL",
                ),
                array(
                    'token' => "identifier",
                    'regex' => "[a-zA-Z_$][a-zA-Z0-9_$]*\\b"
                ),
                array(
                    'token' => "keyword.operator",
                    'regex' => "!|\\$|%|&|\\*|\\-\\-|\\-|\\+\\+|\\+|~|==|=|!=|<=|>=|<<=|>>=|>>>=|<>|<|>|!|&&|\\|\\||\\?\\:|\\*=|%=|\\+=|\\-=|&=|\\^=|\\b(?:in|new|delete|typeof|void)"
                ),
                array(
                    'token' => "lparen",
                    'regex' => "[[({]"
                ),
                array(
                    'token' => "rparen",
                    'regex' => "[\\])}]"
                ),
                array(
                    'token' => "text",
                    'regex' => "\\s+"
                ),
            ),

            'comment' => array(
                array(
                    'token' => "comment", // closing comment
                    'regex' => ".*?\\*/",
                    'next' => "start"
                ),
                array(
                    'token' => "comment", // comment spanning whole line
                    'regex' => ".+"
                ),
            ),

            'qqstring' => array(
                array(
                    'token' => "string",
                    'regex' => '(?:(?:\\\\.)|(?:[^"\\\\]))*?"',
                    'next' => "start"
                ),
                array(
                    'token' => "string",
                    'regex' => '.+'
                ),
            ),

            'qstring' => array(
                array(
                    'token' => "string",
                    'regex' => "(?:(?:\\\\.)|(?:[^'\\\\]))*?'",
                    'next' => "start"
                ),
                array(
                    'token' => "string",
                    'regex' => '.+'
                ),
            ),

        ), // rules

    ), // xml
);

$conf['source_code_highlighter']['modes']['c_cpp']['rules'] += $doc_comment['rules'];

