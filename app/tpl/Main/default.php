<?php
/**
 * Main page. "nyopaste" definition, encouragement to sign-up and twitter stream
 * @author m.augustynowicz
 */

$twitter_screenname = 'nyopaste';

$v->addLess($this->file('style','less'));
?>

<section class="definition">

    <dl>
        <dt>
            <dfn>pastebin</dfn>
            (<i class="ipa">pestbɪn</i>),
            also: <dfn>nopaste</dfn>
            (<i class="ipa">nopest</i>)
            &#8212;&nbsp;
        </dt>
        <dd>
            <p>
                <?=$this->trans('<i><abbr title="noun">n.</abbr></i> a site, one can use to share snippets (<i lang="la"><abbr title="id est">i.e.</abbr></i> pastes) of text, without pasting the whole thing into conversation window, thus making it hard to read.')?>
            </p>
        </dd>
        <dt>
            <dfn class="nyopaste">n<i class="kern_ny">y</i>opaste</dfn>
            (<i class="ipa">njopest</i>)
            &#8212;&nbsp;
        </dt>
        <dd>
            <p>
                <?=$this->trans('<i><abbr title="noun">n.</abbr></i> a little more social pastebin. Perfect for brainstorming and collectively collecting pastes.')?>
            </p>
            <p>
                <?=$this->trans('Comes with versioning system, source code hightlight and more.')?>
            </p>
        </dd>
    </dl>

</section>

<?php if (ENVIRONMENT === LOCAL_ENV) : ?>
    @todo if (loggedin) "sign up for"
<?php endif; /* local environment */ ?>

<div class="sign-up-and-nyo-tweets">

    <section class="sign-up">

        <p>
            <?php
            $url = $this->url2c('User', 'new');
            ?>
            <?=$this->trans('<a href="%s">Sign up</a> for:', $url)?>
        </p>

        <ul>
            <li>
                <p><?=$this->trans('quick acces your pastes,')?></p>
            </li>
            <li>
                <p><?=$this->trans('storing your default settings,')?></p>
            </li>
            <li>
                <?php
                $url = $this->url2c('Group');
                ?>
                <p><?=$this->trans('creating new and joining <a href="%s">groups</a>,', $url)?></p>
            </li>
            <li>
                <p><?=$this->trans('customising your public profile')?></p>
                <?=$this->l2c($this->trans('example'), 'User', '', array('admin'));?>
            </li>
        </ul>

        <p>
            <?php
            $url = $this->url2c('Paste', 'new');
            ?>
            <?=$this->trans('or <a href="%s">paste anonymously</a>.', $url)?>
        </p>

    </section>

    <section class="nyo-tweets">

        <?php
        $v->addJs('http://widgets.twimg.com/j/2/widget.js');
        ?>

        <p class="follow">
            <?php
            echo $this->trans(
                '%s to get site updates and such.',
                $f->tag(
                    'a',
                    array(
                        'href'  => 'http://twitter.com/' . $twitter_screenname,
                        // for twitter "follow" widget
                        'class' => 'twitter-follow-button',
                        'data-show-count' => 'false'
                    ),
                    $this->trans('Follow @%s on twitter', $twitter_screenname)
                )
            )
            ?>
        </p>

        <script>
            try
            {
                new TWTR.Widget({
                    version  : 2,
                    type     : 'profile',
                    rpp      : 4,
                    interval : 30000,
                    width    : 250,
                    height   : 300,
                    theme    : {
                        shell : {
                            background : '#666',
                            color      : 'white'
                        },
                        tweets : {
                            background : 'white',
                            color      : 'black',
                            links      : 'blue'
                        }
                    },
                    features : {
                        scrollbar : false,
                        loop      : false,
                        live      : false,
                        hashtags  : true,
                        timestamp : true,
                        avatars   : false,
                        behavior  : 'all'
                    }
                }).render().setUser('<?=$twitter_screenname?>').start();
            }
            catch (e)
            {
                console.error('Failed to load twitter widget.');
            }
        </script>

    </section>

</div> <!-- .sign-up-and-nyo-tweets -->
