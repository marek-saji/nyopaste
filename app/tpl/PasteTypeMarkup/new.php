<?php
$form = g('Forms', array('paste', $this));
?>

<ul>

    <!-- parser -->
    <li class="field">
        <?php
        $form->input(
            'parser',
            array(
                'label' => $this->trans('markup'),
                'values' => $parsers
            )
        );
        ?>
    </li>

</ul>

