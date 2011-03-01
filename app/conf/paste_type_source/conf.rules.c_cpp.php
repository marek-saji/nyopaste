<?php
/**
 * Rules for highlighting in C/C++ mode.
 * @author m.augustynowicz ported from javascript
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/c_highlight_rules.js
 */

$doc_comment = require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'doc_comment.php');

$conf['paste_types']['source']['modes'] = array(

    'c_cpp' => array(

        'rules' => array(

            'start' => array(

                array(
                    'token' => "comment",
                    'regex' => "\\/\\/.*$"
                ),

                $doc_comment['start_rule'],

                array(
                    'token' => "comment", // multi line comment
                    'regex' => "\\/\\*",
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
                    'regex' => ".*?\\*\\/",
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

$conf['paste_types']['source']['modes']['c_cpp']['rules'] += $doc_comment['rules'];

