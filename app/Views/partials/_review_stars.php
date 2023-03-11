<?php if (empty($rating)) {
    $rating = 0;
} ?>
<div class="rating">
    <i class="<?= $rating >= 1 ? 'icon-star' : 'icon-star-o'; ?>"></i>
    <i class="<?= $rating >= 2 ? 'icon-star' : 'icon-star-o'; ?>"></i>
    <i class="<?= $rating >= 3 ? 'icon-star' : 'icon-star-o'; ?>"></i>
    <i class="<?= $rating >= 4 ? 'icon-star' : 'icon-star-o'; ?>"></i>
    <i class="<?= $rating >= 5 ? 'icon-star' : 'icon-star-o'; ?>"></i>
</div>