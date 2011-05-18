<?php
/**
 * Rules for highlighting in Python mode.
 * @author m.augustynowicz ported from javascript
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/python_highlight_rules.js
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
 *      Colin Gourlay <colin DOT j DOT gourlay AT gmail DOT com>
 *
 * TODO: python delimiters
 */
$strPre = "(?:r|u|ur|R|U|UR|Ur|uR)?";

$decimalInteger = "(?:(?:[1-9]\\d*)|(?:0))";
$octInteger = "(?:0[oO]?[0-7]+)";
$hexInteger = "(?:0[xX][\\dA-Fa-f]+)";
$binInteger = "(?:0[bB][01]+)";
$integer = "(?:" . $decimalInteger . "|" . $octInteger . "|" . $hexInteger . "|" . $binInteger . ")";

$exponent = "(?:[eE][+-]?\\d+)";
$fraction = "(?:\\.\\d+)";
$intPart = "(?:\\d+)";
$pointFloat = "(?:(?:" . $intPart . "?" . $fraction . ")|(?:" . $intPart . "\\.))";
$exponentFloat = "(?:(?:" . $pointFloat . "|" .  $intPart . ")" . $exponent . ")";
$floatNumber = "(?:" . $exponentFloat . "|" . $pointFloat . ")";

$conf['source_code_highlighter']['modes'] = array(

    'python' => array(

        'rules' => array(

            'start' => array(
                array(
                    'token' => "comment",
                    'regex' => "#.*"
                ), array(
                    'token' => "string",           // """ string
                    'regex' => $strPre . '"{3}(?:[^\\\\]|\\\\.)*?"{3}'
                ), array(
                    'token' => "string",           // multi line """ string start
                    'regex' => $strPre . '"{3}.*',
                    'next' => "qqstring"
                ), array(
                    'token' => "string",           // " string
                    'regex' => $strPre . '"(?:[^\\\\]|\\\\.)*?"'
                ), array(
                    'token' => "string",           // ''' string
                    'regex' => $strPre . "'{3}(?:[^\\\]|\\\.)*?'{3}"
                ), array(
                    'token' => "string",           // multi line ''' string start
                    'regex' => $strPre . "'{3}.*",
                    'next' => "qstring"
                ), array(
                    'token' => "string",           // ' string
                    'regex' => $strPre . "'(?:[^\\\\]|\\\\.)*?'"
                ), array(
                    'token' => "constant.numeric", // imaginary
                    'regex' => "(?:" . $floatNumber . "|\\d+)[jJ]\\b"
                ), array(
                    'token' => "constant.numeric", // float
                    'regex' => $floatNumber
                ), array(
                    'token' => "constant.numeric", // long integer
                    'regex' => $integer . "[lL]\\b"
                ), array(
                    'token' => "constant.numeric", // integer
                    'regex' => $integer . "\\b"
                ), array(
                    'token' => "keyword",
                    'regex' => "and|as|assert|break|class|continue|def|del|elif|else|except|exec|finally|for|from|global|if|import|in|is|lambda|not|or|pass|print|raise|return|try|while|with|yield"
                ), array(
                    'token' => "constant.language",
                    'regex' => "True|False|None|NotImplemented|Ellipsis|__debug__"
                ), /*array(
                    'token' => "invalid.illegal",
                    'regex' => ''
                ),*/ array(
                    'token' => "support.function",
                    'regex' => "abs|divmod|input|open|staticmethod|all|enumerate|int|ord|str|any|eval|isinstance|pow|sum|basestring|execfile|issubclass|print|super|binfile|iter|property|tuple|bool|filter|len|range|type|bytearray|float|list|raw_input|unichr|callable|format|locals|reduce|unicode|chr|frozenset|long|reload|vars|classmethod|getattr|map|repr|xrange|cmp|globals|max|reversed|zip|compile|hasattr|memoryview|round|__import__|complex|hash|min|set|apply|delattr|help|next|setattr|buffer|dict|hex|object|slice|coerce|dir|id|oct|sorted|intern"
                ), array(
                    'token' => "invalid.deprecated",
                    'regex' => 'debugger',
                ), array(
                    'token' => "identifier",
                    'regex' => "[a-zA-Z_$][a-zA-Z0-9_$]*\\b"
                ), array(
                    'token' => "keyword.operator",
                    'regex' => "\\+|\\-|\\*|\\*\\*|/|//|%|<<|>>|&|\\||\\^|~|<|>|<=|=>|==|!=|<>|="
                ), array(
                    'token' => "lparen",
                    'regex' => "[\\[\\(\\{]"
                ), array(
                    'token' => "rparen",
                    'regex' => "[\\]\\)\\}]"
                ), array(
                    'token' => "text",
                    'regex' => "\\s+"
                )
            ),
            "qqstring" => array(
                array(
                    'token' => "string", // multi line """ string end
                    'regex' => '(?:[^\\\\]|\\\\.)*?"{3}',
                    'next'  => "start"
                ), array(
                    'token' => "string",
                    'regex' => '.+'
                ),
            ),

            "qstring" => array(
                array(
                    'token' => "string",           // multi line ''' string end
                    'regex' => "(?:[^\\\\]|\\\\.)*?'{3}",
                    'next' => "start"
                ), array(
                    'token' => "string",
                    'regex' => '.+'
                )

            ),
        ), // rules

    ), // python
);

