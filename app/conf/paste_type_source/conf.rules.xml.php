<?php
/**
 * Rules for highlighting in XML mode.
 * @author m.augustynowicz ported from javascript
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/xml_highlight_rules.js
 */
$conf['paste_types']['source']['modes'] = array(

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
