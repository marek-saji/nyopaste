<?php
/**
 * Render robots.txt
 * @author m.augustynowicz
 */
?>
<?php if (ENVIRONMENT !== PROD_ENV) : ?>
User-agent: *
Disallow: /
<?php else : ?>
User-agent: *
Allow: /
<?php endif; ?>
