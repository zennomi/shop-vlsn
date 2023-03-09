<?php

use Config\Globals;

//get category by id
if (!function_exists('getCategory')) {
    function getCategory($id)
    {
        $model = new \App\Models\CategoryModel();
        return $model->getCategory($id);
    }
}

//get subcategories by id
if (!function_exists('getSubCategoriesByParentId')) {
    function getSubCategoriesByParentId($parentId)
    {
        $model = new \App\Models\CategoryModel();
        return $model->getSubCategoriesByParentId($parentId);
    }
}

//get category name
if (!function_exists('getCategoryName')) {
    function getCategoryName($category, $esc = true)
    {
        if (!empty($category)) {
            if (!empty($category->name)) {
                if ($esc) {
                    return esc($category->name);
                }
                return $category->name;
            } else {
                if (!empty($category->second_name)) {
                    if ($esc) {
                        return esc($category->second_name);
                    }
                    return $category->second_name;
                }
            }
        }
        return '';
    }
}

//get category parent tree
if (!function_exists('getCategoryParentTree')) {
    function getCategoryParentTree($category, $onlyVisible = true)
    {
        $model = new \App\Models\CategoryModel();
        return $model->getCategoryParentTree($category, $onlyVisible);
    }
}

//get dropdown category id
if (!function_exists('getDropdownCategoryId')) {
    function getDropdownCategoryId()
    {
        $categoryId = 0;
        $categoryIds = inputPost('category_id');
        if (!empty($categoryIds)) {
            $categoryIds = array_reverse($categoryIds);
            foreach ($categoryIds as $id) {
                if (!empty($id)) {
                    $categoryId = $id;
                    break;
                }
            }
        }
        return $categoryId;
    }
}

//get product
if (!function_exists('getProduct')) {
    function getProduct($id)
    {
        $model = new \App\Models\ProductModel();
        return $model->getProduct($id);
    }
}

//get active product
if (!function_exists('getActiveProduct')) {
    function getActiveProduct($id)
    {
        $model = new \App\Models\ProductModel();
        return $model->getActiveProduct($id);
    }
}

//get product details
if (!function_exists('getProductDetails')) {
    function getProductDetails($id, $langId, $getMainOnNull = true)
    {
        $model = new \App\Models\ProductModel();
        return $model->getProductDetails($id, $langId, $getMainOnNull);
    }
}

//is product in wishlist
if (!function_exists('isProductInWishlist')) {
    function isProductInWishlist($product)
    {
        if (authCheck()) {
            if (!empty($product->is_in_wishlist)) {
                return true;
            }
        } else {
            $session = \Config\Services::session();
            $wishlist = $session->get('mds_guest_wishlist');
            if (!empty($wishlist)) {
                if (in_array($product->id, $wishlist)) {
                    return true;
                }
            }
        }
        return false;
    }
}

//get product title
if (!function_exists('getProductTitle')) {
    function getProductTitle($product)
    {
        if (!empty($product)) {
            if (!empty($product->title)) {
                return esc($product->title);
            } elseif (!empty($product->second_title)) {
                return esc($product->second_title);
            }
        }
        return '';
    }
}

//get product main image
if (!function_exists('getProductMainImage')) {
    function getProductMainImage($productId, $sizeName)
    {
        $model = new \App\Models\FileModel();
        $image = $model->getProductMainImage($productId);
        if (!empty($image)) {
            if ($image->storage == 'aws_s3') {
                return getAWSBaseUrl() . 'uploads/images/' . $image->$sizeName;
            } else {
                return base_url('uploads/images/' . $image->$sizeName);
            }
        }
        return base_url('assets/img/no-image.jpg');
    }
}

//get product item image
if (!function_exists('getProductItemImage')) {
    function getProductItemImage($product, $getSecond = false)
    {
        if (!empty($product)) {
            $image = $product->image;
            if (!empty($product->image_second) && $getSecond == true) {
                $image = $product->image_second;
            }
            if (!empty($image)) {
                $imageArray = explode('::', $image);
                if (!empty($imageArray[0]) && !empty($imageArray[1])) {
                    if ($imageArray[0] == 'aws_s3') {
                        return getAWSBaseUrl() . 'uploads/images/' . $imageArray[1];
                    } else {
                        return base_url('uploads/images/' . $imageArray[1]);
                    }
                }
            }
        }
        return base_url('assets/img/no-image.jpg');
    }
}

//get product image url
if (!function_exists('getProductImageURL')) {
    function getProductImageURL($image, $sizeName)
    {
        if ($image->storage == 'aws_s3') {
            return getAWSBaseUrl() . 'uploads/images/' . $image->$sizeName;
        }
        return base_url('uploads/images/' . $image->$sizeName);
    }
}

//get product images
if (!function_exists('getProductImages')) {
    function getProductImages($productId)
    {
        $model = new \App\Models\FileModel();
        return $model->getProductImages($productId);
    }
}

//get product listing type
if (!function_exists('getProductListingType')) {
    function getProductListingType($product)
    {
        if (!empty($product)) {
            if ($product->listing_type == 'sell_on_site') {
                return trans("add_product_for_sale");
            }
            if ($product->listing_type == 'ordinary_listing') {
                return trans("add_product_services_listing");
            }
        }
    }
}

//get product video url
if (!function_exists('getProductVideoUrl')) {
    function getProductVideoUrl($video)
    {
        $path = '';
        if (!empty($video)) {
            if ($video->storage == 'aws_s3') {
                $path = getAWSBaseUrl() . 'uploads/videos/' . $video->file_name;
            } else {
                $path = base_url('uploads/videos/' . $video->file_name);
            }
        }
        return $path;
    }
}

//get product audio url
if (!function_exists('getProductAudioUrl')) {
    function getProductAudioUrl($audio)
    {
        $path = '';
        if (!empty($audio)) {
            if ($audio->storage == 'aws_s3') {
                $path = getAWSBaseUrl() . 'uploads/audios/' . $audio->file_name;
            } else {
                $path = base_url('uploads/audios/' . $audio->file_name);
            }
        }
        return $path;
    }
}

//check sell active
if (!function_exists('isSaleActive')) {
    function isSaleActive()
    {
        if (Globals::$generalSettings->marketplace_system == 1 || Globals::$generalSettings->bidding_system == 1) {
            return true;
        }
        return false;
    }
}

//get thousands separator
if (!function_exists('getThousandsSeparator')) {
    function getThousandsSeparator()
    {
        $thousandsSeparator = '.';
        if (Globals::$defaultCurrency->currency_format == 'european') {
            $thousandsSeparator = ',';
        }
        return $thousandsSeparator;
    }
}

//calculate product price
if (!function_exists('calculateProductPrice')) {
    function calculateProductPrice($price, $discountRate)
    {
        if (!empty($price)) {
            $price = $price / 100;
            if (!empty($discountRate)) {
                $price = $price - (($price * $discountRate) / 100);
            }
            if (!empty($price)) {
                $price = number_format($price, 2, '.', '');
            }
            return $price * 100;
        }
        return 0;
    }
}

//calculate product vat
if (!function_exists('calculateProductVat')) {
    function calculateProductVat($product)
    {
        if (!empty($product)) {
            if (!empty($product->vat_rate)) {
                $price = calculateProductPrice($product->price, $product->discount_rate);
                return ($price * $product->vat_rate) / 100;
            }
        }
        return 0;
    }
}

//price formatted
if (!function_exists('priceFormatted')) {
    function priceFormatted($price, $currencyCode, $convertCurrency = false)
    {
        $price = $price / 100;
        //convert currency
        if (Globals::$paymentSettings->currency_converter == 1 && $convertCurrency == true) {
            $rate = 1;
            $selectedCurrency = getSelectedCurrency();
            if (isset($selectedCurrency) && isset($selectedCurrency->exchange_rate)) {
                $rate = $selectedCurrency->exchange_rate;
                $price = $price * $rate;
                $currencyCode = $selectedCurrency->code;
            }
        }
        $decPoint = '.';
        $thousandsSep = ',';
        if (!empty(Globals::$currencies[$currencyCode]) && Globals::$currencies[$currencyCode]->currency_format != 'us') {
            $decPoint = ',';
            $thousandsSep = '.';
        }
        if (!empty($price)) {
            if (filter_var($price, FILTER_VALIDATE_INT) !== false) {
                $price = number_format($price, 0, $decPoint, $thousandsSep);
            } else {
                $price = number_format($price, 2, $decPoint, $thousandsSep);
            }
        }
        return priceCurrencyFormat($price, $currencyCode);
    }
}

//price cart
if (!function_exists('priceDecimal')) {
    function priceDecimal($price, $currencyCode, $convertCurrency = false, $moneySign = true)
    {
        //convert currency
        if (Globals::$paymentSettings->currency_converter == 1 && $convertCurrency == true) {
            $rate = 1;
            $selectedCurrency = getSelectedCurrency();
            if (isset($selectedCurrency) && isset($selectedCurrency->exchange_rate)) {
                $rate = $selectedCurrency->exchange_rate;
                $price = $price * $rate;
                $currencyCode = $selectedCurrency->code;
            }
        }
        $decPoint = '.';
        $thousandsSep = ',';
        if (!empty(Globals::$currencies[$currencyCode]) && Globals::$currencies[$currencyCode]->currency_format != 'us') {
            $decPoint = ',';
            $thousandsSep = '.';
        }
        if (!empty($price)) {
            if (strpos($price, '.00') !== false) {
                $price = str_replace('.00', '', $price);
            }
            if (filter_var($price, FILTER_VALIDATE_INT) !== false) {
                $price = number_format($price, 0, $decPoint, $thousandsSep);
            } else {
                $price = number_format($price, 2, $decPoint, $thousandsSep);
            }
        }
        if ($moneySign == false) {
            return $price;
        }
        return priceCurrencyFormat($price, $currencyCode);
    }
}

//price currency format
if (!function_exists('priceCurrencyFormat')) {
    function priceCurrencyFormat($price, $currencyCode)
    {
        if (!empty(Globals::$currencies[$currencyCode])) {
            $currency = Globals::$currencies[$currencyCode];
            $space = '';
            if ($currency->space_money_symbol == 1) {
                $space = ' ';
            }
            if ($currency->symbol_direction == 'left') {
                $price = '<span>' . $currency->symbol . '</span>' . $space . $price;
            } else {
                $price = $price . $space . '<span>' . $currency->symbol . '</span>';
            }
        }
        return $price;
    }
}

//get price
if (!function_exists('getPrice')) {
    function getPrice($price, $formatType)
    {
        if (!empty($price)) {
            if ($formatType == 'input') {
                $price = $price / 100;
                if (filter_var($price, FILTER_VALIDATE_INT) !== false) {
                    $price = number_format($price, 0, '.', '');
                } else {
                    $price = number_format($price, 2, '.', '');
                }
                if (!empty($price) && getThousandsSeparator() == ',') {
                    $price = str_replace('.', ',', $price);
                }
                return $price;
            } elseif ($formatType == 'decimal') {
                $price = $price / 100;
                if (filter_var($price, FILTER_VALIDATE_INT) !== false) {
                    return number_format($price, 0, '.', '');
                } else {
                    return number_format($price, 2, '.', '');
                }
            } elseif ($formatType == 'database') {
                $price = str_replace(',', '.', $price);
                if (!empty($price)) {
                    $price = floatval($price);
                }
                if (!empty($price)) {
                    $price = number_format($price, 2, '.', '') * 100;
                }
                return $price;
            } elseif ($formatType == 'separator_format') {
                $price = $price / 100;
                $decPoint = '.';
                $thousandsSep = ',';
                if (getThousandsSeparator() != '.') {
                    $decPoint = ',';
                    $thousandsSep = '.';
                }
                return number_format($price, 2, $decPoint, $thousandsSep);
            }
        }
        return 0;
    }
}

//convert currency for payments in the cart
if (!function_exists('convertCurrencyByExchangeRate')) {
    function convertCurrencyByExchangeRate($amount, $exchangeRate)
    {
        if ($amount <= 0) {
            return 0;
        }
        if (empty($exchangeRate)) {
            $exchangeRate = 1;
        }
        if (Globals::$paymentSettings->currency_converter == 1) {
            $amount = $amount * $exchangeRate;
            if (!empty($amount)) {
                if (filter_var($amount, FILTER_VALIDATE_INT) !== false) {
                    $amount = number_format($amount, 0, '.', '');
                } else {
                    $amount = number_format($amount, 2, '.', '');
                }
            }
        }
        return $amount;
    }
}

//get unread conversations count
if (!function_exists('getUnreadConversationsCount')) {
    function getUnreadConversationsCount($receiverId)
    {
        $model = new \App\Models\MessageModel();
        return countItems($model->getUnreadConversations($receiverId));
    }
}

//cart product count
if (!function_exists('getCartProductCount')) {
    function getCartProductCount()
    {
        $session = \Config\Services::session();
        if (!empty($session->get('mds_shopping_cart'))) {
            return countItems($session->get('mds_shopping_cart'));
        }
        return 0;
    }
}

//get blog image url
if (!function_exists('getBlogImageURL')) {
    function getBlogImageURL($post, $sizeName)
    {
        if (!empty($post)) {
            if ($post->storage == 'aws_s3') {
                return getAWSBaseUrl() . $post->$sizeName;
            } else {
                return base_url($post->$sizeName);
            }
        }
    }
}

//get file manager image
if (!function_exists('getFileManagerImageUrl')) {
    function getFileManagerImageUrl($image)
    {
        $path = base_url('assets/img/no-image.jpg');
        if (!empty($image)) {
            if ($image->storage == 'aws_s3') {
                $path = getAWSBaseUrl() . 'uploads/images-file-manager/' . $image->image_path;
            } else {
                $path = base_url('uploads/images-file-manager/' . $image->image_path);
            }
        }
        return $path;
    }
}

//get blog content image
if (!function_exists('getBlogFileManagerImage')) {
    function getBlogFileManagerImage($image)
    {
        $path = base_url('assets/img/no-image.jpg');
        if (!empty($image)) {
            if ($image->storage == 'aws_s3') {
                $path = getAWSBaseUrl() . $image->image_path;
            } else {
                $path = base_url($image->image_path);
            }
        }
        return $path;
    }
}

//get new quote requests count
if (!function_exists('getNewQuoteRequestsCount')) {
    function getNewQuoteRequestsCount($userId)
    {
        $model = new \App\Models\BiddingModel();
        return $model->getNewQuoteRequestsCount($userId);
    }
}

//get seller active refund requests count
if (!function_exists('getSellerActiveRefundRequestCount')) {
    function getSellerActiveRefundRequestCount($userId)
    {
        $model = new \App\Models\OrderModel();
        return $model->getSellerActiveRefundRequestCount($userId);
    }
}

//get coupon products array
if (!function_exists('getCouponProductsArray')) {
    function getCouponProductsArray($order)
    {
        if (!empty($order) && !empty($order->coupon_products)) {
            return explode(',', $order->coupon_products);
        }
        return array();
    }
}

//get seller final price
if (!function_exists('getSellerFinalPrice')) {
    function getSellerFinalPrice($orderId)
    {
        $model = new \App\Models\OrderModel();
        return $model->getSellerFinalPrice($orderId);
    }
}

//get product stock status
if (!function_exists('getProductStockStatus')) {
    function getProductStockStatus($product)
    {
        if (!empty($product)) {
            if ($product->product_type == 'digital') {
                return '<span class="text-success">' . trans("in_stock") . '</span>';
            } elseif ($product->listing_type == 'ordinary_listing') {
                if ($product->is_sold == 1) {
                    return '<span class="text-danger">' . trans("sold") . '</span>';
                } else {
                    return '<span class="text-success">' . trans("active") . '</span>';
                }
            } else {
                if ($product->stock < 1) {
                    return '<span class="text-danger">' . trans("out_of_stock") . '</span>';
                } else {
                    return '<span class="text-success">' . trans("in_stock") . ' (' . $product->stock . ')' . '</span>';
                }
            }
        }
        return '';
    }
}

//get new quote requests count
if (!function_exists('getProductDigitalFile')) {
    function getProductDigitalFile($productId)
    {
        $model = new \App\Models\FileModel();
        return $model->getProductDigitalFile($productId);
    }
}

//get variation label
if (!function_exists('getVariationLabel')) {
    function getVariationLabel($labelArray, $langId)
    {
        $label = '';
        if (!empty($labelArray)) {
            $labelArray = unserializeData($labelArray);
            foreach ($labelArray as $item) {
                if ($langId == $item['lang_id']) {
                    $label = $item['label'];
                    break;
                }
            }
            if (empty($label)) {
                foreach ($labelArray as $item) {
                    if (Globals::$generalSettings->site_lang == $item['lang_id']) {
                        $label = $item['label'];
                        break;
                    }
                }
            }
        }
        return $label;
    }
}

//get variation option image url
if (!function_exists('getVariationOptionImageUrl')) {
    function getVariationOptionImageUrl($optionImage)
    {
        if ($optionImage->storage == 'aws_s3') {
            return getAWSBaseUrl() . 'uploads/images/' . $optionImage->image_small;
        } else {
            return base_url('uploads/images/' . $optionImage->image_small);
        }
    }
}

//get variation option name
if (!function_exists('getVariationOptionName')) {
    function getVariationOptionName($namesArray, $langId)
    {
        $name = '';
        if (!empty($namesArray)) {
            $namesArray = unserializeData($namesArray);
            if (!empty($namesArray)) {
                foreach ($namesArray as $item) {
                    if ($langId == $item['lang_id']) {
                        $name = $item['option_name'];
                        break;
                    }
                }
            }
            if (empty($name)) {
                foreach ($namesArray as $item) {
                    if (Globals::$generalSettings->site_lang == $item['lang_id']) {
                        $name = $item['option_name'];
                        break;
                    }
                }
            }
        }
        return $name;
    }
}

//get new quote requests count
if (!function_exists('getSessVariationImagesArray')) {
    function getSessVariationImagesArray()
    {
        $model = new \App\Models\VariationModel();
        return $model->getSessVariationImagesArray();
    }
}

//is there variation uses different price
if (!function_exists('isVariationsUseDifferentPrice')) {
    function isVariationsUseDifferentPrice($productId, $exceptId = null)
    {
        $model = new \App\Models\VariationModel();
        return $model->isVariationsUseDifferentPrice($productId, $exceptId);
    }
}

//get query string array
if (!function_exists('getQueryStringArray')) {
    function getQueryStringArray($customFilters = null)
    {
        $arrayFilterKeys = array();
        if ($customFilters != null) {
            $arrayFilterKeys = getArrayColumnValues($customFilters, 'product_filter_key');
        }
        array_push($arrayFilterKeys, 'p_min');
        array_push($arrayFilterKeys, 'p_max');
        array_push($arrayFilterKeys, 'product_type');
        array_push($arrayFilterKeys, 'sort');
        array_push($arrayFilterKeys, 'search');
        array_push($arrayFilterKeys, 'p_cat');
        $queries = array();
        $arrayQueries = array();
        $str = $_SERVER['QUERY_STRING'];
        if (!empty($str)) {
            $str = str_replace('<', '', $str);
            $str = str_replace('>', '', $str);
            $str = str_replace('*', '', $str);
            $str = str_replace('"', '', $str);
            $str = str_replace('(', '', $str);
            $str = str_replace(')', '', $str);
            @parse_str($str, $queries);
        }
        if (!empty($queries)) {
            foreach ($queries as $key => $value) {
                if (in_array($key, $arrayFilterKeys)) {
                    $key = strSlug($key);
                    $arrayValues = explode(',', $value ?? '');
                    for ($i = 0; $i < countItems($arrayValues); $i++) {
                        $arrayValues[$i] = removeForbiddenCharacters($arrayValues[$i]);
                    }
                    $arrayQueries[$key] = $arrayValues;
                }
            }
        }
        return $arrayQueries;
    }
}

//generate filter url
if (!function_exists('generateFilterUrl')) {
    function generateFilterUrl($queryStringArray, $key, $value)
    {
        $query = '';
        if (!empty($key) && $key != 'rmv_prc' && $key != 'rmv_psrc' && $key != 'rmv_srt' && $key != 'rmv_p_cat') {
            if (empty($queryStringArray) || !is_array($queryStringArray)) {
                return '?' . $key . '=' . @urlencode($value);
            }
            //add remove the key value
            if (!empty($queryStringArray[$key])) {
                if ($key == 'sort') {
                    $queryStringArray[$key] = [$value];
                }
                if ($key == 'p_cat') {
                    $queryStringArray[$key] = [$value];
                } else {
                    if (in_array($value, $queryStringArray[$key])) {
                        $newArray = array();
                        foreach ($queryStringArray[$key] as $item) {
                            if (!empty($item) && $item != $value) {
                                $newArray[] = $item;
                            }
                        }
                        $queryStringArray[$key] = $newArray;
                    } else {
                        $queryStringArray[$key][] = $value;
                    }
                }
            } else {
                $queryStringArray[$key][] = $value;
            }
        }
        //generate query string
        $i = 0;
        foreach ($queryStringArray as $arrayKey => $arrayValues) {
            $addKeys = true;
            if ($key == 'rmv_prc' && ($arrayKey == 'p_min' || $arrayKey == 'p_max')) {
                $addKeys = false;
            }
            if ($key == 'rmv_psrc' && ($arrayKey == 'search')) {
                $addKeys = false;
            }
            if ($key == 'rmv_srt' && ($arrayKey == 'sort')) {
                $addKeys = false;
            }
            if ($key == 'rmv_p_cat' && ($arrayKey == 'p_cat')) {
                $addKeys = false;
            }
            if ($addKeys && !empty($arrayValues)) {
                if ($i == 0) {
                    $query = '?' . generateFilterString($arrayKey, $arrayValues);
                } else {
                    $query .= '&' . generateFilterString($arrayKey, $arrayValues);
                }
                $i++;
            }
        }
        return $query;
    }
}

//generate filter string
if (!function_exists('generateFilterString')) {
    function generateFilterString($key, $arrayValues)
    {
        $str = '';
        $j = 0;
        if (!empty($arrayValues)) {
            foreach ($arrayValues as $value) {
                if (!empty($value) && !is_array($value)) {
                    $value = urlencode($value ?? '');
                    if ($j == 0) {
                        $str = $value;
                    } else {
                        $str .= ',' . $value;
                    }
                    $j++;
                }
            }
            $str = $key . '=' . $str;
        }
        return $str;
    }
}

//get query string array to array of objects
if (!function_exists('convertQueryStringToObjectArray')) {
    function convertQueryStringToObjectArray($queryStringArray)
    {
        $array = array();
        if (!empty($queryStringArray)) {
            foreach ($queryStringArray as $key => $arrayValues) {
                if (!empty($arrayValues)) {
                    foreach ($arrayValues as $value) {
                        $obj = new stdClass();
                        $obj->key = $key;
                        $obj->value = $value;
                        array_push($array, $obj);
                    }
                }
            }
        }
        return $array;
    }
}

//get product filters options
if (!function_exists('getProductFiltersOptions')) {
    function getProductFiltersOptions($customField, $langId, $customFilters, $queryStringArray = null)
    {
        $model = new \App\Models\FieldModel();
        return $model->getProductFiltersOptions($customField, $langId, $customFilters, $queryStringArray);
    }
}

//is custom field option selected
if (!function_exists('isCustomFieldOptionSelected')) {
    function isCustomFieldOptionSelected($queryStringObjectArray, $key, $value)
    {
        if (!empty($queryStringObjectArray)) {
            foreach ($queryStringObjectArray as $item) {
                if ($item->key == $key && $item->value == $value) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }
}


//get product filter id by key
if (!function_exists('getProductFilterIdByKey')) {
    function getProductFilterIdByKey($customFilters, $key)
    {
        if (!empty($customFilters)) {
            foreach ($customFilters as $item) {
                if ($item->product_filter_key == $key) {
                    return $item->id;
                    break;
                }
            }
        }
        return false;
    }
}

//get user products count
if (!function_exists('getUserTotalProductsCount')) {
    function getUserTotalProductsCount($userId)
    {
        $model = new \App\Models\ProductModel();
        return $model->getUserTotalProductsCount($userId);
    }
}

//get product wishlist count
if (!function_exists('getUserWishlistProductsCount')) {
    function getUserWishlistProductsCount($userId)
    {
        $model = new \App\Models\ProductModel();
        return $model->getUserWishlistProductsCount($userId);
    }
}

//discount rate format
if (!function_exists('discountRateFormat')) {
    function discountRateFormat($discountRate)
    {
        return $discountRate . '%';
    }
}

//check product stock
if (!function_exists('checkProductStock')) {
    function checkProductStock($product)
    {
        if (!empty($product)) {
            if ($product->product_type == 'digital') {
                return true;
            }
            if ($product->stock > 0) {
                return true;
            }
        }
        return false;
    }
}

//product form data
if (!function_exists('getProductFormData')) {
    function getProductFormData($product)
    {
        $data = new stdClass();
        $data->addToCartUrl = '';
        $data->button = '';
        if (!empty($product)) {
            $disabled = '';
            if (!checkProductStock($product)) {
                $disabled = ' disabled';
            }
            if ($product->listing_type == 'sell_on_site' || $product->listing_type == 'license_key') {
                if ($product->is_free_product != 1) {
                    $data->addToCartUrl = base_url('add-to-cart');
                    $data->button = '<button class="btn btn-md btn-block btn-product-cart"' . $disabled . '><span class="btn-cart-icon"><i class="icon-cart-solid"></i></span>' . trans("add_to_cart") . '</button>';
                }
            } elseif ($product->listing_type == 'bidding') {
                $data->addToCartUrl = base_url('request-quote-post');
                $data->button = '<button class="btn btn-md btn-block btn-product-cart"' . $disabled . '>' . trans("request_a_quote") . '</button>';
                if (!authCheck() && $product->listing_type == 'bidding') {
                    $data->button = '<button type="button" data-toggle="modal" data-target="#loginModal" class="btn btn-md btn-block btn-product-cart"' . $disabled . '>' . trans("request_a_quote") . '</button>';
                }
            } else {
                if (authCheck()) {
                    $data->button = '<button type="button" class="btn btn-md btn-block btn-product-cart" data-toggle="modal" data-target="#messageModal">' . trans("contact_seller") . '</button>';
                } else {
                    $data->button = '<button type="button" class="btn btn-md btn-block btn-product-cart" data-toggle="modal" data-target="#loginModal">' . trans("contact_seller") . '</button>';
                }
                if (!empty($product->external_link)) {
                    $data->button = '<a href="' . $product->external_link . '" class="btn btn-md btn-block" target="_blank" rel="nofollow">' . trans("buy_now") . '</a>';
                }
            }
        }
        return $data;
    }
}

//get product variation options
if (!function_exists('getProductVariationOptions')) {
    function getProductVariationOptions($variationId)
    {
        $model = new \App\Models\VariationModel();
        return $model->getVariationOptions($variationId);
    }
}

//get variation default option
if (!function_exists('getVariationDefaultOption')) {
    function getVariationDefaultOption($variationId)
    {
        $model = new \App\Models\VariationModel();
        return $model->getVariationDefaultOption($variationId);
    }
}

//get variation sub options
if (!function_exists('getVariationSubOptions')) {
    function getVariationSubOptions($parentId)
    {
        $model = new \App\Models\VariationModel();
        return $model->getVariationSubOptions($parentId);
    }
}

//get variation main option image url
if (!function_exists('getVariationMainOptionImageUrl')) {
    function getVariationMainOptionImageUrl($option, $productImages = null)
    {
        $imageName = '';
        $storage = '';
        if (!empty($option)) {
            if ($option->is_default == 1 && !empty($productImages)) {
                foreach ($productImages as $productImage) {
                    if ($productImage->is_main == 1) {
                        $imageName = $productImage->image_small;
                        $storage = $productImage->storage;
                    }
                }
                if (empty($imageName)) {
                    foreach ($productImages as $productImage) {
                        $imageName = $productImage->image_small;
                        $storage = $productImage->storage;
                        break;
                    }
                }
            } else {
                $model = new \App\Models\VariationModel();
                $optionImage = $model->getVariationOptionMainImage($option->id);
                if (!empty($optionImage)) {
                    $imageName = $optionImage->image_small;
                    $storage = $optionImage->storage;
                }
            }
        }
        if ($storage == 'aws_s3') {
            return getAWSBaseUrl() . 'uploads/images/' . $imageName;
        } else {
            return base_url('uploads/images/' . $imageName);
        }
    }
}

//get custom field option name
if (!function_exists('getCustomFieldOptionName')) {
    function getCustomFieldOptionName($option)
    {
        if (!empty($option)) {
            if (!empty($option->option_name)) {
                return $option->option_name;
            }
            if (!empty($option->second_name)) {
                return $option->second_name;
            }
        }
        return '';
    }
}

//get custom field product values
if (!function_exists('getCustomFieldProductValues')) {
    function getCustomFieldProductValues($customField, $productId, $langId)
    {
        $model = new \App\Models\FieldModel();
        if ($customField->field_type == 'text' || $customField->field_type == 'textarea' || $customField->field_type == 'number' || $customField->field_type == 'date') {
            return $model->getProductCustomFieldInputValue($customField->id, $productId);
        } else {
            $str = '';
            $i = 0;
            $optionValues = $model->getProductCustomFieldValues($customField->id, $productId, $langId);
            foreach ($optionValues as $optionValue) {
                if (!empty($optionValue)) {
                    if ($i == 0) {
                        $str = getCustomFieldOptionName($optionValue);
                    } else {
                        $str .= ', ' . getCustomFieldOptionName($optionValue);
                    }
                    $i++;
                }
            }
            return $str;
        }
    }
}