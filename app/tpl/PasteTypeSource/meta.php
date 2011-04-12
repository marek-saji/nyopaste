<?php
/**
 * Paste with source code, show metadata
 * @author m.augustynowicz
 */
?>

<?php if ($row['syntax']) : ?>
    <?php
    $syntax = (@g()->conf['paste_types']['source']['modes'][$row['syntax']]['name']) or $syntax = $row['syntax'];
    ?>
    <dt><?=$this->trans('type')?></dt>
    <dd><?=$syntax?></dd>
<?php endif; ?>

