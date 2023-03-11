<div class="profile-tabs">
    <ul class="nav">
        <li class="nav-item <?= $activeTab == 'edit_profile' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= generateUrl("settings"); ?>">
                <span><?= trans("update_profile"); ?></span>
            </a>
        </li>
        <?php if (isSaleActive()): ?>
            <li class="nav-item <?= $activeTab == 'shipping_address' ? 'active' : ''; ?>">
                <a class="nav-link" href="<?= generateUrl("settings", "shipping_address"); ?>">
                    <span><?= trans("shipping_address"); ?></span>
                </a>
            </li>
        <?php endif; ?>
        <li class="nav-item <?= $activeTab == 'social_media' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= generateUrl("settings", "social_media"); ?>">
                <span><?= trans("social_media"); ?></span>
            </a>
        </li>
        <li class="nav-item <?= $activeTab == 'change_password' ? 'active' : ''; ?>">
            <a class="nav-link" href="<?= generateUrl("settings", "change_password"); ?>">
                <span><?= trans("change_password"); ?></span>
            </a>
        </li>
    </ul>
</div>
