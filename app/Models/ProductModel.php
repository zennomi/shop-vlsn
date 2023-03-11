<?php namespace App\Models;

use CodeIgniter\Model;
use Config\Globals;

class ProductModel extends BaseModel
{
    protected $builder;
    protected $builderProductDetails;
    protected $builderProductLicenseKeys;
    protected $builderCustomFieldsProduct;
    protected $builderDigitalSales;
    protected $builderWishlist;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('products');
        $this->builderProductDetails = $this->db->table('product_details');
        $this->builderProductLicenseKeys = $this->db->table('product_license_keys');
        $this->builderCustomFieldsProduct = $this->db->table('custom_fields_product');
        $this->builderDigitalSales = $this->db->table('digital_sales');
        $this->builderWishlist = $this->db->table('wishlist');
    }

    //add product
    public function addProduct()
    {
        $data = [
            'slug' => strSlug(inputPost('title_' . selectedLangId())),
            'product_type' => inputPost('product_type'),
            'listing_type' => inputPost('listing_type'),
            'sku' => '',
            'price' => 0,
            'currency' => '',
            'discount_rate' => 0,
            'vat_rate' => 0,
            'user_id' => activeUserId(),
            'status' => 0,
            'is_promoted' => 0,
            'promote_start_date' => date('Y-m-d H:i:s'),
            'promote_end_date' => date('Y-m-d H:i:s'),
            'promote_plan' => 'none',
            'promote_day' => 0,
            'visibility' => 1,
            'rating' => 0,
            'pageviews' => 0,
            'demo_url' => '',
            'external_link' => '',
            'files_included' => '',
            'stock' => 1,
            'shipping_delivery_time_id' => 0,
            'multiple_sale' => 1,
            'is_deleted' => 0,
            'is_draft' => 1,
            'is_free_product' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        if (empty($data['sku'])) {
            $data['sku'] = '';
        }
        if (!empty($data['slug'])) {
            $data['slug'] = substr($data['slug'], 0, 200);
        }
        if (empty($data['multiple_sale'])) {
            $data['multiple_sale'] = 0;
        }
        //set category id
        $data['category_id'] = getDropdownCategoryId();

        if ($this->builder->insert($data)) {
            return $this->db->insertID();
        }
        return false;
    }

    //add product title and desc
    public function addProductTitleDesc($productId)
    {
        $mainTitle = inputPost('title_' . defaultLangId());
        $mainTitle = trim($mainTitle ?? '');
        foreach ($this->activeLanguages as $language) {
            $title = inputPost('title_' . $language->id);
            $title = trim($title ?? '');
            if (!empty($title)) {
                $data = [
                    'product_id' => $productId,
                    'lang_id' => $language->id,
                    'title' => !empty($title) ? $title : $mainTitle,
                    'description' => inputPost('description_' . $language->id),
                    'seo_title' => inputPost('seo_title_' . $language->id),
                    'seo_description' => inputPost('seo_description_' . $language->id),
                    'seo_keywords' => inputPost('seo_keywords_' . $language->id)
                ];
                $this->builderProductDetails->insert($data);
            }
        }
    }

    //edit product title and desc
    public function editProductTitleDesc($productId)
    {
        $mainTitle = inputPost('title_' . defaultLangId());
        $mainTitle = trim($mainTitle ?? '');
        foreach ($this->activeLanguages as $language) {
            $title = inputPost('title_' . $language->id);
            $title = trim($title ?? '');
            $data = [
                'product_id' => $productId,
                'lang_id' => $language->id,
                'title' => !empty($title) ? $title : $mainTitle,
                'description' => inputPost('description_' . $language->id),
                'seo_title' => inputPost('seo_title_' . $language->id),
                'seo_description' => inputPost('seo_description_' . $language->id),
                'seo_keywords' => inputPost('seo_keywords_' . $language->id)
            ];
            $row = getProductDetails($productId, $language->id, false);
            if (empty($row)) {
                $this->builderProductDetails->insert($data);
            } else {
                $this->builderProductDetails->where('product_id', clrNum($productId))->where('lang_id', $language->id)->update($data);
            }
        }
    }

    //edit product details
    public function editProductDetails($id)
    {
        $product = $this->getProduct($id);
        $data = [
            'sku' => inputPost('sku'),
            'price' => inputPost('price'),
            'currency' => inputPost('currency'),
            'discount_rate' => inputPost('discount_rate'),
            'vat_rate' => inputPost('vat_rate'),
            'demo_url' => inputPost('demo_url'),
            'external_link' => inputPost('external_link'),
            'files_included' => inputPost('files_included'),
            'stock' => inputPost('stock'),
            'shipping_class_id' => inputPost('shipping_class_id'),
            'shipping_delivery_time_id' => inputPost('shipping_delivery_time_id'),
            'multiple_sale' => inputPost('multiple_sale'),
            'is_free_product' => inputPost('is_free_product'),
            'is_draft' => 0
        ];
        $data['price'] = getPrice($data['price'], 'database');
        if (empty($data['price'])) {
            $data['price'] = 0;
        }
        if (empty($data['discount_rate'])) {
            $data['discount_rate'] = 0;
        }
        if (empty($data['vat_rate'])) {
            $data['vat_rate'] = 0;
        }
        if (empty($data['external_link'])) {
            $data['external_link'] = '';
        }
        if (empty($data['stock'])) {
            $data['stock'] = 0;
        }
        if (empty($data['shipping_class_id'])) {
            $data['shipping_class_id'] = 0;
        }
        if (empty($data['shipping_delivery_time_id'])) {
            $data['shipping_delivery_time_id'] = 0;
        }
        if (!empty($data['is_free_product'])) {
            $data['is_free_product'] = 1;
        } else {
            $data['is_free_product'] = 0;
        }
        //unset price if bidding system selected
        if ($this->generalSettings->bidding_system == 1) {
            $array['price'] = 0;
        }
        //validate sku
        $isSkuValid = true;
        if (!empty($data['sku'])) {
            $row = $this->builder->where('sku', removeSpecialCharacters($data['sku']))->where('id != ', clrNum($id))->where('user_id', clrNum(activeUserId()))->get()->getRow();
            if (!empty($row)) {
                $isSkuValid = false;
                $data['sku'] = '';
            }
        }
        if (inputPost('submit') == 'save_as_draft') {
            $data['is_draft'] = 1;
        } else {
            if ($this->generalSettings->approve_before_publishing == 0 || hasPermission('products')) {
                $data['status'] = 1;
            }
        }
        if ($this->builder->where('id', clrNum($id))->update($data)) {
            if ($isSkuValid == false) {
                setErrorMessage(trans("msg_error_sku"));
                return redirect()->back();
            }
            return true;
        }
        return false;
    }

    //edit product
    public function editProduct($product, $slug)
    {
        if (!empty($product)) {
            $data = [
                'product_type' => inputPost('product_type'),
                'listing_type' => inputPost('listing_type'),
                'slug' => $slug
            ];
            $data['category_id'] = getDropdownCategoryId();
            $data['is_sold'] = $product->is_sold;
            $data['visibility'] = $product->visibility;
            if ($product->is_draft != 1 && $product->status == 1) {
                $data['is_sold'] = inputPost('is_sold');
                $data['visibility'] = inputPost('visibility');
            }
            if (!empty($data['slug'])) {
                $data['slug'] = str_replace(' ', '-', $data['slug'] ?? '');
                $data['slug'] = removeSpecialCharacters(  $data['slug']);
                $data['slug'] = substr($data['slug'], 0, 200);
            }
            return $this->builder->where('id', $product->id)->update($data);
        }
    }

    //update custom fields
    public function updateProductCustomFields($productId)
    {
        $product = $this->getProduct($productId);
        if (!empty($product)) {
            $fieldModel = new FieldModel();
            $customFields = $fieldModel->getCustomFieldsByCategory($product->category_id);
            if (!empty($customFields)) {
                //delete previous custom field values
                $fieldModel->deleteFieldProductValuesByProductId($productId);
                foreach ($customFields as $customField) {
                    $inputValue = inputPost('field_' . $customField->id);
                    //add custom field values
                    if (!empty($inputValue)) {
                        if ($customField->field_type == 'checkbox') {
                            foreach ($inputValue as $key => $value) {
                                $data = [
                                    'field_id' => $customField->id,
                                    'product_id' => $productId,
                                    'product_filter_key' => $customField->product_filter_key
                                ];
                                $data['field_value'] = '';
                                $data['selected_option_id'] = $value;
                                $this->db->table('custom_fields_product')->insert($data);
                            }
                        } else {
                            $data = [
                                'field_id' => $customField->id,
                                'product_id' => clrNum($productId),
                                'product_filter_key' => $customField->product_filter_key,
                            ];
                            if ($customField->field_type == 'radio_button' || $customField->field_type == 'dropdown') {
                                $data['field_value'] = '';
                                $data['selected_option_id'] = $inputValue;
                            } else {
                                $data['field_value'] = $inputValue;
                                $data['selected_option_id'] = 0;
                            }
                            $this->db->table('custom_fields_product')->insert($data);
                        }
                    }
                }
            }
        }
    }

    //update slug
    public function updateSlug($id)
    {
        $product = $this->getProduct($id);
        if (!empty($product)) {
            if (empty($product->slug) || $product->slug == '-') {
                $data = ['slug' => $product->id];
            } else {
                if ($this->generalSettings->product_link_structure == 'id-slug') {
                    $data = ['slug' => $product->id . '-' . $product->slug];
                } else {
                    $data = ['slug' => $product->slug . '-' . $product->id];
                }
            }
            $pageModel = new PageModel();
            if (!empty($pageModel->checkPageSlugForProduct($data['slug']))) {
                $data['slug'] .= uniqid();
            }
            return $this->builder->where('id', $product->id)->update($data);
        }
    }

    //build sql query string
    public function buildQuery($type = 'active', $compileQuery = false)
    {
        $this->builder->resetQuery();
        $defaultLocation = Globals::$defaultLocation;
        $select = "products.*,
            users.username AS user_username, users.role_id AS role_id, users.slug AS user_slug,
            round(products.price - ((products.price * products.discount_rate)/100)) AS price_final,
            (SELECT title FROM product_details WHERE product_details.product_id = products.id AND product_details.lang_id = " . clrNum(selectedLangId()) . " LIMIT 1) AS title,
            (SELECT CONCAT(storage, '::', image_small) FROM images WHERE products.id = images.product_id ORDER BY is_main DESC LIMIT 1) AS image,
            (SELECT CONCAT(storage, '::', image_small) FROM images WHERE products.id = images.product_id ORDER BY is_main DESC LIMIT 1, 1) AS image_second,
            (SELECT COUNT(wishlist.id) FROM wishlist WHERE products.id = wishlist.product_id) AS wishlist_count,
            (SELECT variations.id FROM variations WHERE products.id = variations.product_id LIMIT 1) AS has_variation";
        if (countItems($this->activeLanguages) > 1) {
            $select .= ", (SELECT title FROM product_details WHERE product_details.product_id = products.id AND product_details.lang_id != " . clrNum(selectedLangId()) . " LIMIT 1) AS second_title";
        }
        if (authCheck()) {
            $select .= ", (SELECT COUNT(wishlist.id) FROM wishlist WHERE products.id = wishlist.product_id AND wishlist.user_id = " . clrNum(user()->id) . ") AS is_in_wishlist";
        } else {
            $select .= ", 0 AS is_in_wishlist";
        }
        $status = $type == 'draft' || $type == 'pending' ? 0 : 1;
        $visibility = $type == 'hidden' ? 0 : 1;
        $isSold = $type == 'sold' ? 1 : 0;
        $isDraft = $type == 'draft' ? 1 : 0;

        $this->builder->select($select);
        if ($compileQuery == true) {
            $this->builder->from('products');
        }
        if ($this->generalSettings->membership_plans_system == 1) {
            if ($type == 'expired') {
                $this->builder->join('users', 'products.user_id = users.id AND users.is_membership_plan_expired = 1');
            } else {
                $this->builder->join('users', 'products.user_id = users.id AND users.is_membership_plan_expired = 0');
            }
        } else {
            $this->builder->join('users', 'products.user_id = users.id');
        }
        if ($type == 'wishlist') {
            $this->builder->join('wishlist', 'products.id = wishlist.product_id');
        }
        $this->builder->where('users.banned', 0)->where('products.status', $status)->where('products.visibility', $visibility)->where('products.is_draft', $isDraft)->where('products.is_deleted', 0);
        if ($type == 'promoted') {
            $this->builder->where('products.is_promoted', 1);
        }
        if ($isSold == 1) {
            $this->builder->where('products.is_sold', 1);
        } else {
            if ($this->generalSettings->show_sold_products != 1) {
                $this->builder->where('products.is_sold', 0);
            }
        }
        if (!empty($defaultLocation->country_id)) {
            $this->builder->where('users.country_id', $defaultLocation->country_id);
        }
        if (!empty($defaultLocation->state_id)) {
            $this->builder->where('users.state_id', $defaultLocation->state_id);
        }
        if (!empty($defaultLocation->city_id)) {
            $this->builder->where('users.city_id', $defaultLocation->city_id);
        }
        if ($compileQuery == true) {
            return $this->builder->getCompiledSelect() . ' ';
        }
    }

    //filter products
    public function filterProducts($queryStringArray = null, $category = null, $customFilters = null, $userId = null)
    {
        $pMin = clrNum(inputGet('p_min'));
        $pMax = clrNum(inputGet('p_max'));
        $sort = strSlug(inputGet('sort'));
        $productType = removeSpecialCharacters(inputGet('product_type'));
        $search = removeSpecialCharacters(inputGet('search'));
        if (!empty($search)) {
            $array = explode(' ', $search);
            $arraySearchWords = array();
            foreach ($array as $item) {
                if (strlen($item) > 1) {
                    array_push($arraySearchWords, $item);
                }
            }
        }
        //check if custom filters selected
        $arraySelectedFilters = array();
        if (!empty($queryStringArray)) {
            foreach ($queryStringArray as $key => $arrayValues) {
                if ($key != 'product_type' && $key != 'p_min' && $key != 'p_max' && $key != 'sort' && $key != 'search') {
                    $keyId = getProductFilterIdByKey($customFilters, $key);
                    if (!empty($keyId)) {
                        $item = new \stdClass();
                        $item->id = $keyId;
                        $item->key = $key;
                        $item->array_values = $arrayValues;
                        array_push($arraySelectedFilters, $item);
                    }
                }
            }
        }
        if (!empty($arraySelectedFilters)) {
            $arrayQueries = array();
            foreach ($arraySelectedFilters as $filter) {
                $arrayQueries[] = $this->builderCustomFieldsProduct->join('custom_fields_options', 'custom_fields_options.id = custom_fields_product.selected_option_id')->select('product_id')
                    ->where('custom_fields_product.field_id', $filter->id)->groupStart()->whereIn('custom_fields_options.option_key', $filter->array_values)->groupEnd()->getCompiledSelect();
                $this->builderCustomFieldsProduct->resetQuery();
            }
            if (!empty($arrayQueries)) {
                $this->buildQuery();
                foreach ($arrayQueries as $query) {
                    $this->builder->where('products.id IN (' . $query . ')');
                }
            }
        } else {
            $this->buildQuery();
        }
        //is vendor products
        if (!empty($userId)) {
            $this->builder->where('products.user_id', clrNum($userId));
        }
        //add protuct filter options
        if (!empty($category)) {
            $this->builder->groupStart()->where('products.category_id', $category->id)->orWhere('products.category_id IN (SELECT id FROM (SELECT id, parent_tree FROM categories WHERE categories.visibility = 1 
            AND categories.tree_id = ' . clrNum($category->tree_id) . ') AS cat_tbl WHERE FIND_IN_SET(' . clrNum($category->id) . ', cat_tbl.parent_tree))')->groupEnd();
            if (empty($sort)) {
                $this->builder->orderBy('products.is_promoted DESC');
            }
        }
        if ($pMin != '' && $pMin != 0) {
            $this->builder->where('(products.price - ((products.price * products.discount_rate)/100)) >=', intval($pMin * 100));
        }
        if ($pMax != '' && $pMax != 0) {
            $this->builder->where('(products.price - ((products.price * products.discount_rate)/100)) <=', intval($pMax * 100));
        }
        if (!empty($arraySearchWords)) {
            $this->builder->join('product_details', 'product_details.product_id = products.id')->where('product_details.lang_id', selectedLangId())->groupStart();
            foreach ($arraySearchWords as $word) {
                if (!empty($word)) {
                    $this->builder->like('product_details.title', removeForbiddenCharacters($word));
                }
            }
            $this->builder->orLike('products.sku', cleanStr($search))->groupEnd();
            if (empty($sort)) {
                $this->builder->orderBy('products.is_promoted DESC');
            }
        }
        //sort products
        if (!empty($sort) && $sort == 'lowest_price') {
            $this->builder->orderBy('price_final');
        } elseif (!empty($sort) && $sort == 'highest_price') {
            $this->builder->orderBy('price_final DESC');
        } elseif (!empty($sort) && $sort == 'rating') {
            $this->builder->orderBy('rating DESC');
        } else {
            $this->builder->orderBy('products.created_at DESC');
        }
    }

    //search products (AJAX search)
    public function searchProducts($search, $category)
    {
        $categoryModel = new CategoryModel();
        if (!empty($search)) {
            if ($category != 'all') {
                $categoryId = clrNum($category);
                $categoryIds = $categoryModel->getSubCategoriesTreeIds($categoryId, true, true);
            }
            $array = explode(' ', $search ?? '');
            $str = '';
            $array_like = array();
            $this->buildQuery();
            $this->builder->join('product_details', 'product_details.product_id = products.id')->where('product_details.lang_id', selectedLangId());
            if (!empty($categoryIds)) {
                $this->builder->whereIn('products.category_id', $categoryIds, FALSE);
            }
            $this->builder->groupStart();
            foreach ($array as $item) {
                if (strlen($item) > 1) {
                    $this->builder->like('product_details.title', cleanStr($item));
                }
            }
            return $this->builder->orLike('products.sku', cleanStr($search))->groupEnd()->orderBy('products.is_promoted DESC')->limit(10)->get()->getResult();
        }
        return array();
    }

    //get products
    public function getProducts($limit)
    {
        $key = 'products_limit_' . clrNum($limit);
        $products = getCacheProduct($key);
        if (!empty($products)) {
            return $products;
        }
        $this->buildQuery();
        $products = $this->builder->orderBy('products.created_at DESC')->get(clrNum($limit))->getResult();
        setCacheProduct($key, $products);
        return $products;
    }

    //get promoted products
    public function getPromotedProducts()
    {
        $this->buildQuery('promoted');
        return $this->builder->orderBy('products.promote_start_date', 'DESC')->get()->getResult();
    }

    //get promoted products
    public function getPromotedProductsLimited($perPage, $offset)
    {
        $key = 'promoted_products_' . $perPage . '_' . $offset;
        $products = getCacheProduct($key);
        if (!empty($products)) {
            return $products;
        }
        $this->buildQuery('promoted');
        $products = $this->builder->orderBy('products.promote_start_date DESC')->limit(clrNum($perPage), clrNum($offset))->get()->getResult();
        setCacheProduct($key, $products);
        return $products;
    }

    //get promoted products count
    public function getPromotedProductsCount()
    {
        $key = 'promoted_products_count';
        $result = getCacheProduct($key);
        if (!empty($result)) {
            return $result;
        }
        $this->buildQuery('promoted');
        $result = $this->builder->countAllResults();
        setCacheProduct($key, $result);
        return $result;
    }

    //check promoted products
    public function checkPromotedProducts()
    {
        $products = $this->builder->where('is_promoted', 1)->get()->getResult();
        if (!empty($products)) {
            foreach ($products as $item) {
                if (dateDifference($item->promote_end_date, date('Y-m-d H:i:s')) < 1) {
                    $this->builder->where('id', $item->id)->update(['is_promoted' => 0]);
                }
            }
        }
    }

    //get special offers
    public function getSpecialOffers()
    {
        $products = getCacheProduct('special_offers');
        if (!empty($products)) {
            return $products;
        }
        $this->buildQuery();
        $products = $this->builder->where('products.is_special_offer', 1)->orderBy('products.special_offer_date', 'DESC')->limit(20)->get()->getResult();
        setCacheProduct('special_offers', $products);
        return $products;
    }

    //get index categories products
    public function getIndexCategoriesProducts($categories)
    {
        $productsArray = getCacheProduct('index_category_products');
        if (!empty($productsArray)) {
            return $productsArray;
        }
        $limit = 15;
        $productsArray = array();
        if (!empty($categories)) {
            foreach ($categories as $category) {
                if ($category->show_subcategory_products == 1) {
                    $this->buildQuery();
                    $productsArray[$category->id] = $this->builder->groupStart()->where("products.category_id IN (SELECT id FROM categories WHERE FIND_IN_SET(" . clrNum($category->id) . ", categories.parent_tree))")
                        ->orWhere("products.category_id", clrNum($category->id))->groupEnd()->orderBy('products.created_at', 'DESC')->limit($limit)->get()->getResult();
                } else {
                    $this->buildQuery();
                    $productsArray[$category->id] = $this->builder->where('products.category_id', clrNum($category->id), false)->orderBy('products.created_at', 'DESC')->limit($limit)->get()->getResult();
                }
            }
        }
        setCacheProduct('index_category_products', $productsArray);
        return $productsArray;
    }

    //get filtered products count
    public function getFilteredProductsCount($queryStringArray, $category, $customFilters)
    {
        $this->filterProducts($queryStringArray, $category, $customFilters);
        return $this->builder->countAllResults();
    }

    //get paginated filtered products
    public function getFilteredProductsPaginated($queryStringArray, $category, $customFilters, $perPage, $offset)
    {
        $this->filterProducts($queryStringArray, $category, $customFilters);
        return $this->builder->limit($perPage, $offset)->get()->getResult();
    }

    //get profile products count
    public function getProfileProductsCount($userId, $category)
    {
        $this->filterProducts(null, $category, null, $userId);
        return $this->builder->countAllResults();
    }

    //get paginated profile products
    public function getProfileProductsPaginated($userId, $category, $perPage, $offset)
    {
        $this->filterProducts(null, $category, null, $userId);
        return $this->builder->limit($perPage, $offset)->get()->getResult();
    }

    //get related products
    public function getRelatedProducts($productId, $categoryId)
    {
        $key = 'related_products_' . $productId;
        $products = getCacheProduct($key);
        if (!empty($products)) {
            return $products;
        }
        $this->buildQuery();
        $products = $this->builder->where('products.category_id', clrNum($categoryId))->where('products.id !=', clrNum($productId))->orderBy('rand()')->get(5)->getResult();
        setCacheProduct($key, $products);
        return $products;
    }

    //get more products by user
    public function getMoreProductsByUser($userId, $productId)
    {
        $key = 'more_products_by_vendor_' . $userId;
        $products = getCacheProduct($key);
        if (!empty($products)) {
            return $products;
        }
        $this->buildQuery();
        $products = $this->builder->where('users.id', clrNum($userId))->where('products.id != ', clrNum($productId))->orderBy('products.created_at DESC')->get(6)->getResult();
        setCacheProduct($key, $products);
        return $products;
    }

    //get user products count
    public function getUserTotalProductsCount($userId)
    {
        $this->buildQuery();
        return $this->builder->where('users.id', clrNum($userId))->countAllResults();
    }

    //get vendor products count
    public function getVendorProductsCount($userId, $listType)
    {
        $this->filterUserProducts($listType);
        return $this->builder->where('users.id', clrNum($userId))->countAllResults();
    }

    //get vendor products
    public function getVendorProductsPaginated($userId, $listType, $perPage, $offset)
    {
        $this->filterUserProducts($listType);
        return $this->builder->where('users.id', clrNum($userId))->orderBy('products.created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter user products
    public function filterUserProducts($listType)
    {
        $productType = inputGet('product_type');
        $category = clrNum(inputGet('category'));
        $subCategory = clrNum(inputGet('subcategory'));
        $stock = inputGet('stock');
        $q = removeSpecialCharacters(inputGet('q'));

        $categoryIds = array();
        $categoryId = $category;
        if (!empty($subCategory)) {
            $categoryId = $subCategory;
        }
        $categoryModel = new CategoryModel();
        if (!empty($categoryId)) {
            $categoryIds = $categoryModel->getSubCategoriesTreeIds($categoryId, true, true);
        }
        if ($listType == 'pending') {
            $this->buildQuery('pending', false, false);
        } elseif ($listType == 'draft') {
            $this->buildQuery('draft', false, false);
        } elseif ($listType == 'hidden') {
            $this->buildQuery('hidden', false, false);
        } elseif ($listType == 'expired') {
            $this->buildQuery('expired', false, false);
        } elseif ($listType == 'sold') {
            $this->buildQuery('sold', false, false);
        } else {
            $this->buildQuery('active', false, false);
        }
        if ($productType == 'physical' || $productType == 'digital') {
            $this->builder->where('products.product_type', $productType);
        }
        if (!empty($categoryIds)) {
            $this->builder->whereIn("products.category_id", $categoryIds, FALSE);
        }
        if ($stock == 'in_stock' || $stock == 'out_of_stock') {
            $this->builder->groupStart();
            if ($stock == 'out_of_stock') {
                $this->builder->where("products.product_type = 'physical' AND products.stock <=", 0);
            } else {
                $this->builder->where("products.product_type = 'digital' OR products.stock >", 0);
            }
            $this->builder->groupEnd();
        }
        if (!empty($q)) {
            $this->builder->join('product_details', 'product_details.product_id = products.id')->where('product_details.lang_id', selectedLangId())->groupStart()
                ->like('product_details.title', $q)->orLike('products.sku', $q)->orLike('products.promote_plan', $q)->groupEnd();
        }
    }

    //get user wishlist products count
    public function getUserWishlistProductsCount($userId)
    {
        $this->buildQuery('wishlist');
        return $this->builder->where('wishlist.user_id', clrNum($userId))->countAllResults();
    }

    //get user wishlist products
    public function getPaginatedUserWishlistProducts($userId, $perPage, $offset)
    {
        $this->buildQuery('wishlist');
        return $this->builder->where('wishlist.user_id', clrNum($userId))->orderBy('products.created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get guest wishlist products count
    public function getGuestWishlistProductsCount()
    {
        $wishlist = helperGetSession('mds_guest_wishlist');
        if (!empty($wishlist) && countItems($wishlist) > 0) {
            $this->buildQuery();
            return $this->builder->whereIn('products.id', $wishlist, FALSE)->countAllResults();
        }
        return 0;
    }

    //get guest wishlist products
    public function getGuestWishlistProductsPaginated($perPage, $offset)
    {
        $wishlist = helperGetSession('mds_guest_wishlist');
        if (!empty($wishlist) && countItems($wishlist) > 0) {
            $this->buildQuery();
            return $this->builder->whereIn('products.id', $wishlist, FALSE)->orderBy('products.created_at DESC')->limit($perPage, $offset)->get()->getResult();
        }
        return array();
    }

    //get user downloads count
    public function getUserDownloadsCount($userId)
    {
        return $this->builderDigitalSales->where('buyer_id', clrNum($userId))->countAllResults();
    }

    //get paginated downloads
    public function getUserDownloadsPaginated($userId, $perPage, $offset)
    {
        return $this->builderDigitalSales->where('buyer_id', clrNum($userId))->orderBy('purchase_date DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get digital sale
    public function getDigitalSale($saleId)
    {
        return $this->builderDigitalSales->where('id', clrNum($saleId))->get()->getRow();
    }

    //get digital sale by buyer id
    public function getDigitalSaleByBuyerId($buyerId, $productId)
    {
        return $this->builderDigitalSales->where('buyer_id', clrNum($buyerId))->where('product_id', clrNum($productId))->get()->getRow();
    }

    //get digital sale by order id
    public function getDigitalSaleByOrderId($buyerId, $productId, $orderId)
    {
        return $this->builderDigitalSales->where('buyer_id', clrNum($buyerId))->where('product_id', clrNum($productId))->where('order_id', clrNum($orderId))->get()->getRow();
    }

    //get product by id
    public function getProduct($id)
    {
        $this->builder->select("products.*, 
        (SELECT title FROM product_details WHERE product_details.product_id = products.id AND product_details.lang_id = " . clrNum(selectedLangId()) . " LIMIT 1) AS title");
        if (countItems($this->activeLanguages) > 1) {
            $this->builder->select('(SELECT title FROM product_details WHERE product_details.product_id = products.id AND product_details.lang_id != ' . clrNum(selectedLangId()) . ' LIMIT 1) AS second_title');
        }
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get product with details
    public function getProductWithDetails($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get available product
    public function getActiveProduct($id)
    {
        $this->buildQuery();
        return $this->builder->where('products.id', clrNum($id))->get()->getRow();
    }

    //get product by slug
    public function getProductBySlug($slug)
    {
        if ($this->generalSettings->membership_plans_system == 1) {
            $this->builder->join('users', 'products.user_id = users.id AND users.banned = 0 AND users.is_membership_plan_expired = 0');
        } else {
            $this->builder->join('users', 'products.user_id = users.id AND users.banned = 0');
        }
        $this->builder->select('products.*, users.username as user_username, users.role_id as user_role, users.slug as user_slug')
            ->where('products.slug', strSlug($slug))->where('products.is_draft', 0)->where('products.is_deleted', 0);
        if ($this->generalSettings->show_sold_products != 1) {
            $this->builder->where('products.is_sold', 0);
        }
        if ($this->generalSettings->vendor_verification_system == 1) {
            $this->builder->where('users.role_id != ', 'member');
        }
        return $this->builder->get()->getRow();
    }

    //get product details
    public function getProductDetails($id, $langId, $getMainOnNull = true)
    {
        $row = $this->builderProductDetails->where('product_id', clrNum($id))->where('lang_id', clrNum($langId))->get()->getRow();
        if ((empty($row) || empty($row->title)) && $getMainOnNull == true) {
            $row = $this->builderProductDetails->where('product_id', clrNum($id))->limit(1)->get()->getRow();
        }
        return $row;
    }

    //is product in wishlist
    public function isProductInWishlist($productId)
    {
        if (authCheck()) {
            if (!empty($this->builderWishlist->where('user_id', user()->id)->where('product_id', clrNum($productId))->get()->getRow())) {
                return true;
            }
        } else {
            $wishlist = $this->session->get('mds_guest_wishlist');
            if (!empty($wishlist)) {
                if (in_array($productId, $wishlist)) {
                    return true;
                }
            }
        }
        return false;
    }

    //get product wishlist count
    public function getProductWishlistCount($productId)
    {
        return $this->builderWishlist->where('product_id', clrNum($productId))->countAllResults();
    }

    //add remove wishlist
    public function addRemoveWishlist($productId)
    {
        if (authCheck()) {
            if ($this->isProductInWishlist($productId)) {
                $this->builderWishlist->where('user_id', user()->id)->where('product_id', clrNum($productId))->delete();
            } else {
                $data = [
                    'user_id' => user()->id,
                    'product_id' => clrNum($productId)
                ];
                $this->builderWishlist->insert($data);
            }
        } else {
            if ($this->isProductInWishlist($productId)) {
                $wishlist = array();
                if (!empty(helperGetSession('mds_guest_wishlist'))) {
                    $wishlist = helperGetSession('mds_guest_wishlist');
                }
                $new = array();
                if (!empty($wishlist)) {
                    foreach ($wishlist as $item) {
                        if ($item != clrNum($productId)) {
                            array_push($new, $item);
                        }
                    }
                }
                helperSetSession('mds_guest_wishlist', $new);
            } else {
                $wishlist = array();
                if (!empty(helperGetSession('mds_guest_wishlist'))) {
                    $wishlist = helperGetSession('mds_guest_wishlist');
                }
                array_push($wishlist, clrNum($productId));
                helperSetSession('mds_guest_wishlist', $wishlist);
            }
        }
    }

    //get vendor total pageviews count
    public function getVendorTotalPageviewsCount($userId)
    {
        return $this->builder->select('SUM(products.pageviews) as total_pageviews')->where('status', 1)->where('products.is_draft', 0)
            ->where('products.is_deleted', 0)->where('products.user_id', clrNum($userId))->get()->getRow()->total_pageviews;
    }

    //get vendor most viewed products
    public function getVendorMostViewedProducts($userId, $limit)
    {
        $this->buildQuery();
        return $this->builder->where('products.user_id', clrNum($userId))->orderBy('products.pageviews DESC')->limit(clrNum($limit))->get()->getResult();
    }

    //increase product pageviews
    public function increaseProductPageviews($product)
    {
        if (!empty($product)) {
            if (empty(helperGetCookie('pr_' . $product->id))) {
                helperSetCookie('pr_' . $product->id, '1', time() + (86400 * 300));
                $this->builder->where('id', $product->id)->update(['pageviews' => $product->pageviews + 1]);
            }
        }
    }

    //get rss products by category
    public function getRssProductsByCategory($categoryId)
    {
        $categoryModel = new CategoryModel();
        $categoryIds = $categoryModel->getSubCategoriesTreeIds($categoryId, true, true);
        if (empty($categoryIds) || countItems($categoryIds) < 1) {
            return array();
        }
        $this->buildQuery();
        return $this->builder->whereIn('products.category_id', $categoryIds, FALSE)->orderBy('products.created_at DESC')->get()->getResult();
    }

    //get rss products by user
    public function getRssProductsByUser($userId)
    {
        $this->buildQuery();
        return $this->builder->where('users.id', clrNum($userId))->orderBy('products.created_at DESC')->get()->getResult();
    }

    //get products
    public function getSitemapProducts()
    {
        $this->buildQuery();
        return $this->builder->orderBy('products.created_at')->get()->getResult();
    }

    /*
     * --------------------------------------------------------------------
     * License Keys
     * --------------------------------------------------------------------
     */

    //add license keys
    public function addLicenseKeys($productId)
    {
        $licenseKeys = inputPost('license_keys');
        $allowDuplicate = inputPost('allow_duplicate');
        $licenseKeysArray = explode(',', $licenseKeys ?? '');
        if (!empty($licenseKeysArray)) {
            foreach ($licenseKeysArray as $licenseKey) {
                $licenseKey = trim($licenseKey);
                if (!empty($licenseKey)) {
                    //check duplicate
                    $addKey = true;
                    if (empty($allowDuplicate)) {
                        if (!empty($this->checkLicenseKey($productId, $licenseKey))) {
                            $addKey = false;
                        }
                    }
                    //add license key
                    if ($addKey) {
                        $data = [
                            'product_id' => $productId,
                            'license_key' => trim($licenseKey ?? ''),
                            'is_used' => 0
                        ];
                        $this->builderProductLicenseKeys->insert($data);
                    }
                }
            }
        }
    }

    //get license keys
    public function getProductLicenseKeys($productId)
    {
        return $this->builderProductLicenseKeys->where('product_id', clrNum($productId))->get()->getResult();
    }

    //get license key
    public function getLicenseKey($id)
    {
        return $this->builderProductLicenseKeys->where('id', clrNum($id))->get()->getRow();
    }

    //get unused license key
    public function getUnusedLicenseKey($productId)
    {
        return $this->builderProductLicenseKeys->where('product_id', clrNum($productId))->where('is_used = 0')->get()->getRow();
    }

    //check license key
    public function checkLicenseKey($productId, $licenseKey)
    {
        return $this->builderProductLicenseKeys->where('product_id', clrNum($productId))->where('license_key', $licenseKey)->get()->getRow();
    }

    //set license key used
    public function setLicenseKeyUsed($id)
    {
        $this->builderProductLicenseKeys->where('id', clrNum($id))->update(['is_used' => 1]);
    }

    //delete license key
    public function deleteLicenseKey($id)
    {
        $licenseKey = $this->getLicenseKey($id);
        if (!empty($licenseKey)) {
            return $this->builderProductLicenseKeys->where('id', $licenseKey->id)->delete();
        }
        return false;
    }

}
