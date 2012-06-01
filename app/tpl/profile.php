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

<?php
$class = "vcard {$row['Type']} profile ";
if (@$row['IsGroup'])
{
    $class .= 'group';
}
else
{
    $class .= 'user';
}
?>

<section class="<?=$class?>">
        <header>
            <h2>
                <div class="avatar">
                    <?php
                    $size = 128;
                    ?>
                    <?php if (@$row['IsGroup']) : ?>
                        <?php
                        echo $f->tag(
                            'object',
                            array(
                                'data'   => $this->file('group.svgz', 'gfx'),
                                'type'   => 'image/svg+xml',
                                'width'  => $size,
                                'height' => $size,
                                'class'  => 'photo'
                            ),
                            $f->tag(
                                'img',
                                array(
                                    'src'    => $this->file('group.png', 'gfx'),
                                    'width'  => $size,
                                    'height' => $size,
                                    'class'  => 'photo',
                                    'alt'    => ''
                                )
                            )
                        );
                        ?>
                    <?php else : ?>
                        <?php
                        $gravatar_url = sprintf(
                            'http://gravatar.com/avatar/%s?%s',
                            md5($row['email']),
                            http_build_query(array(
                                's' => $size,
                                'd' => 'identicon'
                            ))
                        );
                        ?>
                        <?php
                        echo $f->tag(
                            'img',
                            array(
                                'src'    => $gravatar_url,
                                'class'  => 'photo',
                                'alt'    => ''
                            )
                        );
                        ?>
                    <?php endif; /* $row['email'] */ ?>
                </div>

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
                <?php else : ?>
                    <dt>
                        <?=$this->trans('Members')?>
                        <?php if (@$row['MembersCount']) : ?>
                            (<?=$row['MembersCount']?>)
                        <?php endif; /* $ow['MembersCount'] */ ?>

                    </dt>
                    <dd>
                        <?php
                        $this->getChild('Users')->render();
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

