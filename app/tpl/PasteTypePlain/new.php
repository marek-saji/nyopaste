<?php
$form = g('Forms', array('paste', $this));

$post_data =& $this->data['paste'];

$post_data['do_wrap_at'] = (bool) $post_data['wrap_at'];

$v->addJs($this->file('type-new', 'js'));
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
        <?php
        $form->input('do_wrap_at', array(
            'label' => $this->trans('wrap at')
        ));
        ?>
        <label>
            <?php
            $form->input('wrap_at');
            ?>
        </label>
        <div class="help">
            <p>
                <?=$t->trans('Wrap your text automatically after this column. You can also choose not to break words apart.')?>
            </p>
        </div>
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

