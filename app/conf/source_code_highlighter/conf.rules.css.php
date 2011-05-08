<?php
/**
 * Rules for highlighting in CSS mode.
 * @author m.augustynowicz ported from javascript
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/css_highlight_rules.js
 */

$numRe = "\\-?(?:(?:[0-9]+)|(?:[0-9]*\\.[0-9]+))";

$conf['source_code_highlighter']['modes'] = array(

    'css' => array(

        'rules' => array(

            "start" => array(

                array(
                    'token' => "comment", // multi line comment
                    'regex' => "/\\*",
                    'next'  => "comment"
                ), array(
                    'token' => "string", // single line
                    'regex' => '["](?:(?:\\\\.)|(?:[^"\\\\]))*?["]'
                ), array(
                    'token' => "string", // single line
                    'regex' => "['](?:(?:\\\\.)|(?:[^'\\\\]))*?[']"
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)em'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)ex'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)px'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)cm'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)mm'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)in'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)pt'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)pc'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)deg'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)rad'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)grad'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)ms'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)s'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)hz'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '(?i)khz'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe . '%'
                ), array(
                    'token' => "constant.numeric",
                    'regex' => $numRe
                ), array(
                    'token' => "constant.numeric",  // hex6 color
                    'regex' => "#[a-fA-F0-9]{6}"
                ), array(
                    'token' => "constant.numeric", // hex3 color
                    'regex' => "#[a-fA-F0-9]{3}"
                ), array(
                    'token' => "lparen",
                    'regex' => "\{"
                ), array(
                    'token' => "rparen",
                    'regex' => "\}"
                ), array(
                    'token' => "support.type",
                    'regex' => "-moz-box-sizing|-webkit-box-sizing|azimuth|background-attachment|background-color|background-image|background-position|background-repeat|background|border-bottom-color|border-bottom-style|border-bottom-width|border-bottom|border-collapse|border-color|border-left-color|border-left-style|border-left-width|border-left|border-right-color|border-right-style|border-right-width|border-right|border-spacing|border-style|border-top-color|border-top-style|border-top-width|border-top|border-width|border|bottom|box-sizing|caption-side|clear|clip|color|content|counter-increment|counter-reset|cue-after|cue-before|cue|cursor|direction|display|elevation|empty-cells|float|font-family|font-size-adjust|font-size|font-stretch|font-style|font-variant|font-weight|font|height|left|letter-spacing|line-height|list-style-image|list-style-position|list-style-type|list-style|margin-bottom|margin-left|margin-right|margin-top|marker-offset|margin|marks|max-height|max-width|min-height|min-width|-moz-border-radius|opacity|orphans|outline-color|outline-style|outline-width|outline|overflow|overflow-x|overflow-y|padding-bottom|padding-left|padding-right|padding-top|padding|page-break-after|page-break-before|page-break-inside|page|pause-after|pause-before|pause|pitch-range|pitch|play-during|position|quotes|richness|right|size|speak-header|speak-numeral|speak-punctuation|speech-rate|speak|stress|table-layout|text-align|text-decoration|text-indent|text-shadow|text-transform|top|unicode-bidi|vertical-align|visibility|voice-family|volume|white-space|widows|width|word-spacing|z-index"
                ), array(
                    'token' => "support.function",
                    'regex' => "rgb|rgba|url|attr|counter|counters"
                ), array(
                    'token' =>  "support.constant",
                    'regex' => "absolute|all-scroll|always|armenian|auto|baseline|below|bidi-override|block|bold|bolder|border-box|both|bottom|break-all|break-word|capitalize|center|char|circle|cjk-ideographic|col-resize|collapse|content-box|crosshair|dashed|decimal-leading-zero|decimal|default|disabled|disc|distribute-all-lines|distribute-letter|distribute-space|distribute|dotted|double|e-resize|ellipsis|fixed|georgian|groove|hand|hebrew|help|hidden|hiragana-iroha|hiragana|horizontal|ideograph-alpha|ideograph-numeric|ideograph-parenthesis|ideograph-space|inactive|inherit|inline-block|inline|inset|inside|inter-ideograph|inter-word|italic|justify|katakana-iroha|katakana|keep-all|left|lighter|line-edge|line-through|line|list-item|loose|lower-alpha|lower-greek|lower-latin|lower-roman|lowercase|lr-tb|ltr|medium|middle|move|n-resize|ne-resize|newspaper|no-drop|no-repeat|nw-resize|none|normal|not-allowed|nowrap|oblique|outset|outside|overline|pointer|progress|relative|repeat-x|repeat-y|repeat|right|ridge|row-resize|rtl|s-resize|scroll|se-resize|separate|small-caps|solid|square|static|strict|super|sw-resize|table-footer-group|table-header-group|tb-rl|text-bottom|text-top|text|thick|thin|top|transparent|underline|upper-alpha|upper-latin|upper-roman|uppercase|vertical-ideographic|vertical-text|visible|w-resize|wait|whitespace|zero"
                ),
                array(
                    'token' => "support.constant.color",
                    'regex' => "aqua|black|blue|fuchsia|gray|green|lime|maroon|navy|olive|orange|purple|red|silver|teal|white|yellow"
                ),
                array(
                    'token' => 'text',
                    'regex' => "\\-?[a-zA-Z_][a-zA-Z0-9_\\-]*"
                )
            ),

            'comment' => array(
                array(
                    'token' => "comment", // closing comment
                    'regex' => ".*?\\*/",
                    'next'  => "start"
                ), array(
                    'token' => "comment", // comment spanning whole line
                    'regex' => ".+"
                )
            )
        )
    )
);

