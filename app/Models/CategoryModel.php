<?php namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('categories');
    }

    //build query
    public function buildQuery($langId, $allColumns = false)
    {
        if (empty($langId)) {
            $langId = selectedLangId();
        }
        $this->builder->resetQuery();
        if ($allColumns == true) {
            $this->builder->select('categories.*, categories.parent_id AS join_parent_id');
        } else {
            $this->builder->select('categories.id, categories.slug, categories.parent_id, categories.parent_tree, categories.category_order, categories.featured_order, categories.storage, categories.image, categories.show_on_main_menu, categories.show_image_on_main_menu, categories.parent_id AS join_parent_id');
        }
        $this->builder->select('(SELECT name FROM categories_lang WHERE categories_lang.category_id = categories.id AND categories_lang.lang_id = ' . clrNum($langId) . ' LIMIT 1) AS name');
        if (countItems($this->activeLanguages) > 1) {
            $this->builder->select('(SELECT name FROM categories_lang WHERE categories_lang.category_id = categories.id AND categories_lang.lang_id != ' . clrNum($langId) . ' LIMIT 1) AS second_name');
        }
        $this->builder->select('(SELECT slug FROM categories WHERE id = join_parent_id) AS parent_slug')->select('(SELECT id FROM categories AS sub_categories WHERE sub_categories.parent_id = categories.id LIMIT 1) AS has_subcategory');
    }

    //get categories array
    public function getCategoriesArray()
    {
        $key = 'categories_array';
        $array = getCacheData($key);
        if (!empty($array)) {
            return $array;
        }
        $array = array();
        $maxLevel = 3;
        if ($this->generalSettings->selected_navigation != 1) {
            $maxLevel = 4;
        }
        $this->buildQuery(selectedLangId());
        $this->builder->where('visibility', 1)->where('level <= ', $maxLevel);
        $this->orderByCategories();
        $categories = $this->builder->get()->getResult();
        if (!empty($categories)) {
            foreach ($categories as $category) {
                if ($category->show_on_main_menu == 1) {
                    $array[$category->parent_id][] = $category;
                }
            }
        }
        setCacheData($key, $array);
        return $array;
    }

    //get parent categories
    public function getParentCategories()
    {
        $key = 'parent_categories';
        $categories = getCacheData($key);
        if (!empty($categories)) {
            return $categories;
        }
        $this->buildQuery(selectedLangId(), true);
        $this->builder->where('parent_id', 0)->where('visibility', 1);
        if ($this->generalSettings->sort_parent_categories_by_order == 1) {
            $this->builder->orderBy('category_order');
        } else {
            $this->orderByCategories();
        }
        $categories = $this->builder->get()->getResult();
        setCacheData($key, $categories);
        return $categories;
    }

    //get category
    public function getCategory($id, $langId = null)
    {
        $this->buildQuery($langId, true);
        return $this->builder->where('categories.id', clrNum($id))->get()->getRow();
    }

    //get category by slug
    public function getCategoryBySlug($slug)
    {
        $this->buildQuery(selectedLangId(), true);
        return $this->builder->where('visibility', 1)->where('categories.slug', cleanStr($slug))->get()->getRow();
    }

    //get parent category by slug
    public function getParentCategoryBySlug($slug)
    {
        $this->buildQuery(selectedLangId(), true);
        return $this->builder->where('categories.slug', cleanStr($slug))->where('visibility', 1)->where('parent_id', 0)->orderBy('id')->get()->getRow();
    }

    //get parent category by slug
    public function getSubCategoryBySlug($slug)
    {
        $this->buildQuery(selectedLangId(), true);
        return $this->builder->where('categories.slug', cleanStr($slug))->where('visibility', 1)->where('parent_id !=', 0)->orderBy('id')->get()->getRow();
    }

    //get featured categories
    public function getFeaturedCategories()
    {
        $key = 'featured_categories';
        $categories = getCacheData($key);
        if (!empty($categories)) {
            return $categories;
        }
        $this->buildQuery(selectedLangId(), true);
        $categories = $this->builder->where('visibility', 1)->where('is_featured', 1)->orderBy('featured_order')->get()->getResult();
        setCacheData($key, $categories);
        return $categories;
    }

    //get index categories
    public function getIndexCategories()
    {
        $key = 'index_categories';
        $categories = getCacheData($key);
        if (!empty($categories)) {
            return $categories;
        }
        $this->buildQuery(selectedLangId(), true);
        $categories = $this->builder->where('visibility', 1)->where('show_products_on_index', 1)->orderBy('homepage_order')->get()->getResult();
        setCacheData($key, $categories);
        return $categories;
    }

    //get vendor categories
    public function getVendorCategories($category = null, $vendorId = null, $onlyHasProducts = true, $returnByParentId = true)
    {
        $categories = array();
        $categoryIds = array();
        if ($onlyHasProducts == true) {
            if (!empty($vendorId)) {
                $this->builder->where('categories.id IN (SELECT category_id FROM products WHERE products.status = 1 AND products.visibility = 1 AND products.is_draft = 0 AND products.is_deleted = 0 AND user_id = ' . clrNum($vendorId) . ')');
            } else {
                $this->builder->where('categories.id IN (SELECT category_id FROM products WHERE products.status = 1 AND products.visibility = 1 AND products.is_draft = 0 AND products.is_deleted = 0)');
            }
        }
        if (!empty($category)) {
            $this->builder->where('FIND_IN_SET(' . $category->id . ', categories.parent_tree)');
        }
        $result = $this->builder->get()->getResult();
        if (!empty($result)) {
            foreach ($result as $item) {
                if (!in_array($item->id, $categoryIds)) {
                    array_push($categoryIds, $item->id);
                }
                if (!empty($item->parent_tree)) {
                    $array = explode(',', $item->parent_tree ?? '');
                    if (!empty($array)) {
                        foreach ($array as $id) {
                            $id = intval($id);
                            if (!in_array($id, $categoryIds)) {
                                array_push($categoryIds, $id);
                            }
                        }
                    }
                }
            }
        }
        $parentId = 0;
        if (!empty($category)) {
            $parentId = $category->id;
        }
        if (!empty($categoryIds)) {
            $this->buildQuery(selectedLangId(), true);
            $this->builder->whereIn('id', $categoryIds, FALSE)->where('visibility', 1);
            if ($returnByParentId == true) {
                $this->builder->where('parent_id', clrNum($parentId));
            }
            $categories = $this->builder->orderBy('slug')->get()->getResult();
        }
        if (empty($categories)) {
            array_push($categories, $category);
        }
        return $categories;
    }

    //get category parent tree
    public function getCategoryParentTree($category, $onlyVisible = true)
    {
        if (empty($category)) {
            return array();
        }
        $parentTree = $category->parent_tree;
        $ids = array();
        $strSort = '';
        if (!empty($parentTree)) {
            $array = explode(',', $parentTree);
            if (!empty($array)) {
                foreach ($array as $item) {
                    if (!empty($item)) {
                        array_push($ids, intval($item));
                        if ($strSort == '') {
                            $strSort = intval($item);
                        } else {
                            $strSort .= ',' . intval($item);
                        }
                    }
                }
            }
        }
        if (!in_array($category->id, $ids)) {
            array_push($ids, $category->id);
            if ($strSort == '') {
                $strSort = $category->id;
            } else {
                $strSort .= ',' . $category->id;
            }
        }
        $this->buildQuery(selectedLangId(), true);
        $this->builder->whereIn('categories.id', $ids, false);
        if ($onlyVisible == true) {
            $this->builder->where('categories.visibility', 1);
        }
        return $this->builder->orderBy('FIELD(id, ' . $strSort . ')')->get()->getResult();
    }

    //get subcategories tree ids
    public function getSubCategoriesTreeIds($categoryId, $onlyVisible = false, $cache = true)
    {
        $this->builder->select('id')->where('FIND_IN_SET(' . clrNum($categoryId) . ', categories.parent_tree)');
        if ($onlyVisible == true) {
            $this->builder->where('categories.visibility', 1);
        }
        $result = $this->builder->get()->getResult();
        $array = array();
        array_push($array, $categoryId);
        if (!empty($result)) {
            foreach ($result as $item) {
                array_push($array, $item->id);
            }
        }
        return $array;
    }

    //sort categories
    public function orderByCategories()
    {
        $sort = $this->generalSettings->sort_categories;
        if ($sort == 'date') {
            $this->builder->orderBy('categories.created_at');
        } elseif ($sort == 'date_desc') {
            $this->builder->orderBy('categories.created_at', 'DESC');
        } elseif ($sort == 'alphabetically') {
            $this->builder->orderBy('name');
        } else {
            $this->builder->orderBy('category_order, name');
        }
    }

    /*
     * --------------------------------------------------------------------
     * Back-End
     * --------------------------------------------------------------------
     */

    //input values
    public function inputValues()
    {
        return [
            'slug' => inputPost('slug'),
            'title_meta_tag' => inputPost('title_meta_tag'),
            'description' => inputPost('description'),
            'keywords' => inputPost('keywords'),
            'category_order' => inputPost('category_order'),
            'featured_order' => 1,
            'visibility' => inputPost('visibility'),
            'show_on_main_menu' => inputPost('show_on_main_menu'),
            'show_image_on_main_menu' => inputPost('show_image_on_main_menu')
        ];
    }

    //add category
    public function addCategory()
    {
        $data = $this->inputValues();
        $name = inputPost('name_lang_' . $this->generalSettings->site_lang);
        $data['slug'] = generateSlug($data['slug'], $name);
        //set parent id
        $data['parent_id'] = 0;
        $categoryIdsArray = inputPost('category_id');
        if (!empty($categoryIdsArray)) {
            foreach ($categoryIdsArray as $key => $value) {
                if (!empty($value)) {
                    $data['parent_id'] = $value;
                }
            }
        }
        $data['tree_id'] = 0;
        $data['level'] = 1;
        $data['parent_tree'] = '';
        if (!empty($data['parent_id'])) {
            $parentCategory = $this->getCategory($data['parent_id']);
            if (!empty($parentCategory)) {
                $data['tree_id'] = $parentCategory->tree_id;
                $data['level'] = $parentCategory->level + 1;
                if (!empty($parentCategory->parent_tree)) {
                    $data['parent_tree'] = $parentCategory->parent_tree . ',' . $parentCategory->id;
                } else {
                    $data['parent_tree'] = $parentCategory->id;
                }
            }
        }

        $data['storage'] = 'local';
        $data['image'] = '';
        $uploadModel = new UploadModel();
        $tempFile = $uploadModel->uploadTempFile('file');
        if (!empty($tempFile) && !empty($tempFile['path'])) {
            $data['image'] = $uploadModel->uploadCategoryImage($tempFile['path']);
            $uploadModel->deleteTempFile($tempFile['path']);
        }
        //move to s3
        if ($this->storageSettings->storage == 'aws_s3') {
            $awsModel = new AwsModel();
            $data['storage'] = 'aws_s3';
            //move image
            if ($data['image'] != '') {
                $awsModel->putCategoryObject($data['image'], FCPATH . $data['image']);
                deleteFile($data['image']);
            }
        }
        $data['is_featured'] = 0;
        $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->builder->insert($data)) {
            $lastId = $this->db->insertID();
            if (empty($data['parent_tree'])) {
                $this->builder->where('id', $lastId)->update(['tree_id' => $lastId]);
            }
            $this->addCategoryName($lastId);
            $this->updateSlug($lastId);
            return true;
        }
        return false;
    }

    //update category
    public function editCategory($id)
    {
        $category = $this->getCategory($id);
        if (!empty($category)) {
            $data = $this->inputValues();
            $name = inputPost('name_lang_' . $this->generalSettings->site_lang);
            $data['slug'] = generateSlug($data['slug'], $name);
            //set parent id
            $data['parent_id'] = 0;
            $categoryIdsArray = inputPost('category_id');
            if (!empty($categoryIdsArray)) {
                foreach ($categoryIdsArray as $key => $value) {
                    if (!empty($value)) {
                        $data['parent_id'] = $value;
                    }
                }
            }
            $data['tree_id'] = 0;
            $data['level'] = $category->level;
            if (!empty($data['parent_id'])) {
                $parentCategory = $this->getCategory($data['parent_id']);
                if (!empty($parentCategory)) {
                    $data['tree_id'] = $parentCategory->tree_id;
                    $data['level'] = $parentCategory->level + 1;
                }
            }
            $uploadModel = new UploadModel();
            $tempFile = $uploadModel->uploadTempFile('file');
            if (!empty($tempFile) && !empty($tempFile['path'])) {
                $data['image'] = $uploadModel->uploadCategoryImage($tempFile['path']);
                $uploadModel->deleteTempFile($tempFile['path']);
                $data['storage'] = 'local';
                //move to s3
                if ($this->storageSettings->storage == 'aws_s3') {
                    $awsModel = new AwsModel();
                    $data['storage'] = 'aws_s3';
                    $awsModel->putCategoryObject($data['image'], FCPATH . $data['image']);
                    deleteFile($data['image']);
                }
                //delete old image
                if ($category->storage == 'aws_s3') {
                    $awsModel = new AwsModel();
                    $awsModel->deleteCategoryObject($category->image);
                } else {
                    deleteFile($category->image);
                }
            }

            $oldParentId = $category->parent_id;
            $oldTreeId = $category->tree_id;
            $newParentId = $data['parent_id'];
            if (empty($data['tree_id'])) {
                $data['tree_id'] = $category->id;
            }
            if ($this->builder->where('id', $category->id)->update($data)) {
                //update category info
                $this->updateCategoryName($category->id);
                //update slug
                $this->updateSlug($category->id);
                //update category tree
                if ($oldParentId != $newParentId) {
                    $this->updateCategoriesParentTree($oldTreeId);
                    if ($oldTreeId != $data['tree_id']) {
                        $this->updateCategoriesParentTree($data['tree_id']);
                    }
                }
                return true;
            }
        }
        return false;
    }

    //add category name
    public function addCategoryName($categoryId)
    {
        foreach ($this->activeLanguages as $language) {
            $data = [
                'category_id' => clrNum($categoryId),
                'lang_id' => $language->id,
                'name' => inputPost('name_lang_' . $language->id)
            ];
            $this->db->table('categories_lang')->insert($data);
        }
    }

    //update slug
    public function updateSlug($id)
    {
        $category = $this->getCategory($id);
        if (!empty($category)) {
            if (empty($category->slug) || $category->slug == '-') {
                $data = ['slug' => $category->id];
                $this->builder->where('id', $category->id)->update($data);
            } else {
                $numRows = $this->builder->where('slug', cleanStr($category->slug))->where('id !=', $category->id)->countAllResults();
                if ($numRows > 0) {
                    $data = ['slug' => $category->slug . '-' . $category->id];
                    $this->builder->where('id', $category->id)->update($data);
                }
            }
        }
    }

    //update category name
    public function updateCategoryName($categoryId)
    {
        foreach ($this->activeLanguages as $language) {
            $data = [
                'category_id' => clrNum($categoryId),
                'lang_id' => $language->id,
                'name' => inputPost('name_lang_' . $language->id)
            ];
            //check category name exists
            $row = $this->db->table('categories_lang')->where('category_id', clrNum($categoryId))->where('lang_id', $language->id)->get()->getRow();
            if (empty($row)) {
                $this->db->table('categories_lang')->insert($data);
            } else {
                $this->db->table('categories_lang')->where('category_id', clrNum($categoryId))->where('lang_id', $language->id)->update($data);
            }
        }
    }

    //update all categories parent tree
    public function updateCategoriesParentTree($treeId = null)
    {
        if (!empty($treeId)) {
            $category = $this->builder->where('id', clrNum($treeId))->get()->getRow();
            if (!empty($category)) {
                //update parent
                $this->builder->where('id', $category->id)->update(['tree_id' => $category->id, 'parent_tree' => '', 'level' => 1]);
                //update all subcategories
                $this->updateSubCategoriesParentTree($category, $category->id);
            }
        } else {
            $categories = $this->builder->where('parent_id', 0)->get()->getResult();
            if (!empty($categories)) {
                foreach ($categories as $category) {
                    //update parent
                    $this->builder->where('id', $category->id)->update(['tree_id' => $category->id, 'parent_tree' => '', 'level' => 1]);
                    //update all subcategories
                    $this->updateSubCategoriesParentTree($category, $category->id);
                }
            }
        }
    }

    //recursive update subcategory parent tree
    public function updateSubCategoriesParentTree($category, $treeId)
    {
        if (!empty($category)) {
            $categories = $this->builder->select('categories.id, categories.parent_id AS parent_category_id, (SELECT parent_tree FROM categories WHERE id = parent_category_id) AS parent_category_tree')
                ->where('parent_id', $category->id)->get()->getResult();
            if (!empty($categories)) {
                foreach ($categories as $item) {
                    $parentTree = '';
                    if ($item->parent_category_id != 0) {
                        if (empty($item->parent_category_tree)) {
                            $parentTree = $item->parent_category_id;
                        } else {
                            $parentTree = $item->parent_category_tree . ',' . $item->parent_category_id;
                        }
                    }
                    $level = 1;
                    if (!empty($parentTree)) {
                        $array = explode(',', $parentTree);
                        $level = countItems($array) + 1;
                    }
                    $this->builder->where('id', $item->id)->update(['tree_id' => $treeId, 'parent_tree' => $parentTree, 'level' => $level]);
                    $this->updateSubCategoriesParentTree($item, $treeId);
                }
            }
        }
    }

    //check category parent trees
    public function checkCategoryParentTrees()
    {
        $status = false;
        if ($this->builder->where('tree_id', NULL)->orWhere('tree_id', '')->orWhere('tree_id', 0)->countAllResults() > 0) {
            $status = true;
        }
        if ($this->builder->where('level', NULL)->orWhere('level', '')->orWhere('level', 0)->countAllResults() > 0) {
            $status = true;
        }
        if ($this->builder->where('parent_id != ', 0)->groupStart()->where('parent_tree', NULL)->orWhere('parent_tree', '')->groupEnd()->countAllResults() > 0) {
            $status = true;
        }
        if ($status == true) {
            $this->updateCategoriesParentTree();
        }
    }

    //update settings
    public function updateSettings()
    {
        $data = [
            'sort_categories' => inputPost('sort_categories'),
            'sort_parent_categories_by_order' => inputPost('sort_parent_categories_by_order')
        ];
        if (empty($data['sort_parent_categories_by_order'])) {
            $data['sort_parent_categories_by_order'] = 0;
        }
        return $this->db->table('general_settings')->where('id', 1)->update($data);
    }

    //get subcategories by parent id
    public function getSubCategoriesByParentId($parentId, $langId = null)
    {
        if (empty($langId)) {
            $langId = selectedLangId();
        }
        $this->buildQuery($langId, true);
        $this->builder->where('categories.parent_id', clrNum($parentId));
        $this->orderByCategories();
        return $this->builder->get()->getResult();
    }

    //get all parent categories by lang
    public function getAllParentCategoriesByLang($langId)
    {
        $this->buildQuery($langId, true);
        $this->builder->where('parent_id', 0);
        if ($this->generalSettings->sort_parent_categories_by_order == 1) {
            $this->builder->orderBy('category_order');
        } else {
            $this->orderByCategories();
        }
        return $this->builder->get()->getResult();
    }

    //get sitemap categories
    public function getSitemapCategories()
    {
        $this->buildQuery(selectedLangId(), true);
        return $this->builder->where('visibility', 1)->get()->getResult();
    }

    //generate CSV object
    public function generateCsvObject($filePath)
    {
        $array = array();
        $fields = array();
        $txtName = uniqid() . '.txt';
        $i = 0;
        $handle = fopen($filePath, 'r');
        if ($handle) {
            while (($row = fgetcsv($handle)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }
                foreach ($row as $k => $value) {
                    $array[$i][$fields[$k]] = $value;
                }
                $i++;
            }
            if (!feof($handle)) {
                return false;
            }
            fclose($handle);
            if (!empty($array)) {
                $txtFile = fopen(FCPATH . 'uploads/temp/' . $txtName, 'w');
                fwrite($txtFile, serialize($array));
                fclose($txtFile);
                $csvObject = new \stdClass();
                $csvObject->number_of_items = count($array);
                $csvObject->txt_file_name = $txtName;
                @unlink($filePath);
                return $csvObject;
            }
        }
        return false;
    }

    //import csv item
    public function importCsvItem($txtFileName, $index)
    {
        $filePath = FCPATH . 'uploads/temp/' . $txtFileName;
        $file = fopen($filePath, 'r');
        $content = fread($file, filesize($filePath));
        $array = @unserializeData($content);
        if (!empty($array)) {
            $i = 1;
            foreach ($array as $item) {
                if ($i == $index) {
                    $data = array();
                    $name = getCsvValue($item, 'name');
                    $data['id'] = getCsvValue($item, 'id', 'int');
                    $data['slug'] = getCsvValue($item, 'slug') ? getCsvValue($item, 'slug') : strSlug($name);
                    $data['parent_id'] = getCsvValue($item, 'parent_id', 'int');
                    $data['tree_id'] = 0;
                    $data['level'] = 1;
                    $data['parent_tree'] = '';
                    $data['title_meta_tag'] = '';
                    $data['description'] = getCsvValue($item, 'description');
                    $data['keywords'] = getCsvValue($item, 'keywords');
                    $data['category_order'] = getCsvValue($item, 'category_order', 'int');
                    $data['featured_order'] = $data['category_order'];
                    $data['visibility'] = 1;
                    $data['is_featured'] = 0;
                    $data['storage'] = 'local';
                    $data['image'] = '';
                    $data['show_image_on_main_menu'] = 0;
                    $data['created_at'] = date('Y-m-d H:i:s');
                    if ($this->builder->insert($data)) {
                        $lastId = $this->db->insertID();
                        //add category  name
                        $dataName = [
                            'category_id' => $lastId,
                            'lang_id' => selectedLangId(),
                            'name' => $name
                        ];
                        $this->db->table('categories_lang')->insert($dataName);
                        //update slug
                        $this->updateSlug($lastId);
                        //update category parent tree
                        $parentTree = '';
                        $treeId = 0;
                        $level = 1;
                        $category = $this->builder->where('id', $lastId)->get()->getRow();
                        if (!empty($category)) {
                            if ($category->parent_id == 0) {
                                $treeId = $category->id;
                            } else {
                                $parentCategory = $this->builder->where('id', $category->parent_id)->get()->getRow();
                                $level = $parentCategory->level + 1;
                                $treeId = $parentCategory->tree_id;
                                if (empty($parentCategory->parent_tree)) {
                                    $parentTree = $parentCategory->id;
                                } else {
                                    $parentTree = $parentCategory->parent_tree . ',' . $parentCategory->id;
                                }
                            }
                            $this->builder->where('id', $category->id)->update(['parent_tree' => $parentTree, 'tree_id' => $treeId, 'level' => $level]);
                        }
                        return $name;
                    }
                }
                $i++;
            }
        }
    }

    //search categories by name
    public function searchCategoriesByName($categoryName)
    {
        return $this->builder->select('categories.id, categories_lang.name as name')->join('categories_lang', 'categories_lang.category_id = categories.id')->like('name', cleanStr($categoryName))
            ->where('visibility', 1)->orderBy('categories.parent_id, categories_lang.name')->get()->getResult();
    }

    //set unset featured category
    public function setUnsetFeaturedCategory($categoryId)
    {
        $category = $this->getCategory($categoryId);
        if (!empty($category)) {
            if (inputPost('is_form') == 1) {
                $data['is_featured'] = 1;
            } else {
                $data['is_featured'] = 0;
            }
            if ($category->is_featured == 0) {
                $data['is_featured'] = 1;
            }
            return $this->builder->where('id', $category->id)->update($data);
        }
        return false;
    }

    //set unset index category
    public function setUnsetIndexCategory($categoryId)
    {
        $category = $this->getCategory($categoryId);
        if (!empty($category)) {
            if (inputPost('is_form') == 1) {
                $data['show_products_on_index'] = 1;
            } else {
                $data['show_products_on_index'] = 0;
            }
            if ($category->show_products_on_index == 0) {
                $data['show_products_on_index'] = 1;
            }
            $data['show_subcategory_products'] = inputPost('show_subcategory_products');
            if (empty($data['show_subcategory_products'])) {
                $data['show_subcategory_products'] = 0;
            }
            return $this->builder->where('id', $category->id)->update($data);
        }
        return false;
    }

    //edit featured categories order
    public function editFeaturedCategoriesOrder()
    {
        $categoryId = inputPost('category_id');
        $order = clrNum(inputPost('order'));
        $category = $this->getCategory($categoryId);
        if (!empty($category) && !empty($order)) {
            $data['featured_order'] = $order;
            $this->builder->where('id', $category->id)->update($data);
        }
    }

    //update index categories order
    public function editIndexCategoriesOrder()
    {
        $categoryId = inputPost('category_id');
        $order = clrNum(inputPost('order'));
        $category = $this->getCategory($categoryId);
        if (!empty($category) && !empty($order)) {
            $data['homepage_order'] = $order;
            $this->builder->where('id', $category->id)->update($data);
        }
    }

    //delete category image
    public function deleteCategoryImage($categoryId)
    {
        $category = $this->getCategory($categoryId);
        if (!empty($category)) {
            deleteFile($category->image);
            $this->builder->where('id', $category->id)->update(['image' => '']);
        }
    }

    //delete category
    public function deleteCategory($id)
    {
        $category = $this->getCategory($id);
        if (!empty($category)) {
            if ($category->storage == 'aws_s3') {
                $awsModel = new AwsModel();
                if (!empty($category->image)) {
                    $awsModel->deleteCategoryObject($category->image);
                }
            } else {
                deleteFile($category->image);
            }
            $this->db->table('categories_lang')->where('category_id', $category->id)->delete();
            return $this->builder->where('id', $category->id)->delete();
        }
        return false;
    }
}
