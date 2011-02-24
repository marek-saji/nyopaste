<?php
/**
 * Paste with source code, render contents
 * @author m.augustynowicz
 */

$v->addJs($this->file('default', 'js'));
$v->addLess($this->file('default', 'less'));

$line_numbers = $f->anyToBool($row['line_numbers']);
$class = $line_numbers ? 'line_no' : '';
$lines = explode("\n", $row['content']);
?>

<pre class="<?=$class?>"><?php foreach ($lines as $i => $line) : ?><code id="line<?=$i?>" class="line"><?=$line?></code>
<?php endforeach; ?></pre>

