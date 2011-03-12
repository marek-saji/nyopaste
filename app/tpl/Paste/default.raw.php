<?php
/**
 * Display or download raw paste.
 * @author m.augustynowicz
 *
 * @param string $content text content
 * @param string $filename if given, paste will be served as an attachment
 *               instead of displayed
 */
if (@$filename)
{
    $v->addHeader('Content-Disposition: attachment; filename="'.$filename.'"');
}
?>
<?=htmlspecialchars_decode($content)?>
