<?php if ($tags) : ?>
    <ul class="tags">
        <?php foreach ($tags as $tag) : ?>
            <li>
                <?=$tag/*$this->l2c($tag, 'Paste', 'search', array('tag:'.$tag), array('rel'=>'tag'))*/?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

