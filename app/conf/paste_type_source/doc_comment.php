<?php
/**
 * Dcocumentation comments highlight rules.
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/doc_comment_highlight_rules.js
 */

return array(

    'start_rule' => array(
        'token' => "comment.doc", // doc comment
        'regex' => "/\\*\\*",
        'next' => 'doc-start',
    ),

    'rules' => array(
        'doc-start' => array(
            array(
                'token' => "comment.doc", // closing comment
                'regex' => "\\*/",
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
                'token' => "comment.doc",
                'regex' => "."
            ),
        ),
    ),

);

