<?php
/**
 *
 * @author m.augustynowicz
 */

if ($row['wrap_at'])
{
    if ($f->anyToBool($row['wrap_words']))
        $wrap_func = 'wordwrap';
    else
        $wrap_func = 'chunk_split';
    $row['content'] = $wrap_func($row['content'], $row['wrap_at']);
}
?>

<pre>
<?php
echo $row['content'];
?>
</pre>

