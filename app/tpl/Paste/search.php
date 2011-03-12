<?php
/**
 * Temporary listing action, for testing new request processing in hologram
 */

?>
<section>
    <h2>@todo title</h2>

    <ol id="content" start="<?=$this->getChild('p')->getFirstItemIndex()?>">
        <?php
        foreach ($rows as & $row) :
        ?>
            <li>
                <?=$t->l2a($row['title'], '', array($row['url'], 'v'=>$row['version']))?>
                <?php
                var_dump($row);
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

