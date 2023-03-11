<div class="row-custom product-share">
    <label><?= trans("share"); ?>:</label>
    <ul>
        <li>
            <a href="javascript:void(0)" onclick='window.open("https://www.facebook.com/sharer/sharer.php?u=<?= generateProductUrl($product); ?>", "Share This Post", "width=640,height=450");return false'>
                <i class="icon-facebook"></i>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" onclick='window.open("https://twitter.com/share?url=<?= generateProductUrl($product); ?>&amp;text=<?= getProductTitle($product); ?>", "Share This Post", "width=640,height=450");return false'>
                <i class="icon-twitter"></i>
            </a>
        </li>
        <li>
            <a href="https://api.whatsapp.com/send?text=<?= str_replace('&', '', getProductTitle($product) ?? ''); ?> - <?= generateProductUrl($product); ?>" target="_blank">
                <i class="icon-whatsapp"></i>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" onclick='window.open("http://pinterest.com/pin/create/button/?url=<?= generateProductUrl($product); ?>&amp;media=<?= getProductMainImage($product->id, 'image_default'); ?>", "Share This Post", "width=640,height=450");return false'>
                <i class="icon-pinterest"></i>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" onclick='window.open("http://www.linkedin.com/shareArticle?mini=true&amp;url=<?= generateProductUrl($product); ?>", "Share This Post", "width=640,height=450");return false'>
                <i class="icon-linkedin"></i>
            </a>
        </li>
    </ul>
</div>