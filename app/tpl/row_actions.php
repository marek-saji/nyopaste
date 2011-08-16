<?php
/**
 * Links to actions that can be performed on a row
 * @author m.augustynowicz
 *
 * @param array $actions usually assigned to $row[Actions]
 *        it is expected to look like this:
 *        array(ACTION => PERMITTED)
 *        PERMITTED can be either an array describing URL to the action
 *        (will get passed to url2c), or boolean. false does not render
 *        the action, whereas true renders link to action with the same
 *        parameters as current action
 * @param string $name actions' name (used as css class)
 * @param bool $inside_link whether to use `l2cInside` instead of `l2c`
 *
 * Rest of params used as HTML attributes.
 */
$defaults = array(
    'actions'     => array(),
    'name'        => $t->getName(),
    'inside_link' => false
);
extract(
    array_merge(
        $defaults,
        (array) $____local_variables
    ),
    EXTR_REFS|EXTR_PREFIX_INVALID, 'param'
);

$actions = (array) $actions;

$attrs = array_diff_key($____local_variables, $defaults);
@$attrs['class'] .= " {$name} actions";
?>

<nav <?=$f->xmlAttr($attrs)?>>
    <ul>
        <?php foreach ($actions as $action => & $value) : ?>
            <?php
            if (false === $value)
                continue;

            $label_f = '((action:%s:%s))';
            $label = $t->trans($label_f, $action, $name);
            if (sprintf($label_f, $action, $name) == $label)
                $label = $action;

            if (true === $value)
            {
                $params = $this->getParams();
                $value = array($this, $action, $params);
            }
            ?>
            <li class="<?=$action?> action">
                <?php
                $action == $this->_default_action and $action='';
                switch ($action)
                {
                    case 'remove' :
                    case 'restore' :
                    case 'login' :
                        $value[3] = array('class'=>'modal');
                        break;
                    default :
                        $value[3] = array();
                }

                if (true === $value[0])
                {
                    $value['href'] = $value[1];
                    unset($value[0], $value[1], $value[2], $value[3]);
                    echo $f->tag('a', $value, $label);
                }
                else if ($inside_link)
                {
                    echo $t->l2cInside($label, $value);
                }
                else
                {
                    echo $t->l2c($label, $value);
                }
                ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

