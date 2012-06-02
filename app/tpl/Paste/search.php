<?php
/**
 * Listing search results.
 */

if ($query)
{
    $title = $this->trans('"%s" search results', $query);
}
else
{
    $title = $this->trans('All pastes');
}
$v->setTitle($title);
$v->setMeta('og:type', 'website', 'property');
$v->setMeta('og:url', $this->url2a('search', $this->getParams(), true), 'property');

$v->addLess($this->file('search', 'less'));
?>
<section>
    <h2><?=$title?></h2>

    <?php
    $form = g('Forms', array('search', $this));
    ?>
    <div class="holoform search wide">

        <?php
        $form->create();
        ?>

        <fieldset>
            <ul>
                <li class="field">
                    <?php
                    $form->label('query', 'search');
                    ?>
                    <?php
                    $form->input('query', array(
                        'ajax' => false, // do not validate
                        'attrs' => array(
                            'placeholder' => 'search for a paste',
                            'type'        => 'search',
                            'autosave'    => 'search'
                        )
                    ));
                    ?>

                    <div class="help">
                        <ul>
                            <li><?=$this->trans('Use <code>user:username</code> to search for only user\'s pastes and <code>tag:"a tag"</code> to search within tags.')?></li>
                            <li><?=$this->trans('Use <code>OR</code> and <code>NOT</code> operators and brackets to build even more advanced queries.')?></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </fieldset>

        <?php
        $this->inc(
            'Forms/buttons',
            array(
                'form'   => $form,
                'submit' => 'search pastes',
                'cancel' => false
            )
        );
        ?>

        <?php
        $form->end();
        ?>

    </div> <!-- .holoform -->

    <?php if (empty($rows)) : ?>
        <ol id="content" class="empty pastes">
            <li>
                <p><strong><?=$this->trans('No results. Try modyfying your search query.')?></strong></p>
            </li>
        </ol>
    <?php else : ?>
        <ol id="content" class="pastes hfeed" start="<?=$this->getChild('p')->getFirstItemIndex()?>">
            <?php
            foreach ($rows as & $row) :
            ?>
                <li class="hentry">
                    <h4>
                        <?=$t->l2a(
                            $row['title'],
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
                    ?>
                </li>
            <?php
            endforeach;
            ?>
        </ol>
        <?php
        $this->getChild('p')->render();
        ?>
    <?php endif; /* else empty rows */ ?>
</section>

