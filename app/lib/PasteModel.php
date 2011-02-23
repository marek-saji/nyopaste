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
    public function __construct()
    {
        parent::__construct();

        $this->_addField(new FId('id'));
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


}

