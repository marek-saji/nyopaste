<?php
/**
 * Rules for highlighting in JavaScript mode.
 * @author m.augustynowicz ported from javascript
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/javascript_highlight_rules.js
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
 *      Mihai Sucan <mihai DOT sucan AT gmail DOT com>
 */

$doc_comment = require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'doc_comment.php');

$conf['source_code_highlighter']['modes'] = array(

    'javascript' => array(

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
                ), array(
                    'token' => "string.regexp",
                    'regex' => "[/](?:(?:\\[(?:\\\\]|[^\\]])+\\])|(?:\\\\/|[^\\]/]))*[/]\\w*\\s*(?=[).,;]|$)"
                ), array(
                    'token' => "string", // single line
                    'regex' => '["](?:(?:\\\.)|(?:[^"\\\\]))*?["]'
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
                    'regex' => "0[xX][0-9a-fA-F]+\\b"
                ), array(
                    'token' => "constant.numeric", // float
                    'regex' => "[+-]?\\d+(?:(?:\\.\\d*)?(?:[eE][+-]?\\d+)?)?\\b"
                ), array(
                    'token' => "constant.language.boolean",
                    'regex' => "(?:true|false)\\b"
                ), array(
                    'token' => "variable.language",
                    'regex' => "this",
                ), array(
                    'token' => "keyword",
                    'regex' => "break|case|catch|continue|default|delete|do|else|finally|for|function|if|in|instanceof|new|return|switch|throw|try|typeof|let|var|while|with|const|yield|import|get|set",
                ), array(
                    'token' => "constant.language",
                    'regex' => "null|Infinity|NaN|undefined"
                ), array(
                    'token' => "invalid.illegal",
                    'regex' => "class|enum|extends|super|export|implements|private|public|interface|package|protected|static"
                ), array(
                    'token' => "invalid.deprecated",
                    'regex' => "debugger"
                ), array(
                    'token' => "identifier",
                    // TODO: Unicode escape sequences
                    // TODO: Unicode identifiers
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
                    'token' => "comment",
                    'regex' => "^#!.*$" 
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
                    'next'  => "start"
                ), array(
                    'token' => "string",
                    'regex' => '.+'
                )
            ),

            "qstring" => array(
                array(
                    'token' => "string",
                    'regex' => "(?:(?:\\\\.)|(?:[^'\\\\]))*?'",
                    'next'  => "start"
                ), array(
                    'token' => "string",
                    'regex' => '.+'
                )
            )

        ) // rules

    ) // javascript

); // modes

$conf['source_code_highlighter']['modes']['javascript']['rules'] += $doc_comment['rules'];

