<?php
g()->load('DataSets', null);

/**
 * THE paste.
 *
 * Common attributes for all the types.
 * @author m.augustynowicz
 */
class PasteModel extends Model
{
    /**
     * Used as a base for url field suffixes.
     */
    const URL_BASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';

    public function __construct()
    {
        parent::__construct();

        $this->_addField(new FId('id'));
        $this->_addField(new FString('url', true));
        $this->_addField(new FString('title', true));
        $this->_addField(new FMultilineString('content'));

        $this->_addField(new FString('paster'))
                ->auto(array($this, 'autoValPaster'));
        $this->_addField(new FForeignId('paster_id', false, 'User'))
                ->auto(array($this, 'autoValPasterId'));
        $this->relate('Paster', 'User', 'Nto1');

        $this->_addField(new FInt('status', 2, true, STATUS_ACTIVE));

        $this->_addField(new FString('author'));
        $this->_addField(new FURL('source_url'))
            ->mess(array(
                'unsupported protocol' => $this->trans('Please specify one of these protocols: http, https, ftp, gopher.'),
                'syntax error'         => $this->trans('This does not look like a URL.'),
                'no dns record'        => $this->trans('This host name does not exist.'),
            ));

        $this->_addField(new FInt('type_id', 2, true));

        // privacy
        $this->_addField(new FBool('list'));
        $this->_addField(new FMD5String('enc_passwd', false, 3));

        $this->_addField(new FTimestamp('creation'))
                ->auto(array($this, 'autoValCreation'));

        $this->_addField(new FTimestamp('last_edit'));

        $this->_pk('id');
        $this->whiteListAll();
    }


    public function autoValPasterId($action, $field, $value)
    {
        if ('insert' != $action)
        {
            return null;
        }
        else
        {
            return ($val = g()->auth->id()) ? $val : null;
        }
    }


    public function autoValPaster($action, $field, $value)
    {
        if ('insert' != $action)
        {
            return null;
        }
        else
        {
            return ($val = g()->auth->displayName()) ? $val : null;
        }
    }


    public function autoValCreation($action, $field, $value)
    {
        if ('insert' != $action)
        {
            return null;
        }
        else
        {
            return time();
        }
    }


    /**
     * Propose unique url.
     * @author m.augustynowicz
     *
     * @param string $url user value, if it's not unique, will be suffixed
     *        with some garbadge string
     *
     * @return string
     */
    public function uniquifyURL($url='', $title=null)
    {
        $base = self::URL_BASE;
        $base_len = strlen($base)-1;
        if ('' === $url)
        {
            $url = $title;
        }

        $url = iconv('utf-8', 'ascii//translit', $title);
        $url = trim(preg_replace("/[^".preg_quote($base)."]+/", '-', $url), '-');

        while ($this->filter(array('url'=>$url))->getCount())
        {
            $url .= $base[rand(0,$base_len)];
        }

        return $url;
    }
}

