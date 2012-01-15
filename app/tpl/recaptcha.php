<?php
/**
 * Render reCAPTCHA form field, the whole <fieldset />.
 *
 * recaptcha PHP lib *has to* be loaded.
 * @author m.augustynowicz
 *
 * @param string $recaptcha_publickey
 */
extract(
    array_merge(
        array(
            'recaptcha_publickey' => null
        ),
        (array) $____local_variables
    ),
    EXTR_REFS|EXTR_PREFIX_INVALID, 'param'
);

if (empty($recaptcha_publickey) === true)
{
    trigger_error('No reCAPTCHA public key passed to template.', E_USER_NOTICE);
    return;
}
?>

<fieldset class="verbose">
    <legend><?=$this->trans('prove that you are human')?></legend>
    <ul>
        <li class="captcha field" id="recaptcha" style="display: none">
            <script type="text/javascript">
                var RecaptchaOptions = {
                    theme               : "custom",
                    custom_theme_widget : "recaptcha"
                };
            </script>

            <div id="recaptcha_image"></div>
            <p>
                <?=$this->trans('Type in text from the image.')?>
            </p>
            <input type="text" id="recaptcha_response_field" value="" name="recaptcha_response_field" />

            <?=recaptcha_get_html($recaptcha_publickey);?>

            <div class="help">
                <p>
                    <?=$this->trans('To prove you are not an evil robot, complete this challenge.')?>
                </p>
                <p>
                    <?=$this->trans('After signing-in, this will not appear.')?>
                </p>
                <p>
                    <?=$this->trans('Can\'t solve this?')?>
                </p>
                <ul>
                    <li>
                        <a href="javascript:Recaptcha.reload()"><?=$this->trans('try another challenge')?></a>
                    </li>
                    <li class="recaptcha_only_if_image">
                        <a href="javascript:Recaptcha.switch_type('audio')"><?=$this->trans('show audio challenge')?></a>
                    </li>
                    <li class="recaptcha_only_if_audio">
                        <a href="javascript:Recaptcha.switch_type('image')"><?=$this->trans('show image challenge')?></a>
                    </li>
                </ul>
            </div>
            </li>
    </ul>
</fieldset>
