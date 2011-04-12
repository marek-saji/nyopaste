<?php
/**
 * Temporary listing action, for testing new request processing in hologram
 */

?>
<section>
    <h2>@todo title</h2>

    <ol id="content" class="hfeed" start="<?=$this->getChild('p')->getFirstItemIndex()?>">
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

