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
                    <?=$t->l2a(
                        sprintf(
                            '%s %s',
                            $row['title'],
                            $f->tag('span', array('class'=>'version'), $this->trans('v%s', $row['version']))
                        ),
                        '',
                        array($row['url'], 'v'=>$row['version']),
                        array('class' => 'entry-title')
                    )?>
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

