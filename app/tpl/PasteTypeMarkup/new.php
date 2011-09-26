<?php
$form = g('Forms', array('paste', $this));

$v->addLess($this->file('new', 'less'));
?>

<ul>

    <!-- parser -->
    <li class="field parsers">
        <dl>
            <?php foreach ($parsers as $parser_name) : ?>
                <dt>
                    <?php
                    $form->input(
                        'parser',
                        array(
                            'label' => $this->trans("((parser:$parser_name))"),
                            'value' => $parser_name,
                            'attrs' => array('required' => false)
                        )
                    );
                    ?>
                </dt>
                <dd class="description">
                    <?=$this->trans("((parser:$parser_name:desc))")?>
                </dd>
            <?php endforeach; ?>
        </dl>
    </li>

</ul>

