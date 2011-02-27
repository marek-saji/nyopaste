<?php
/**
 *
 * @author m.augustynowicz
 */

$v->addJs($this->file('default', 'js'));
$v->addLess($this->file('default', 'less'));

if ($row['wrap_at'])
{
    if ($f->anyToBool($row['wrap_words']))
        $wrap_func = 'wordwrap';
    else
        $wrap_func = 'chunk_split';
    $row['content'] = $wrap_func($row['content'], $row['wrap_at']);
}

$line_numbers = $f->anyToBool($row['line_numbers']);

$class = $line_numbers ? 'line_no ' : '';

$lines = explode("\n", $row['content']);
?>

<pre class="<?=$class?>"><?php foreach ($lines as $i => $line) : ?><code id="line<?=$i?>" class="line"><?=$line?></code><?php endforeach; ?></pre>

