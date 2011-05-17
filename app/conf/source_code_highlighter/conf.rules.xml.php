<?php
/**
 * Rules for highlighting in XML mode.
 * @author m.augustynowicz ported from javascript
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/xml_highlight_rules.js
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
$conf['source_code_highlighter']['modes'] = array(

    'xml' => array(

        'rules' => array(

            'start' => array(
                array(
                    'token' => 'text',
                    'regex' => "<\\!\\[CDATA\\[",
                    'next' => 'cdata'
                ),
                array(
                    'token' => "xml_pe",
                    'regex' => "<\\?.*?\\?>"
                ),
                array(
                    'token' => "comment",
                    'regex' => "<\\!--",
                    'next' => "comment"
                ),
                array(
                    'token' => "text", // opening tag
                    'regex' => "<\\/?",
                    'next' => "tag"
                ),
                array(
                    'token' => "text",
                    'regex' => "\\s+"
                ),
                array(
                    'token' => "text",
                    'regex' => "[^<]+"
                ),
            ),

            'tag' => array(
                array(
                    'token' => "text",
                    'regex' => ">",
                    'next' => "start"
                ),
                array(
                    'token' => "keyword",
                    'regex' => "[-_a-zA-Z0-9:]+"
                ),
                array(
                    'token' => "text",
                    'regex' => "\\s+"
                ),
                array(
                    'token' => "string",
                    'regex' => '".*?"'
                ),
                array(
                    'token' => "string",
                    'regex' => "'.*?'"
                ),
            ),

            'cdata' => array(
                array(
                    'token' => "text",
                    'regex' => "\\]\\]>",
                    'next' => "start"
                ),
                array(
                    'token' => "text",
                    'regex' => "\\s+"
                ),
                array(
                    'token' => "text",
                    'regex' => "(?:[^\\]]|\\](?!\\]>))+"
                ),
            ),

            'comment' => array(
                array(
                    'token' => "comment",
                    'regex' => ".*?-->",
                    'next' => "start"
                ),
                array(
                    'token' => "comment",
                    'regex' => ".+"
                ),
            ),

        ), // rules

    ), // xml
);
