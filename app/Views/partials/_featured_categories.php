<?php if (!empty($featuredCategories)): ?>
    <div class="featured-categories">
        <div class="card-columns">
            <?php foreach ($featuredCategories as $category): ?>
                <div class="card lazyload" data-bg="<?= getCategoryImageUrl($category); ?>">
                    <a href="<?= generateCategoryUrl($category); ?>">
                        <div class="caption text-truncate">
                            <span><?= getCategoryName($category); ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>