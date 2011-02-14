<?php
/**
 * Metalanguages text parser
 * @author m.augustynowicz
 */
class TextParser extends HgBase
{
    protected $_parser_objects = array();


    /**
     * Parse text with a parser
     * @author m.augustynowicz
     *
     * @param string $parser_name defined in conf[parsers]
     * @param string $string
     *
     * @return string
     */
    public function parse($parser_name, $string)
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
            case 'class':
                $obj = & $this->_parser_objects[$parser_name];
                if (null === $obj)
                {
                    $obj = new $parser['class'];
                }
                return $obj->{$parser['method']}($string);
                break;

            case 'method':
                $method = '_' . $parser['method'];
                return $this->$method($string);
                break;

            case 'function':
                $fun = $parser['function'];
                return $fun($string);
                break;

            default :
                throw HgException('Misconfigured parser: '.$parser_name);
        }
    }

}

