<?php
/**
 * Paste with source code, render contents
 * @author m.augustynowicz
 */
if ($row['syntax'])
{
    $v->setDescription($this->trans(
        'a %s paste by %s',
        $row['syntax'],
        $row['Paster'] ? $row['Paster']['DisplayName'] : $row['paster']
    ));
}

$v->addJs($this->file('default', 'js'));
$v->addLess($this->file('default', 'less'));

$line_numbers = $f->anyToBool($row['line_numbers']);
$class = $line_numbers ? 'line_no ' : '';
$class .= $row['colour_scheme'];

$highlighter = g('SourceCodeHighlighter', 'class', array(
    'syntax' => $row['syntax']
));
$lines = explode("\n", $highlighter->highlight($row['content']));

$v->addCss($this->file('themes/'.$row['colour_scheme'], 'css'));
?>

<?php if (g()->debug->on()) : ?>
<style type="text/css">
.UNCLASIFIED { font-weight: bold; color: red; }
</style>
<?php endif; ?>

<div class="wrapper <?=$class?>">
    <?php
    $data = array(
        'texts' => array(
            'lineLink' => $this->trans('link to line %%d')
        )
    );
    $attrs = array(
        'class'      => 'content ace_scroller ace_text-layer',
        'data-paste' => json_encode($data)
    );
    ?>
    <pre <?=$f->xmlAttr($attrs)?>><?php foreach ($lines as $i => $line) : ?><code id="line<?=$i?>" class="line ace_line"><?=$line?></code>
<?php endforeach; ?></pre>
</div>

