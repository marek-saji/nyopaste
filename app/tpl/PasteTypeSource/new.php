<?php
/**
 * Paste with source code, add new form.
 * @author m.augustynowicz
 */
$form = g('Forms', array('paste', $this));

$v->addJs($this->file('type-new', 'js'));
?>

<ul>

    <li class="field">
        <?php
        $form->label('syntax', 'language');
        ?>
        <?php
        $form->input('syntax', array('values' => $syntax_values));
        ?>
        <div class="help">
            <p><?=$t->trans('Syntax paste as it\'s written in this language.')?></p>
            <p><?=$t->trans('If your language is not here, but it\'s kind of C-like, C is always a good option.')?></p>
        </div>
    </li>

</ul>

<?php
$less_important_options_json = htmlspecialchars(json_encode(array(
    'expand' => $this->trans('show options including hiding line numbers and syntax hightlight colour scheme'),
    'collapse' => $this->trans('it\'s too cluttered, hide these options'),
)), ENT_QUOTES);
?>
<ul class="less-important-options" data-less-important-options='<?=$less_important_options_json?>'>

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

    <li class="field">
        <?php
        $form->label('colour_scheme', 'syntax hightlight colour scheme');
        ?>
        <?php
        $form->input('colour_scheme', array('values' => $colour_scheme_values));
        ?>
        <noscript>
            <p class="nojs advice description">
                <?=$this->trans('With JavaScript available, changing this would also set the scheme in the editor above. Try enabling JavaScript, if you can.')?>
            </p>
        </noscript>
    </li>

</ul>

