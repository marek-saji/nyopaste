<?php
/**
 * Rules for highlighting in C mode.
 * @author m.augustynowicz ported from javascript
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/c_highlight_rules.js
 */
$conf['paste_types']['source']['modes'] = array(

    'c' => array(

        'rules' => array(

            'start' => array(

                array(
                    'token' => "comment",
                    'regex' => "\\/\\/.*$"
                ),

                array(
                    'token' => "comment.doc", // doc comment
                    'regex' => "\\/\\*\\*",
                    'next' => 'doc-start',
                ),

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

            // from doc_comment_highlight_rules.js
            'doc-start' => array(
                array(
                    'token' => "comment.docFOO", // closing comment
                    'regex' => "\\*\\/",
                    'next' => "start"
                ),
                array(
                    'token' => "comment.doc.tag",
                    'regex' => "@[\\w\\d_]+" // TODO: fix email addresses
                ),
                array(
                    'token' => "comment.doc",
                    'regex' => "\s+"
                ),
                array(
                    'token' => "comment.doc",
                    'regex' => "TODO"
                ),
                array(
                    'token' => "comment.doc",
                    'regex' => "[^@\\*]+"
                ),
                array(
                    'token' => "comment.docDOT",
                    'regex' => "."
                ),
            ),

        ), // rules

    ), // xml
);
