<?php
/**
 * Syntax highlight ported from ACE projet (javascript)
 * @author m.augustynowicz
 * @url https://github.com/ajaxorg/ace/blob/master/lib/ace/mode/
 */
class SourceCodeHighlighter
{
    protected $_rules = array();

    /**
     * Loads configs files with mode rules
     * @author m.augustynowicz
     *
     * @param array $options
     *        - [syntax] one of supported modes (syntaxes)
     *
     * @return bool success of operation
     */
    public function __construct($options)
    {
        $mode =& $options['syntax'];

        $conf =& g()->conf['source_code_highlighter']['modes'][$mode];

        if (!isset($conf['rules']))
        {
            g()->readConfigFiles('source_code_highlighter', 'conf.rules.'.$mode);

            if (!isset($conf['rules']))
            {
                trigger_error("Mode {$mode} not defined.", E_USER_WARNING);
                return false;
            }

            foreach ($conf['rules'] as &$state_rules)
            {
                foreach ($state_rules as &$rule)
                {
                    $rule['regex'] = '/^(?:'
                        . str_replace('/', '\/', $rule['regex'])
                        . ')/';
                }
                unset($rule);
            }
            unset($state_rules);
        }

        $this->_rules =& $conf['rules'];

    }


    /**
     * Do highlight
     * @author m.augustynowicz
     * @url http://etherpad.mozilla.com:9000/ep/pad/view/oSW4EWUuOX/U5pPuVBBaT
     *
     * @param string $text source code to highlight
     * @param string $class_prefix HTML class prefix
     * @param bool $decode_entities decode $text before highlightling
     *
     * @return string highlit source code
     */
    public function highlight($text, $class_prefix='ace_', $decode_entities=true)
    {
        if ($decode_entities)
        {
            $text = html_entity_decode($text);
        }

        $f = g('Functions');

        $state = 'start';
        $stack = array();
        $tokenized_text = '';
        $unclasified = 0;
        while ($text)
        {
            $len = mb_strlen($text);
            foreach ($this->_rules[$state] as &$rule)
            {
                if (preg_match($rule['regex'], $text, $match))
                {
                    $classes = $class_prefix .
                        implode(
                            " {$class_prefix}",
                            explode('.', $rule['token'])
                        )
                    ;

                    $attrs = array(
                        'class' => $classes
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
            unset($rule);

            // mark one character as unclasified
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
        }

        return $tokenized_text;
    }

}

