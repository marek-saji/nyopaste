<?php
/**
 * Text parsers
 * @author m.augustynowicz
 *
 * Each parser has to have defined type:
 *
 * - class
 *   
 *   Then, [class] and [method] keys are required.
 *
 * - function
 *
 *   Then, [function] key is required.
 *
 * One can define location of a file to include in [file].
 */

$conf['parsers'] = array(
    'markdown' => array(
        'type' => 'class',
        'file' => 'PHPMarkdownExtra/markdown.php',
        'class' => 'MarkdownExtra_Parser',
        'method' => 'transform',
    ),
    'textile' => array(
        'type' => 'class',
        'file' => 'Textile/classTextile.php',
        'class' => 'Textile',
        'method' => 'TextileRestricted',
    ),
);

