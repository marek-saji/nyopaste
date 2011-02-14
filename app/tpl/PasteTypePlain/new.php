<?php
$form = g('Forms', array('paste', $this));
?>

<ul>

    <!-- line numbers -->
    <li class="field">
        <?php
        $form->input(
            'line_numbers',
            array(
                'label' => $t->trans('show line numbers')
            )
        );
        ?>
    </li>

    <!-- wrap at -->
    <li class="field">
        <label> <!-- @todo for="" -->
            <?=$this->trans('wrap at')?>
            <?php
            $form->input('wrap_at');
            ?>
        </label>
        <fieldset>
            <ul>
                <li class="field">
                    <?php
                    $form->input(
                        'wrap_words',
                        array('label'=>'don\'t split words')
                    );
                    ?>
                </li>
            </ul>
        </fieldset>
    </li>

</ul>

