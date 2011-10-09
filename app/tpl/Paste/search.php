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
                            <li><?=$this->trans('Use <code>paster:query</code> and <code>group:query</code> to search for only user/group\'s pastes.')?></li>
                            <li><?=$this->trans('Use brackets and <code>OR</code> operator to build even more advanced queries.')?></li>
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

