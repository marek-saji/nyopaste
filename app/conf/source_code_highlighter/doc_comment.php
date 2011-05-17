<?php
/**
 * Dcocumentation comments highlight rules.
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/doc_comment_highlight_rules.js
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

