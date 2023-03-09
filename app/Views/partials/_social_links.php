<ul>
    <?php if (!empty($baseSettings->facebook_url)): ?>
        <li><a href="<?= esc($baseSettings->facebook_url); ?>" class="facebook" target="_blank"><i class="icon-facebook"></i></a></li>
    <?php endif;
    if (!empty($baseSettings->twitter_url)): ?>
        <li><a href="<?= esc($baseSettings->twitter_url); ?>" class="twitter" target="_blank"><i class="icon-twitter"></i></a></li>
    <?php endif;
    if (!empty($baseSettings->instagram_url)): ?>
        <li><a href="<?= esc($baseSettings->instagram_url); ?>" class="instagram" target="_blank"><i class="icon-instagram"></i></a></li>
    <?php endif;
    if (!empty($baseSettings->pinterest_url)): ?>
        <li><a href="<?= esc($baseSettings->pinterest_url); ?>" class="pinterest" target="_blank"><i class="icon-pinterest"></i></a></li>
    <?php endif;
    if (!empty($baseSettings->linkedin_url)): ?>
        <li><a href="<?= esc($baseSettings->linkedin_url); ?>" class="linkedin" target="_blank"><i class="icon-linkedin"></i></a></li>
    <?php endif;
    if (!empty($baseSettings->vk_url)): ?>
        <li><a href="<?= esc($baseSettings->vk_url); ?>" class="vk" target="_blank"><i class="icon-vk"></i></a></li>
    <?php endif;
    if (!empty($baseSettings->whatsapp_url)): ?>
        <li><a href="<?= esc($baseSettings->whatsapp_url); ?>" class="whatsapp" target="_blank"><i class="icon-whatsapp"></i></a></li>
    <?php endif;
    if (!empty($baseSettings->telegram_url)): ?>
        <li><a href="<?= esc($baseSettings->telegram_url); ?>" class="telegram" target="_blank"><i class="icon-telegram"></i></a></li>
    <?php endif;
    if (!empty($baseSettings->youtube_url)): ?>
        <li><a href="<?= esc($baseSettings->youtube_url); ?>" class="youtube" target="_blank"><i class="icon-youtube"></i></a></li>
    <?php endif;
    if ($generalSettings->rss_system == 1 && isset($showRSS)): ?>
        <li><a href="<?= generateUrl('rss_feeds'); ?>" class="rss" target="_blank"><i class="icon-rss"></i></a></li>
    <?php endif; ?>
</ul>