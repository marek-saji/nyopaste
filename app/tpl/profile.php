<?php
/**
 * User's or Group's profile
 * @author m.augustynowicz
 *
 * @param array $row one row data
 */

$title = $row['DisplayName'];
$v->setTitle($title);

$v->addLess($this->file('profile', 'less'));
?>

<section id="content">

    <section class="vcard <?=$row['Type']?>">
        <header>
            <h2>
                <?php if (@$row['email']) : ?>
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
                                'width'  => $gravatar_size,
                                'height' => $gravatar_size,
                                'class'  => 'photo'
                                // size unspecified to make avatar scalable with css
                            )
                        );
                        ?>
                    </div>
                <?php endif; /* $row['email'] */ ?>

                <span class="fn">
                    <?=$title?>
                </span>

            </h2>
        </header>

        <div class="meta">

            <?php
            $t->inc('row_actions', array(
                'actions' => & $row['Actions']
            ));
            ?>

            <dl>

                <?php if (@$row['creation']) : ?>
                    <dd>
                        <?php if (@$row['IsGroup']) : ?>
                            <?=$this->trans('created %s', $row['DisplayCreation'])?>
                        <?php else : ?>
                            <?=$this->trans('member since %s', $row['DisplayCreation'])?>
                        <?php endif; /* $row is group */ ?>
                    </dd>
                <?php endif; /* $ow['creation'] */ ?>

                <?php if (@$row['MembersCount']) : ?>
                    <dd>
                        <?=$this->trans('%d member(s)', $row['MembersCount'])?>
                    </dd>
                <?php endif; /* $ow['MembersCount'] */ ?>

                <?php if ($row['Type']) : ?>
                    <dt class="assumed">
                        <?=$this->trans('new members policy')?>
                    </dt>
                    <dd>
                        <?=$this->trans($row['Type'])?>
                    </dd>
                <?php endif; /* $row['Type' */ ?>

                <?php if (trim($row['DisplayDescription'])) : ?>
                    <!-- about me -->
                    <dt class="assumed">
                        <?=$t->trans('something about you')?>
                    </dt>
                    <dd>
                        <?=$row['DisplayDescription']?>
                    </dd>
                <?php endif; ?>

                <?php if (trim(@$row['website'])) : ?>
                    <!-- website -->
                    <dt class="assumed">
                        <?=$t->trans('website')?>
                    </dt>
                    <dd>
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

                <?php if ( ! @$row['IsGroup'] ) : ?>
                    <dt>
                        <?=$this->trans('Member of')?>
                    </dt>
                    <dd>
                        <?php
                        $this->getChild('groups')->render();
                        ?>
                    </dd>
                <?php endif; /*  row is not a group  */ ?>

            </dl>

        </div>

    </section>

    <div class="boxes_wrapper">
        <?php
        $this->getChild('Boxes')->render();
        ?>
    </div>

</section>

