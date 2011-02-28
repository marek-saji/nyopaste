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
                'colour_scheme' => array(
                    '_tpl' => 'Forms/FSelect'
                ),
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
        $conf = &g()->conf['paste_types']['source'];

        $modes = array_keys($conf['modes']);
        $this->assign('syntax_values', array_combine($modes, $modes));

        $this->assign('colour_scheme_values', $conf['themes']);

        $this->data['paste'] =
            (isset($this->data['paste']) ? (array)$this->data['paste'] : array())
            +
            array(
                'syntax' => $conf['default mode'],
                'colour_scheme' => $conf['default theme']
            );
    }


    /**
     * @url http://etherpad.mozilla.com:9000/ep/pad/view/oSW4EWUuOX/U5pPuVBBaT
     */
    public function highlight($text, $syntax, $class_prefix='ace_', $decode_entities=true)
    {
        if ($decode_entities)
        {
            $text = html_entity_decode($text);
        }

        g()->readConfigFiles('paste_type_source', 'conf.rules.'.$syntax);
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
        $unclasified = 0;
        while ($text)
        {
            $len = mb_strlen($text);
            foreach($rules[$state] as &$rule)
            {
                if (preg_match("/^(?:{$rule['regex']})/", $text, $match))
                {
                    $class = $rule['token'];
                    $class = preg_replace('/\./', ' '.$class_prefix, $class);

                    $attrs = array(
                        'class' => $class_prefix . $class
                    );
                    if (g()->debug->on('paste'))
                    {
                        $attrs['title'] = $attrs['class'] . '; state: '.$state;
                    }
                    $open_tag  = $f->tag('span', $attrs, null, 'open');
                    $close_tag = $f->tag('span', $attrs, null, 'close');
                    $line = htmlspecialchars($match[0]);
                    $line = preg_replace("/\n/", "{$close_tag}\n{$open_tag}", $line);
                    $tokenized_text .= $open_tag . $line . $close_tag;
                    $text = mb_substr($text, mb_strlen($match[0]));
                    if (isset($rule['next']))
                    {
                        $state = $rule['next'];
                        continue(2);
                    }
                    break;
                }
            }
            if ($len == mb_strlen($text))
            {
                $tokenized_text .= $f->tag(
                    'span',
                    array('class' => 'UNCLASIFIED'),
                    htmlspecialchars(mb_substr($text, 0, 1))
                );
                $text = mb_substr($text, 1);
                $unclasified++;
            }
            unset($rule);
        }

        return $tokenized_text;
    }

}

