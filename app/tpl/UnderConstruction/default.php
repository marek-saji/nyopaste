<style type="text/css">
  .under-construction
  {
    background  : transparent url(../gfx/under-construction.png) scroll center 10px no-repeat;
    padding-top : 140px;
    text-align : center;
  }
  .under-construction p
  {
    font-size   : 1.5em;
  }
</style>

<section class="under-construction">
    <p><?=$this->trans('Sorry, this page is under construction.')?></p>

    <?php
    $referer = g()->req->getReferer();
    ?>
    <?php if ($referer) : ?>
        <a href="<?=$referer?>"><?=$this->trans('go back')?></a>
    <?php endif; /* $referer */ ?>

</section>

