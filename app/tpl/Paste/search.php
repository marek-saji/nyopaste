<?php
/**
 * Listing search results.
 */

$title = $this->trans('Pastes');
$v->setTitle($title);
$v->setMeta('og:type', 'website', 'property');
$v->setMeta('og:url', $this->url2a('search', $this->getParams(), true), 'property');
/*
$v->setDescription($this->trans(
    'a paste by %s',
    $row['Paster'] ? $row['Paster']['DisplayName'] : $row['paster']
));
 */

$v->addLess($this->file('search', 'less'));
?>
<section>
    <h2><?=$title?></h2>

    <ol id="content" class="pastes hfeed" start="<?=$this->getChild('p')->getFirstItemIndex()?>">
        <?php
        foreach ($rows as & $row) :
        ?>
            <li class="hentry">
                <h4>
                    <span class="entry-title">
                        <?=$t->l2a($row['title'], '', array($row['url'], 'v'=>$row['version']))?>
                    </span>
                    by <?=$this->inc('paster', $row)?>
                </h4>
                <div class="entry-content">
                    <pre><?=$row['content_excerpt']?></pre>
                </div>
                <?php
                $this->inc('tags', array('tags'=>&$row['Tags']));
                //var_dump($row);
                ?>
            </li>
        <?php
        endforeach;
        ?>
    </ol>
    <?php
    $this->getChild('p')->render();
    ?>
</section>

