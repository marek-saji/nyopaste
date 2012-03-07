<?php
/**
 * Metalanguages text parser
 * @author m.augustynowicz
 */
class TextParser
{
    protected static $_parsers = array();

    protected $_parser;


    /**
     *
     * @param array $options
     *        - [parser] defined in `conf[parsers]`
     */
    public function __construct($options)
    {
        $parser_name =& $options['parser'];

        if (false === isset(self::$_parsers[$parser_name]))
        {
            $parser = & g()->conf['parsers'][$parser_name];
            if (null === $parser)
            {
                throw new HgException('Unknown parser: '.$parser_name);
            }

            if (@$parser['file'])
            {
                include_once $parser['file'];
            }

            switch ($parser['type'])
            {
                case 'class' :
                    $parser['object'] = new $parser['class'];
                    $parser['parse'] = function ($text) use ($parser) {
                        return $parser['object']->{$parser['method']}($text);
                    };
                    break;
                case 'function' :
                    $parser['parse'] = function ($text) use ($parser) {
                        return $parser['function']($text);
                    };
                    break;
                default :
                    throw HgException('Misconfigured parser: '.$parser_name);
            }

            self::$_parsers[$parser_name] =& $parser;
        }

        $this->_parser =& self::$_parsers[$parser_name];
    }


    /**
     * Parse text with a parser
     * @author m.augustynowicz
     *
     * @param string $string
     *
     * @return string
     */
    public function parse($string)
    {
        return $this->_parser['parse']($string);
    }

}

