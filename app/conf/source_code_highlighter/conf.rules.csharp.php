<?php
/**
 * Rules for highlighting in C# mode.
 * @author m.augustynowicz ported from javascript
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/csharp_highlight_rules.js
 */
$doc_comment = require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'doc_comment.php');

$conf['source_code_highlighter']['modes'] = array(

    'csharp' => array(

        'rules' => array(

            "start" => array(
                array(
                    'token' => "comment",
                    'regex' => "//.*$"
                ),

                $doc_comment['start_rule'],

                array(
                    'token' => "comment", // multi line comment
                    'regex' => "/\\*",
                    'next'  => "comment"
                ), array(
                    'token' => "comment", // multi line comment
                    'regex' => "/\\*\\*",
                    'next'  => "comment"
                ), array(
                    'token' => "string.regexp",
                    'regex' => "[/](?:(?:\\[(?:\\\\]|[^\\]])+\\])|(?:/|[^\\]/]))*[/]\\w*\\s*(?=[).,;]|$)"
                ), array(
                    'token' => "string", // single line
                    'regex' => '["](?:(?:\\\\.)|(?:[^"\\\\]))*?["]'
                ), array(
                    'token' => "string", // single line
                    'regex' => "['](?:(?:\\\\.)|(?:[^'\\\\]))*?[']"
                ), array(
                    'token' => "constant.numeric", // hex
                    'regex' => "0[xX][0-9a-fA-F]+\\b"
                ), array(
                    'token' => "constant.numeric", // float
                    'regex' => "[+-]?\\d+(?:(?:\\.\\d*)?(?:[eE][+-]?\\d+)?)?\\b"
                ), array(
                    'token' => "constant.language.boolean",
                    'regex' => "(?:true|false)\\b"
                ), array(
                    'token' => "variable.language",
                    'regex' => 'this'
                ), array(
                    'token' => 'keyword',
                    'regex' => "abstract|event|new|struct|as|explicit|null|switch|base|extern|object|this|bool|false|operator|throw|break|finally|out|true|byte|fixed|override|try|case|float|params|typeof|catch|for|private|uint|char|foreach|protected|ulong|checked|goto|public|unchecked|class|if|readonly|unsafe|const|implicit|ref|ushort|continue|in|return|using|decimal|int|sbyte|virtual|default|interface|sealed|volatile|delegate|internal|short|void|do|is|sizeof|while|double|lock|stackalloc|else|long|static|enum|namespace|string|var|dynamic"
                ), array(
                    'token' => "constant.language"
                    'regex' => "null|true|false"
                ), array(
                    'token' => "identifier",
                    'regex' => "[a-zA-Z_$][a-zA-Z0-9_$]*\\b"
                ), array(
                    'token' => "keyword.operator",
                    'regex' => "!|\\$|%|&|\\*|\\-\\-|\\-|\\+\\+|\\+|~|===|==|=|!=|!==|<=|>=|<<=|>>=|>>>=|<>|<|>|!|&&|\\|\\||\\?\\:|\\*=|%=|\\+=|\\-=|&=|\\^=|\\b(?:in|instanceof|new|delete|typeof|void)"
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

            "comment" => array(
                array(
                    'token' => "comment", // closing comment
                    'regex' => ".*?\\*/",
                    'next'  => "start"
                ), array(
                    'token' => "comment", // comment spanning whole line
                    'regex' => ".+"
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
        )
    )
);

$conf['source_code_highlighter']['modes']['csharp']['rules'] += $doc_comment['rules'];

