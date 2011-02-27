<?php
/**
 * Error 404 page for pastes.
 * @author m.augustynowicz
 *
 * @param string $url url of missing paste
 */
$title = $this->trans('Bummer, there is no paste called <q>%s</q>', $url);
$v->setTitle($title);
?>
<section class="error 404">
    <h2>
        <?=$title?>
    </h2>
    <p>
        <?=$this->trans('Maybe try <a href="%s">looking it up</a>.', $this->url2a('search'))?>
    </p>
</section>

