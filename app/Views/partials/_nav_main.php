<?php $subCategoryDisplayLimit = 6;
if ($generalSettings->selected_navigation == 1): ?>
<div class="container">
<div class="navbar navbar-light navbar-expand">
<ul class="nav navbar-nav mega-menu">
<?php $limit = $generalSettings->menu_limit;
$count = 1;
if (!empty($parentCategories)):
foreach ($parentCategories as $category):
if ($category->show_on_main_menu == 1):
$arrayImageCategories = array();
if (!empty($category->image) && $category->show_image_on_main_menu == 1) {
array_push($arrayImageCategories, $category);
}
if ($count <= $limit):?>
<li class="nav-item dropdown" data-category-id="<?= $category->id; ?>">
<a id="nav_main_category_<?= $category->id; ?>" href="<?= generateCategoryUrl($category); ?>" class="nav-link dropdown-toggle nav-main-category" data-id="<?= $category->id; ?>" data-parent-id="<?= $category->parent_id; ?>" data-has-sb="<?= !empty($category->has_subcategory) ? '1' : '0'; ?>"><?= getCategoryName($category); ?></a>
<?php $subCategories = !empty($categoriesArray[$category->id]) ? $categoriesArray[$category->id] : null;
if (!empty($subCategories)): ?>
<div id="mega_menu_content_<?= $category->id; ?>" class="dropdown-menu mega-menu-content">
<div class="row">
<div class="col-8 menu-subcategories col-category-links">
<div class="card-columns">
<?php
foreach ($subCategories as $subCategory):
if ($subCategory->show_on_main_menu == 1):
if (!empty($subCategory->image) && $subCategory->show_image_on_main_menu == 1) {
array_push($arrayImageCategories, $subCategory);
} ?>
<div class="card">
<div class="row">
<div class="col-12">
<a id="nav_main_category_<?= $subCategory->id; ?>" href="<?= generateCategoryUrl($subCategory); ?>" class="second-category nav-main-category" data-id="<?= $subCategory->id; ?>" data-parent-id="<?= $subCategory->parent_id; ?>" data-has-sb="<?= !empty($subCategory->has_subcategory) ? '1' : '0'; ?>"><?= getCategoryName($subCategory); ?></a>
<?php $thirdCategories = !empty($categoriesArray[$subCategory->id]) ? $categoriesArray[$subCategory->id] : null;
if (!empty($thirdCategories)):
$displayLimit = 1; ?>
<ul>
<?php foreach ($thirdCategories as $thirdCategory):
if (!empty($thirdCategory->image) && $thirdCategory->show_image_on_main_menu == 1) {
array_push($arrayImageCategories, $thirdCategory);
} ?>
<li><a id="nav_main_category_<?= $thirdCategory->id; ?>" href="<?= generateCategoryUrl($thirdCategory); ?>" class="nav-main-category <?= $displayLimit > $subCategoryDisplayLimit ? 'hidden' : ''; ?>" data-id="<?= $thirdCategory->id; ?>" data-parent-id="<?= $thirdCategory->parent_id; ?>" data-has-sb="0"><?= getCategoryName($thirdCategory); ?></a></li>
<?php $displayLimit++;
endforeach;
if ($displayLimit > $subCategoryDisplayLimit): ?>
<li><a href="<?= generateCategoryUrl($subCategory); ?>" class="link-view-all"><?= trans("show_all"); ?></a></li>
<?php endif; ?>
</ul>
<?php endif; ?>
</div>
</div>
</div>
<?php endif;
endforeach; ?>
</div>
</div>
<div class="col-4 col-category-images">
<?php if (!empty($arrayImageCategories)):
foreach ($arrayImageCategories as $imageCategory): ?>
<div class="nav-category-image">
<a href="<?= generateCategoryUrl($imageCategory); ?>">
<img src="<?= base_url(IMG_BG_PRODUCT_SMALL); ?>" data-src="<?= getCategoryImageUrl($imageCategory); ?>" alt="<?= getCategoryName($imageCategory); ?>" class="lazyload img-fluid">
<span><?= characterLimiter(getCategoryName($imageCategory), 20, '..'); ?></span>
</a>
</div>
<?php endforeach;
endif; ?>
</div>
</div>
</div>
<?php endif; ?>
</li>
<?php $count++;
endif;
endif;
endforeach;
if (countItems($parentCategories) > $limit): ?>
<li class="nav-item dropdown menu-li-more">
<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?= trans("more"); ?></a>
<div class="dropdown-menu dropdown-menu-more-items">
<?php $count = 1;
if (!empty($parentCategories)):
foreach ($parentCategories as $category):
if ($category->show_on_main_menu == 1):
if ($count > $limit):?>
<a href="<?= generateCategoryUrl($category); ?>" class="dropdown-item" data-id="<?= $category->id; ?>" data-parent-id="<?= $category->parent_id; ?>" data-has-sb="<?= !empty($category->has_subcategory) ? '1' : '0'; ?>"><?= getCategoryName($category); ?></a>
<?php $subCategories = !empty($categoriesArray[$category->id]) ? $categoriesArray[$category->id] : null;
if (!empty($subCategories)):
foreach ($subCategories as $subCategory): ?>
<a id="nav_main_category_<?= $subCategory->id; ?>" href="<?= generateCategoryUrl($subCategory); ?>" class="hidden" data-id="<?= $subCategory->id; ?>" data-parent-id="<?= $subCategory->parent_id; ?>" data-has-sb="<?= !empty($subCategory->has_subcategory) ? '1' : '0'; ?>"><?= getCategoryName($subCategory); ?></a>
<?php $thirdCategories = !empty($categoriesArray[$subCategory->id]) ? $categoriesArray[$subCategory->id] : null;
if (!empty($thirdCategories)):
foreach ($thirdCategories as $thirdCategory): ?>
<a id="nav_main_category_<?= $thirdCategory->id; ?>" href="<?= generateCategoryUrl($thirdCategory); ?>" class="hidden" data-id="<?= $thirdCategory->id; ?>" data-parent-id="<?= $thirdCategory->parent_id; ?>" data-has-sb="0"><?= getCategoryName($thirdCategory); ?></a>
<?php endforeach;
endif;
endforeach;
endif;
endif;
endif;
$count++;
endforeach;
endif; ?>
</div>
</li>
<?php endif;
endif; ?>
</ul>
</div>
</div>
<?php else: ?>
<div class="container">
<div class="navbar navbar-light navbar-expand">
<ul class="nav navbar-nav mega-menu">
<?php $limit = $generalSettings->menu_limit;
$menuItemCount = 1;
if (!empty($parentCategories)):
foreach ($parentCategories as $category):
if ($category->show_on_main_menu == 1):
if ($menuItemCount <= $limit):?>
<li class="nav-item dropdown" data-category-id="<?= $category->id; ?>">
<a id="nav_main_category_<?= $category->id; ?>" href="<?= generateCategoryUrl($category); ?>" class="nav-link dropdown-toggle nav-main-category" data-id="<?= $category->id; ?>" data-parent-id="<?= $category->parent_id; ?>" data-has-sb="<?= !empty($category->has_subcategory) ? '1' : '0'; ?>"><?= getCategoryName($category); ?></a>
<?php $subCategories = !empty($categoriesArray[$category->id]) ? $categoriesArray[$category->id] : null;
if (!empty($subCategories)):?>
<div id="mega_menu_content_<?= $category->id; ?>" class="dropdown-menu dropdown-menu-large">
<div class="row">
<div class="col-4 left">
<?php $count = 0;
foreach ($subCategories as $subCategory): ?>
<div class="large-menu-item <?= $count == 0 ? 'large-menu-item-first active' : ''; ?>" data-subcategory-id="<?= $subCategory->id; ?>">
<a id="nav_main_category_<?= $subCategory->id; ?>" href="<?= generateCategoryUrl($subCategory); ?>" class="second-category nav-main-category" data-id="<?= $subCategory->id; ?>" data-parent-id="<?= $subCategory->parent_id; ?>" data-has-sb="<?= !empty($subCategory->has_subcategory) ? '1' : '0'; ?>"><?= getCategoryName($subCategory); ?>&nbsp;<i class="icon-arrow-right"></i></a>
</div>
<?php $count++;
endforeach; ?>
</div>
<div class="col-8 right">
<?php $count = 0;
foreach ($subCategories as $subCategory): ?>
<div id="large_menu_content_<?= $subCategory->id; ?>" class="large-menu-content <?= ($count == 0) ? 'large-menu-content-first active' : ''; ?>">
<?php $thirdCategories = !empty($categoriesArray[$subCategory->id]) ? $categoriesArray[$subCategory->id] : null;
if (!empty($thirdCategories)): ?>
<div class="row">
<div class="card-columns">
<?php foreach ($thirdCategories as $thirdCategory): ?>
<div class="card item-large-menu-content">
<a id="nav_main_category_<?= $thirdCategory->id; ?>" href="<?= generateCategoryUrl($thirdCategory); ?>" class="second-category nav-main-category" data-id="<?= $thirdCategory->id; ?>" data-parent-id="<?= $thirdCategory->parent_id; ?>" data-has-sb="0"><?= getCategoryName($thirdCategory); ?></a>
<?php $i = 1;
$fourthCategories = !empty($categoriesArray[$thirdCategory->id]) ? $categoriesArray[$thirdCategory->id] : null;
if (!empty($fourthCategories)): ?>
<ul>
<?php foreach ($fourthCategories as $fourthCategory): ?>
<li><a id="nav_main_category_<?= $fourthCategory->id; ?>" href="<?= generateCategoryUrl($fourthCategory); ?>" class="nav-main-category <?= ($i > $subCategoryDisplayLimit) ? 'hidden' : ''; ?>" data-id="<?= $fourthCategory->id; ?>" data-parent-id="<?= $fourthCategory->parent_id; ?>" data-has-sb="0"><?= getCategoryName($fourthCategory); ?></a></li>
<?php $i++;
endforeach; ?>
</ul>
<?php endif;
if ($i - 1 > $subCategoryDisplayLimit): ?>
<div><a href="<?= generateCategoryUrl($thirdCategory); ?>" class="link-view-all"><?= trans("show_all"); ?></a></div>
<?php endif; ?>
</div>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>
</div>
<?php
$count++;
endforeach; ?>
</div>
</div>
</div>
<?php endif; ?>
</li>
<?php $menuItemCount++;
endif;
endif;
endforeach;
if (countItems($parentCategories) > $limit): ?>
<li class="nav-item dropdown menu-li-more">
<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?= trans("more"); ?></a>
<div class="dropdown-menu dropdown-menu-more-items">
<?php $menuItemCount = 1;
if (!empty($parentCategories)):
foreach ($parentCategories as $category):
if ($menuItemCount > $limit): ?>
<a href="<?= generateCategoryUrl($category); ?>" class="dropdown-item" data-id="<?= $category->id; ?>" data-parent-id="<?= $category->parent_id; ?>" data-has-sb="<?= !empty($category->has_subcategory) ? '1' : '0'; ?>"><?= getCategoryName($category); ?></a>
<?php $subCategories = !empty($categoriesArray[$category->id]) ? $categoriesArray[$category->id] : null;
if (!empty($subCategories)):
foreach ($subCategories as $subCategory): ?>
<a id="nav_main_category_<?= $subCategory->id; ?>" href="<?= generateCategoryUrl($subCategory); ?>" class="hidden" data-id="<?= $subCategory->id; ?>" data-parent-id="<?= $subCategory->parent_id; ?>" data-has-sb="<?= !empty($subCategory->has_subcategory) ? '1' : '0'; ?>"><?= getCategoryName($subCategory); ?></a>
<?php $thirdCategories = !empty($categoriesArray[$subCategory->id]) ? $categoriesArray[$subCategory->id] : null;
if (!empty($thirdCategories)):
foreach ($thirdCategories as $thirdCategory): ?>
<a id="nav_main_category_<?= $thirdCategory->id; ?>" href="<?= generateCategoryUrl($thirdCategory); ?>" class="hidden" data-id="<?= $thirdCategory->id; ?>" data-parent-id="<?= $thirdCategory->parent_id; ?>" data-has-sb="0"><?= getCategoryName($thirdCategory); ?></a>
<?php endforeach;
endif;
endforeach;
endif;
endif;
$menuItemCount++;
endforeach;
endif; ?>
</div>
</li>
<?php endif;
endif; ?>
</ul>
</div>
</div>
<?php endif; ?>