<?php
/**
 * Share widget.
 *
 * Tweet, post to facebook, mail ,adthis..
 * @author m.augustynowicz
 *
 * @param string $link permalink to an item
 * @param string $title item title
 * @param string $mail_subject
 * @param string $mail_body
 */
extract(
    array_merge(
        array(
            'link'         => null,
            'title'        => @$____local_variables['link'],
            'mail_subject' => @$____local_variables['title'],
            'mail_body'    => @$____local_variables['link'],
        ),
        (array) $____local_variables
    ),
    EXTR_REFS|EXTR_PREFIX_INVALID, 'param'
);

if (null === $link)
{
    trigger_error('No link passed to ' . __FILE__, E_USER_WARNING);
    return;
}
?>


<nav class="share actions">
    <?php /*
    <script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script>
    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4d9267601252e98f"></script>
    */ ?>
    <ul>

        <li class="addthis twitter action">
            <?php
            $twitter_q = http_build_query(array(
                'text' => $title,
                'url'  => $link // will get shortened by t.co
            ));
            ?>
            <a class="action share twitter addthis_button_twitter" href="https://twitter.com/intent/tweet?<?=$twitter_q?>">tweet</a>
        </li>

        <li class="addthis identica action">
            <?php
            $identica_q = http_build_query(array(
                'action'          => 'newnotice',
                'status_textarea' => sprintf("%s\n%s", $title, $link)
            ));
            ?>
            <a class="action share identica addthis_button_identica" href="http://identi.ca/index.php?<?=$identica_q?>">post to identi.ca</a>
        </li>

        <li class="addthis facebook action">
            <?php
            $facebook_q = http_build_query(array(
                'u' => $link
            ));
            ?>
            <a href="https://www.facebook.com/sharer/sharer.php?<?=$facebook_q?>" class="action share facebook addthis_button_facebook"><?=$this->trans('post to facebook')?></a>
        </li>

        <li class="addthis email action">
            <?php
            $mail_q = http_build_query(array(
                'subject' => $mail_subject,
                'body'    => $mail_body
            ));
            ?>
            <a class="action share mail addthis_button_email" href="mailto:?<?=$mail_q?>"><?=$this->trans('e-mail')?></a>
        </li>

        <li class="addthis compact action">
            <?php
            $addthis_q = http_build_query(array(
                'title' => $row['title'],
                'url'   => $link
            ));
            ?>
            <a class="action share addthis addthis_button_compact" href="http://addthis.com/bookmark.php?<?=$addthis_q?>"><?=$this->trans('choose from over 100 other services <small class="nojs">(requires JavaScript)</small>')?></a>
        </li>

        <li class="copy-to-clipboard action">
            <?php
            //$v->loadJsLib('zeroclipboard');
            ?>
            <p>
                <?=$this->trans('&hellip; or copy link to this paste and do whatever you want')?>
            </p>
            <label class="copyable">
                <?php
                echo $f->tag(
                    'input',
                    array(
                        'type'          => 'text',
                        'class'         => 'copyable',
                        'value'         => $link,
                        'id'            => $f->uniqueId(),
                        'readonly'      => 'readonly',
                        'data-copyable' => json_encode(array(
                            'text'      => $this->trans('copy'),
                            'title'     => $this->trans('click to copy this paste URL to clipboard'),
                            'afterText' => $this->trans('copied!'),
                        ))
                    )
                );
                ?>
            </label>
        </li>

    </ul>
</nav>

