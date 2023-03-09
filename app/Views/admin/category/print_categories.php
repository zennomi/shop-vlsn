<?php function printSubCategories($parentId, $langId)
{
    $model = new \App\Models\CategoryModel();
    $subCategories = $model->getSubCategoriesByParentId($parentId, $langId);
    if (!empty($subCategories)) {
        foreach ($subCategories as $category) {
            $i = 0;
            if ($i == 0) {
                if (!empty($category->has_subcategory)) {
                    echo '<div class="panel-group">';
                } else {
                    echo '<div class="panel-group cursor-default">';
                }
                echo '<div class="panel panel-default">';
                if (!empty($category->has_subcategory)) {
                    $divContent = '<div class="panel-heading" data-item-id="' . $category->id . '" href="#collapse_' . $category->id . '">';
                } else {
                    $divContent = '<div class="panel-heading">';
                }
                $divContent .= '<div class="left">';
                if (!empty($category->has_subcategory)) {
                    $divContent .= '<i class="fa fa-caret-right"></i>';
                } else {
                    $divContent .= '<i class="fa fa-circle" style="font-size: 8px;"></i>';
                }
                $divContent .= getCategoryName($category) . '<span class="id">(' . trans("id") . ': ' . $category->id . ')</span>';
                $divContent .= '</div>';
                $divContent .= '<div class="right">';
                $divContent .= ($category->is_featured == 1) ? '<label class="label bg-teal">' . trans("featured") . '</label>' : '';
                $divContent .= ($category->visibility == 1) ? '<label class="label bg-olive">' . trans("visible") . '</label>' : '<label class="label bg-danger">' . trans("hidden") . '</label>';
                $divContent .= '<div class="btn-group btn-group-option">';
                $divContent .= '<a href="' . adminUrl() . '/edit-category/' . $category->id . '" target="_blank" class="btn btn-sm btn-default btn-edit">' . trans("edit") . '</a>';
                $divContent .= '<a href="javascript:void(0)" class="btn btn-sm btn-default btn-delete" data-item-id="' . $category->id . '"><i class="fa fa-trash-o"></i></a>';
                $divContent .= '</div>';
                $divContent .= '</div>';
                $divContent .= '</div>';
                echo $divContent;
                echo '<div id="collapse_' . $category->id . '" class="panel-collapse collapse"><div class="panel-body nested-sortable" data-parent-id="' . $category->id . '">';
            } else {
                echo '<div id="collapse_' . $category->id . '" class="list-group-item" data-item-id="' . $category->id . '">' . getCategoryName($category) . '<span class="id">(' . trans("id") . ': ' . $category->id . ')</span>' . '</div>';
            }
            printSubCategories($category->id, $langId);
            $i++;
            if ($i > 0) {
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        }
    }
} ?>
<div class="panel-body">
    <?= printSubCategories($parentCategoryId, $langId); ?>
</div>
