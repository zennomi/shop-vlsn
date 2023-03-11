<?php namespace App\Models;

use CodeIgniter\Model;

class FieldModel extends BaseModel
{
    protected $builder;
    protected $builderFieldsOptions;
    protected $builderFieldsProduct;
    protected $builderFieldsCategory;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('custom_fields');
        $this->builderFieldsOptions = $this->db->table('custom_fields_options');
        $this->builderFieldsProduct = $this->db->table('custom_fields_product');
        $this->builderFieldsCategory = $this->db->table('custom_fields_category');
    }

    //input values
    public function inputValues()
    {
        return [
            'row_width' => inputPost('row_width'),
            'is_required' => inputPost('is_required'),
            'status' => inputPost('status'),
            'field_order' => inputPost('field_order')
        ];
    }

    //add field
    public function addField()
    {
        $data = $this->inputValues();
        if (empty($data['is_required'])) {
            $data['is_required'] = 0;
        }
        //generate filter key
        $fieldName = inputPost('name_lang_' . selectedLangId());
        $key = strSlug($fieldName);
        //check filter key exists
        $row = $this->getFieldByFilterKey($key);
        if (!empty($row)) {
            $key = 'q_' . $key;
            $row = $this->getFieldByFilterKey($key);
            if (!empty($row)) {
                $key = $key . rand(1, 999);
            }
        }
        if (empty($key)) {
            $key = uniqid();
        }
        $data['product_filter_key'] = $key;
        $data['field_type'] = inputPost('field_type');

        $nameArray = array();
        foreach ($this->activeLanguages as $language) {
            $item = [
                'lang_id' => $language->id,
                'name' => inputPost('name_lang_' . $language->id)
            ];
            array_push($nameArray, $item);
        }
        $data['name_array'] = serialize($nameArray);
        if ($this->builder->insert($data)) {
            return $this->db->insertID();
        }
        return false;
    }

    //update field
    public function editField($id)
    {
        $field = $this->getField($id);
        if (!empty($field)) {
            $data = $this->inputValues();
            if (empty($data['is_required'])) {
                $data['is_required'] = 0;
            }
            $key = strSlug(inputPost('product_filter_key'));
            //check filter key exists
            $row = $this->getFieldByFilterKey($key, $id);
            if (!empty($row)) {
                $key = 'q_' . $key;
                $row = $this->getFieldByFilterKey($key);
                if (!empty($row)) {
                    $key = $key . rand(1, 999);
                }
            }
            if (empty($key)) {
                $key = uniqid();
            }
            $data['product_filter_key'] = $key;
            $data['field_type'] = inputPost('field_type');

            $nameArray = array();
            foreach ($this->activeLanguages as $language) {
                $item = [
                    'lang_id' => $language->id,
                    'name' => inputPost('name_lang_' . $language->id)
                ];
                array_push($nameArray, $item);
            }
            $data['name_array'] = serialize($nameArray);
            return $this->builder->where('id', $field->id)->update($data);
        }
        return false;
    }

    //add field option
    public function addFieldOption($fieldId)
    {
        $mainOption = inputPost('option_lang_' . selectedLangId());
        $data = [
            'field_id' => $fieldId,
            'option_key' => strSlug($mainOption)
        ];
        if ($this->builderFieldsOptions->insert($data)) {
            $lastId = $this->db->insertID();
            foreach ($this->activeLanguages as $language) {
                $option = inputPost('option_lang_' . $language->id);
                $item = [
                    'option_id' => $lastId,
                    'lang_id' => $language->id,
                    'option_name' => trim($option)
                ];
                $this->db->table('custom_fields_options_lang')->insert($item);
            }
        }
        return true;
    }

    //edit field option
    public function editFieldOption()
    {
        $id = inputPost('id');
        $fieldOption = $this->getFieldOption($id);
        if (!empty($fieldOption)) {
            $mainOption = inputPost('option_lang_' . selectedLangId());
            $data = ['option_key' => strSlug($mainOption)];
            if ($this->builderFieldsOptions->where('id', $fieldOption->id)->update($data)) {
                $this->db->table('custom_fields_options_lang')->where('option_id', $fieldOption->id)->delete();
                foreach ($this->activeLanguages as $language) {
                    $option = inputPost('option_lang_' . $language->id);
                    $item = [
                        'option_id' => $fieldOption->id,
                        'lang_id' => $language->id,
                        'option_name' => trim($option)
                    ];
                    $this->db->table('custom_fields_options_lang')->insert($item);
                }
            }
        }
    }

    //get field
    public function getField($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get field by filter key
    public function getFieldByFilterKey($filterKey, $exceptId = null)
    {
        if (!empty($exceptId)) {
            $this->builder->where('id != ', clrNum($exceptId));
        }
        return $this->builder->where('product_filter_key', $filterKey)->get()->getRow();
    }

    //get fields
    public function getFields()
    {
        return $this->builder->orderBy('field_order')->get()->getResult();
    }

    //get custom fields by category
    public function getCustomFieldsByCategory($categoryId)
    {
        $category = getCategory($categoryId);
        if (empty($category)) {
            return array();
        }
        $categories = getCategoryParentTree($category, true);
        $categoryIds = array();
        if (!empty($categories)) {
            $categoryIds = getIdsFromArray($categories);
        }
        if (!empty($categoryIds)) {
            return $this->builder->join('custom_fields_category', 'custom_fields_category.field_id = custom_fields.id')
                ->select('custom_fields.*, custom_fields_category.category_id AS category_id')
                ->where('custom_fields.status', 1)->whereIn('custom_fields_category.category_id', $categoryIds, false)->orderBy('custom_fields.field_order')->get()->getResult();
        }
        return array();
    }

    //get custom filters
    public function getCustomFilters($categoryId, $categories = null)
    {
        $categoryIds = array();
        if (!empty($categories)) {
            $categoryIds = getIdsFromArray($categories);
        }
        if (!empty($categoryIds)) {
            $this->builder->join('custom_fields_category', 'custom_fields_category.field_id = custom_fields.id')->whereIn('custom_fields_category.category_id', $categoryIds, false);
        }
       return $this->builder->select('custom_fields.*')->where('custom_fields.status', 1)->where('custom_fields.is_product_filter', 1)
            ->groupStart()->where('custom_fields.field_type', 'checkbox')->orWhere('custom_fields.field_type', 'radio_button')->orWhere('custom_fields.field_type', 'dropdown')->groupEnd()
            ->orderBy('custom_fields.field_order')->get()->getResult();
    }

    //get field categories
    public function getFieldCategories($fieldId)
    {
        return $this->builderFieldsCategory->where('field_id', clrNum($fieldId))->get()->getResult();
    }

    //get field options
    public function getFieldOptions($customField, $langId)
    {
        if (!empty($customField)) {
            $this->builderFieldsOptions->select('custom_fields_options.*')->select('(SELECT option_name FROM custom_fields_options_lang WHERE custom_fields_options.id = custom_fields_options_lang.option_id AND custom_fields_options_lang.lang_id =  ' . clrNum($langId) . ' LIMIT 1) AS option_name');
            if (countItems($this->activeLanguages) > 1) {
                $this->builderFieldsOptions->select('(SELECT option_name FROM custom_fields_options_lang WHERE custom_fields_options.id = custom_fields_options_lang.option_id AND custom_fields_options_lang.lang_id !=  ' . clrNum($langId) . ' LIMIT 1) AS second_name');
            }
            $this->builderFieldsOptions->where('custom_fields_options.field_id', clrNum($customField->id));
            if ($customField->sort_options == 'date') {
                $this->builderFieldsOptions->orderBy('custom_fields_options.id');
            }
            if ($customField->sort_options == 'date_desc') {
                $this->builderFieldsOptions->orderBy('custom_fields_options.id DESC');
            }
            if ($customField->sort_options == 'alphabetically') {
                $this->builderFieldsOptions->orderBy('option_name');
            }
            return $this->builderFieldsOptions->get()->getResult();
        }
        return array();
    }

    //get product filters options
    public function getProductFiltersOptions($customField, $langId, $customFilters, $queryStringArray = null)
    {
        if (!empty($customField)) {
            $this->builderFieldsOptions->select('custom_fields_options.*')->select('(SELECT option_name FROM custom_fields_options_lang WHERE custom_fields_options.id = custom_fields_options_lang.option_id AND custom_fields_options_lang.lang_id =  ' . clrNum($langId) . ' LIMIT 1) AS option_name');
            if (countItems($this->languages) > 1) {
                $this->builderFieldsOptions->select('(SELECT option_name FROM custom_fields_options_lang WHERE custom_fields_options.id = custom_fields_options_lang.option_id AND custom_fields_options_lang.lang_id !=  ' . clrNum($langId) . ' LIMIT 1) AS second_name');
            }
            $this->builderFieldsOptions->where('custom_fields_options.field_id', clrNum($customField->id));
            if ($customField->sort_options == 'date') {
                $this->builderFieldsOptions->orderBy('custom_fields_options.id');
            }
            if ($customField->sort_options == 'date_desc') {
                $this->builderFieldsOptions->orderBy('custom_fields_options.id DESC');
            }
            if ($customField->sort_options == 'alphabetically') {
                $this->builderFieldsOptions->orderBy('option_name');
            }
            return $this->builderFieldsOptions->get()->getResult();
        }
        return array();
    }

    //update field options settings
    public function updateFieldOptionsSettings()
    {
        $fieldId = inputPost('field_id');
        $data = ['sort_options' => inputPost('sort_options')];
        return $this->builder->where('id', clrNum($fieldId))->update($data);
    }

    //get field all options
    public function getFieldAllOptions($fieldId)
    {
        $this->builderFieldsOptions->select('custom_fields_options.*');
        foreach ($this->activeLanguages as $language) {
            $this->builderFieldsOptions->select('(SELECT option_name FROM custom_fields_options_lang WHERE custom_fields_options.id = custom_fields_options_lang.option_id AND custom_fields_options_lang.lang_id =  ' . clrNum($language->id) . ' LIMIT 1) AS option_name_' . clrNum($language->id));
        }
        return $this->builderFieldsOptions->where('custom_fields_options.field_id', clrNum($fieldId))->get()->getResult();
    }

    //get field option
    public function getFieldOption($optionId)
    {
        return $this->builderFieldsOptions->where('id', clrNum($optionId))->get()->getRow();
    }

    //add category to field
    public function addCategoryToField()
    {
        $fieldId = clrNum(inputPost('field_id'));
        $categoryId = getDropdownCategoryId();
        $row = $this->getCategoryField($fieldId, $categoryId);
        if (empty($row)) {
            $data = [
                'field_id' => $fieldId,
                'category_id' => $categoryId
            ];
            return $this->builderFieldsCategory->insert($data);
        }
        return false;
    }

    //get category field
    public function getCategoryField($fieldId, $categoryId)
    {
        return $this->builderFieldsCategory->where('field_id', clrNum($fieldId))->where('category_id', clrNum($categoryId))->get()->getRow();
    }

    //get product custom field values
    public function getProductCustomFieldValues($fieldId, $productId, $langId)
    {
        $this->builderFieldsProduct->select('custom_fields_product.*')->where('custom_fields_product.field_id', clrNum($fieldId))->where('custom_fields_product.product_id', clrNum($productId))
            ->select('(SELECT option_name FROM custom_fields_options_lang WHERE custom_fields_product.selected_option_id = custom_fields_options_lang.option_id AND custom_fields_options_lang.lang_id =  ' . clrNum($langId) . ' LIMIT 1) AS option_name');
        if (countItems($this->activeLanguages) > 1) {
            $this->builderFieldsProduct->select('(SELECT option_name FROM custom_fields_options_lang WHERE custom_fields_product.selected_option_id = custom_fields_options_lang.option_id AND custom_fields_options_lang.lang_id !=  ' . clrNum($langId) . ' LIMIT 1) AS second_name');
        }
        return $this->builderFieldsProduct->get()->getResult();
    }

    //get product custom field input value
    public function getProductCustomFieldInputValue($fieldId, $productId)
    {
        $row = $this->builderFieldsProduct->where('field_id', clrNum($fieldId))->where('product_id', clrNum($productId))->get()->getRow();
        if (!empty($row) && !empty($row->field_value)) {
            return $row->field_value;
        }
        return '';
    }

    //delete category from field
    public function deleteCategoryFromField($fieldId, $categoryId)
    {
        return $this->builderFieldsCategory->where('field_id', clrNum($fieldId))->where('category_id', clrNum($categoryId))->delete();
    }

    //delete custom field option
    public function deleteCustomFieldOption($id)
    {
        $option = $this->getFieldOption($id);
        if (!empty($option)) {
            $this->builderFieldsOptions->where('id', $option->id)->delete();
            $this->db->table('custom_fields_options_lang')->where('option_id', $option->id)->delete();
        }
    }

    //add remove custom field filters
    public function addRemoveCustomFieldFilters($fieldId)
    {
        $field = $this->getField($fieldId);
        if (!empty($field)) {
            if ($field->is_product_filter == 1) {
                $data = ['is_product_filter' => 0];
            } else {
                $data = ['is_product_filter' => 1];
            }
            return $this->builder->where('id', $field->id)->update($data);
        }
    }

    //delete field product values by product id
    public function deleteFieldProductValuesByProductId($productId)
    {
        if (!empty($productId)) {
            $this->builderFieldsProduct->where('product_id', clrNum($productId))->delete();
        }
    }

    //delete field
    public function deleteField($id)
    {
        $field = $this->getField($id);
        if (!empty($field)) {
            $this->builderFieldsCategory->where('field_id', $field->id)->delete();
            $options = $this->builderFieldsOptions->where('field_id', $field->id)->get()->getResult();
            if (!empty($options)) {
                foreach ($options as $option) {
                    $this->db->table('custom_fields_options_lang')->where('option_id', $option->id)->delete();
                    $this->builderFieldsOptions->where('id', $option->id)->delete();
                }
            }
            $this->builderFieldsProduct->where('field_id', $field->id)->delete();
            return $this->builder->where('id', $field->id)->delete();
        }
        return false;
    }
}
