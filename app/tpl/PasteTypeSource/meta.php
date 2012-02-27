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

<?php if ($row['colour_scheme']) : ?>
    <?php
    $colour_scheme = (@g()->conf['paste_types']['source']['themes'][$row['colour_scheme']]) or $colour_scheme = $row['colour_scheme'];
    ?>
<?php endif; ?>

