<ul class="nav nav-tabs nav-tabs-horizontal nav-tabs-profile" role="tablist">
    <?php if ($generalSettings->multi_vendor_system == 1):
        if (isVendor($user)): ?>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab == 'products' ? 'active' : ''; ?>" href="<?= generateProfileUrl($user->slug); ?>"><?= trans("products"); ?><span class="count">(<?= getUserTotalProductsCount($user->id); ?>)</span></a>
            </li>
        <?php endif;
    endif; ?>
    <li class="nav-item">
        <a class="nav-link <?= $activeTab == 'wishlist' ? 'active' : ''; ?>" href="<?= generateUrl('wishlist') . '/' . $user->slug; ?>"><?= trans("wishlist"); ?><span class="count">(<?= getUserWishlistProductsCount($user->id); ?>)</span></a>
    </li>
    <?php if ($generalSettings->multi_vendor_system == 1): ?>
        <?php if (authCheck() && user()->id == $user->id && isSaleActive() && $generalSettings->digital_products_system == 1): ?>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab == 'downloads') ? 'active' : ''; ?>" href="<?= generateUrl('downloads'); ?>"><?= trans("downloads"); ?><span class="count">(<?= getUserDownloadsCount($user->id); ?>)</span></a>
            </li>
        <?php endif;
    endif; ?>
    <li class="nav-item">
        <a class="nav-link <?= $activeTab == 'followers' ? 'active' : ''; ?>" href="<?= generateUrl('followers') . '/' . $user->slug; ?>"><?= trans("followers"); ?><span class="count">(<?= getFollowersCount($user->id); ?>)</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $activeTab == 'following' ? 'active' : ''; ?>" href="<?= generateUrl('following') . '/' . $user->slug; ?>"><?= trans("following"); ?><span class="count">(<?= getFollowingUsersCount($user->id); ?>)</span></a>
    </li>
    <?php if (($generalSettings->reviews == 1) && isVendor($user) && $generalSettings->multi_vendor_system == 1): ?>
        <li class="nav-item">
            <a class="nav-link <?= $activeTab == 'reviews' ? 'active' : ''; ?>" href="<?= generateUrl('reviews') . '/' . $user->slug; ?>"><?= trans("reviews"); ?><span class="count">(<?= $userRating->count; ?>)</span></a>
        </li>
    <?php endif; ?>
</ul>