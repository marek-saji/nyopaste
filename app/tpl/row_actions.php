<?php
/**
 * Links to actions that can be performed on a row
 *
 * @param array $actions usually assigned to $row[Actions]
 *        it is expected to look like this:
 *        array(ACTION => PERMITTED)
 *        PERMITTED can be either an array describing URL to the action
 *        (will get passed to url2c), or boolean. false does not render
 *        the action, whereas true renders link to action with the same
 *        parameters as current action
 */
$actions = (array) $actions;
$name = $t->getName();
@$____local_variables['class'] .= ' ' . $name . ' actions';
unset($____local_variables['actions']);
$attrs = $f->xmlAttr($____local_variables);
?>

<nav <?=$attrs?>>
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
                else
                {
                    echo $t->l2c($label, $value);
                }
                ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

