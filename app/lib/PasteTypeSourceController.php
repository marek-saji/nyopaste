<?php
g()->load('PasteTypeSource', 'controller');
/**
 * Paste with source code
 * @author m.augustynowicz
 */
class PasteTypeSourceController extends PasteTypePlainController
{
    protected $_type = 'source';
    protected $_type_idx = 1;

    public $forms = array(
        'paste' => array(
            'model' => 'PasteTypeSource',
            'inputs' => array(
                'syntax' => array(
                    '_tpl' => 'Forms/FSelect'
                ),
                'line_numbers',
                'colour_scheme'
            ) // forms[paste][inputs]
        ) // forms[paste]
    ); // forms


    /**
     * Add new paste.
     *
     * Assign template variables
     * @author m.augustynowicz
     *
     * @param array $params request params:
     *        ignored
     * @return void
     */
    public function actionNew(array $params)
    {
        $modes = array_keys(g()->conf['paste_types']['source']['modes']);
        $this->assign('syntax_values', array_combine($modes, $modes));
    }


    /**
     * @url http://etherpad.mozilla.com:9000/ep/pad/view/oSW4EWUuOX/U5pPuVBBaT
     */
    public function highlight($text, $syntax, $token_class_prefix='token_', $decode_entities=true)
    {
        mb_internal_encoding('utf-8');
        if ($decode_entities)
        {
            $text = html_entity_decode($text);
        }

        g()->readConfigFiles('paste_type_source', 'conf.rules.xml');
        $modes = & g()->conf['paste_types']['source']['modes'];
        if (!isset($modes[$syntax]))
        {
            return htmlspecialchars($text);
        }
        else
        {
            $rules = & $modes[$syntax]['rules'];
        }

        $f = g('Functions');

        $state = 'start';
        $stack = array();
        $tokenized_text = '';
        $parse_errors = 0;
        while ($text)
        {
            $len = mb_strlen($text);
            foreach($rules[$state] as &$rule)
            {
                if (preg_match("/^(?:{$rule['regex']})/", $text, $match))
                {
                    $class = $token_class_prefix . $rule['token'];
                    if (g()->debug->on('paste'))
                    {
                        $class .= ' state_'.$state;
                    }
                    $tokenized_text .= $f->tag(
                        'span',
                        array('class' => $class),
                        htmlspecialchars($match[0])
                    );
                    $text = mb_substr($text, mb_strlen($match[0]));
                    if (isset($rule['next']))
                    {
                        $state = $rule['next'];
                        continue(2);
                    }
                }
            }
            if ($len == mb_strlen($text))
            {
                $tokenized_text .= $f->tag(
                    'span',
                    array('class' => $token_class_prefix . 'PARSE_ERROR'),
                    htmlspecialchars(mb_substr($text, 0, 1))
                );
                $text = mb_substr($text, 1);
                $parse_errors++;
            }
            unset($rule);
        }

        if ($parse_errors)
        {
            trigger_error("Encountered {$parse_errors} parse errors while hightlighting.", E_USER_NOTICE);
        }

        return $tokenized_text;
    }

}

