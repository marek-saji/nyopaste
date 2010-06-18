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

$name = $t->getName();
$params = $t->_params;
$actions = (array) $actions;
?>

<nav class="actions">
    <ul>
        <?php
        foreach ($actions as $action => & $value) :
            if (!$value)
                continue;
        ?>
            <li class="<?=$action?> action">
                <?php
                $label = $t->trans(sprintf('((%s:action:%s))', $name, $action));
                $action == $this->_default_action && $action='';
                switch ($action)
                {
                    case 'remove' :
                    case 'restore' :
                    case 'login' :
                        $attrs = array('class'=>'modal');
                        break;
                    default :
                        $attrs = array();
                }
                if (is_array($value))
                    echo $t->l2c($label, $value, array(), $attrs);
                else
                    echo $t->l2a($label, $action, $params, $attrs);
                ?>
            </li>
        <?php
        endforeach;
        ?>
    </ul>
</nav>

