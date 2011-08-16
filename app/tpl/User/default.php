<?php
/**
 * User's profile
 * @author m.augustynowicz
 *
 * @param array $row one row data
 */

$title = $row['DisplayName'];
$v->setTitle($title);

$v->addLess($this->file('default', 'less'));
?>

<section id="content">

    <section class="vcard <?=$row['Type']?>">
        <header>
            <h2>
                <?php
                $gravatar_size  = 128;
                $gravatar_url = sprintf(
                    'http://gravatar.com/avatar/%s?%s',
                    md5($row['email']),
                    http_build_query(array(
                        's' => $gravatar_size,
                        'd' => 'identicon'
                    ))
                );
                ?>
                <div class="avatar">
                    <?php
                    echo $f->tag(
                        'img',
                        array(
                            'src'    => $gravatar_url,
                            'class'  => 'photo'
                            // size unspecified to make avatar scalable with css
                        )
                    );
                    ?>
                </div>
                <span class="fn">
                    <?=$title?>
                </span>
                <?php if ($row['Type']) : ?>
                    <small class="user_type">
                        (<?=$this->trans($row['Type'])?>)
                    </small>
                <?php endif; /* $row['Type' */ ?>
            </h2>
        </header>

        <div class="paster-meta">

            <?php
            $t->inc('row_actions', array(
                'actions' => & $row['Actions']
            ));
            ?>

            <dl>
                <?php if (trim($row['website'])) : ?>
                    <!-- website -->
                    <dt class="website text url">
                        <?=$t->trans('website')?>
                    </dt>
                    <dd class="website text url">
                        <?php
                        echo $f->tag(
                            'a',
                            array(
                                'href'   => $row['website'],
                                'target' => '_blank',
                                'class'  => 'url',
                                'rel'    => 'me'
                            ),
                            $row['website']
                        );
                        ?>
                    </dd>
                <?php endif; ?>
                <?php if (trim($row['AboutMe'])) : ?>
                    <!-- about me -->
                    <dt class="about_me text">
                        <?=$t->trans('something about you')?>
                    </dt>
                    <dd class="about_me text user-content">
                        <?=$row['AboutMe']?>
                    </dd>
                <?php endif; ?>
            </dl>

        </div>

    </section>

    <section class="boxes">
        <?php
        $this->getChild('boxes')->render();
        ?>
    </section>

</section>

