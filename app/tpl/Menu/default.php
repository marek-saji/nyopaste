<ul>
    <?php if (g()->debug->allowed()) : ?>
        <li>@todo</li>
    <?php endif; ?>
    <li><?=$this->l2c('nyu paste', 'Paste','new')?></li>
    <?php if (g()->debug->allowed()) : ?>
    <li><?=$this->l2c('pastes', 'Paste','search')?></li>
    <?php endif; ?>
</ul>

