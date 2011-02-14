<?php
/**
 * Temporary listing action, for testing new request processing in hologram
 */

?>
<ol>
    <?php
    foreach ($rows as & $row) :
    ?>
        <li>
            <?=$t->l2a($row['title'], '', array($row['id']))?>
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

