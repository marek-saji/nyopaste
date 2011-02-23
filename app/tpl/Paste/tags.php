<?php if ($tags) : ?>
    <ul class="tags">
        <?php foreach ($tags as $tag) : ?>
            <li>
                <?=$this->l2c($tag, 'Paste', 'search', array('tag:'.$tag), array('rel'=>'tag'))?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

