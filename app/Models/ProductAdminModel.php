<?php namespace App\Models;

use CodeIgniter\Model;

class ProductAdminModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('products');
    }

    //build query
    public function buildQuery($langId = null, $type = null)
    {
        if (empty($langId)) {
            $langId = $this->activeLang->id;
        }
        $this->builder->resetQuery();
        $this->builder->select('products.*');
        $this->builder->select('(SELECT title FROM product_details WHERE product_details.product_id = products.id AND product_details.lang_id = ' . clrNum($langId) . ' LIMIT 1) AS title');
        if (countItems($this->activeLanguages) > 1) {
            $this->builder->select('(SELECT title FROM product_details WHERE product_details.product_id = products.id AND product_details.lang_id != ' . clrNum($langId) . " LIMIT 1) AS second_title");
        }
        if ($this->generalSettings->membership_plans_system == 1) {
            if ($type == 'expired') {
                $this->builder->join('users', 'products.user_id = users.id AND users.is_membership_plan_expired = 1');
            } else {
                $this->builder->join('users', 'products.user_id = users.id AND users.is_membership_plan_expired = 0');
            }
        }
    }

    //get latest products
    public function getLatestProducts($limit)
    {
        $this->buildQuery();
        return $this->builder->where('products.status', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)->orderBy('products.created_at DESC')->get(clrNum($limit))->getResult();
    }

    //get products count
    public function getProductsCount()
    {
        $this->buildQuery();
        return $this->builder->where('products.status', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)->countAllResults();
    }

    //get latest pending products
    public function getLatestPendingProducts($limit)
    {
        $this->buildQuery();
        return $this->builder->where('products.status !=', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)->orderBy('products.created_at DESC')->get(clrNum($limit))->getResult();
    }

    //get pending products count
    public function getPendingProductsCount()
    {
        $this->buildQuery();
        return $this->builder->where('products.status !=', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)->countAllResults();
    }

    //get paginated products count
    public function getFilteredProductCount($list)
    {
        if ($list == 'expired_products') {
            $this->buildQuery(null, 'expired');
        } else {
            $this->buildQuery();
        }
        $this->filterProducts($list);
        return $this->builder->countAllResults();
    }

    //get paginated products
    public function getFilteredProductsPaginated($perPage, $offset, $list)
    {
        if ($list == 'expired_products') {
            $this->buildQuery(null, 'expired');
        } else {
            $this->buildQuery();
        }
        $this->filterProducts($list);
        return $this->builder->limit($perPage, $offset)->get()->getResult();
    }

    //filter by values
    public function filterProducts($list)
    {
        $productType = inputGet('product_type');
        $stock = inputGet('stock');
        $q = inputGet('q');
        $categoryId = inputGet('category');
        $subCategoryId = inputGet('subcategory');

        $arrayCategoryIds = array();
        if (!empty($subCategoryId)) {
            $categoryId = $subCategoryId;
        }
        if (!empty($categoryId)) {
            $categoryModel = new CategoryModel();
            $arrayCategoryIds = $categoryModel->getSubCategoriesTreeIds($categoryId, false, false);
        }

        if (!empty($arrayCategoryIds)) {
            $this->builder->whereIn('products.category_id', $arrayCategoryIds);
        }
        if (!empty($q)) {
            $this->builder->join('product_details', 'product_details.product_id = products.id')->where('product_details.lang_id', selectedLangId())
                ->groupStart()->like('product_details.title', $q)->orLike('products.sku', $q)->orLike('products.promote_plan', $q)->groupEnd();
        }
        if ($productType == 'physical' || $productType == 'digital') {
            $this->builder->where('products.product_type', $productType);
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
        if (!empty($list)) {
            if ($list == 'products') {
                $this->builder->where('products.visibility', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)->where('products.status', 1)->orderBy('products.created_at DESC');
            }
            if ($list == 'featured_products') {
                $this->builder->where('products.visibility', 1)->where('products.is_promoted', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)->where('products.status', 1)->orderBy('products.created_at DESC');
            }
            if ($list == 'special_offers') {
                $this->builder->where('products.visibility', 1)->where('products.is_special_offer', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)->where('products.status', 1)->orderBy('products.special_offer_date DESC');
            }
            if ($list == 'pending_products') {
                $this->builder->where('products.visibility', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)->where('products.status !=', 1)->orderBy('products.created_at DESC');
            }
            if ($list == 'hidden_products') {
                $this->builder->where('products.visibility', 0)->where('products.is_draft', 0)->where('products.is_deleted', 0)->where('products.status', 1)->orderBy('products.created_at DESC');
            }
            if ($list == 'expired_products') {
                $this->builder->where('products.is_draft', 0)->where('products.is_deleted', 0)->orderBy('products.created_at DESC');
            }
            if ($list == 'sold_products') {
                $this->builder->where('products.is_sold', 1)->where('products.is_deleted', 0)->orderBy('products.created_at DESC');
            }
            if ($list == 'drafts') {
                $this->builder->where('products.is_draft', 1)->where('products.is_deleted', 0)->orderBy('products.created_at DESC');
            }
            if ($list == 'deleted_products') {
                $this->builder->where('products.is_deleted', 1)->orderBy('products.created_at DESC');
            }
        }
    }

    //get product
    public function getProduct($id)
    {
        return $this->builder->where('products.id', clrNum($id))->get()->getRow();
    }

    //get product by slug
    public function isProductSlugUnique($productId, $slug)
    {
        if ($this->builder->where('products.id !=', clrNum($productId))->where('products.slug', removeSpecialCharacters($slug))->get()->getRow()) {
            return false;
        }
        return true;
    }

    //approve product
    public function approveProduct($id)
    {
        $product = $this->getProduct($id);
        if (!empty($product)) {
            $data = [
                'status' => 1,
                'is_rejected' => 0,
                'reject_reason' => '',
                'created_at' => date('Y-m-d H:i:s')
            ];
            return $this->builder->where('id', $product->id)->update($data);
        }
        return false;
    }

    //reject product
    public function rejectProduct($id)
    {
        $product = $this->getProduct($id);
        if (!empty($product)) {
            $data = [
                'status' => 0,
                'is_rejected' => 1,
                'reject_reason' => inputPost('reject_reason')
            ];
            return $this->builder->where('id', $product->id)->update($data);
        }
        return false;
    }

    //add remove promoted product
    public function addRemoveFeaturedProduct($productId, $dayCount)
    {
        $product = $this->getProduct($productId);
        if (!empty($product)) {
            $transaction = null;
            if ($product->is_promoted == 1) {
                $data = ['is_promoted' => 0];
            } else {
                $date = date('Y-m-d H:i:s');
                $endDate = date('Y-m-d H:i:s', strtotime($date . ' + ' . clrNum($dayCount) . ' days'));
                $data = [
                    'is_promoted' => 1,
                    'promote_start_date' => $date,
                    'promote_end_date' => $endDate
                ];
                $transactionId = inputPost('transaction_id');
                $transaction = $this->db->table('promoted_transactions')->where('id', clrNum($transactionId))->get()->getRow();
                if (!empty($transaction)) {
                    $data["promote_plan"] = $transaction->purchased_plan;
                    $data["promote_day"] = $transaction->day_count;
                }
            }
            $result = $this->builder->where('id', $product->id)->update($data);
            if ($result && !empty($transaction)) {
                $dataTransaction = ['payment_status' => "Completed"];
                $this->db->table('promoted_transactions')->where('id', $transaction->id)->update($dataTransaction);
            }
            return $result;
        }
        return false;
    }

    //add remove special offers
    public function addRemoveSpecialOffer($productId)
    {
        $product = $this->getProduct($productId);
        if (!empty($product)) {
            if ($product->is_special_offer == 1) {
                $data = [
                    'is_special_offer' => 0,
                    'special_offer_date' => ''
                ];
            } else {
                $data = [
                    'is_special_offer' => 1,
                    'special_offer_date' => date('Y-m-d H:i:s')
                ];
            }
            return $this->builder->where('id', $product->id)->update($data);
        }
        return false;
    }

    //delete product
    public function deleteProduct($productId)
    {
        $product = $this->getProduct($productId);
        if (!empty($product)) {
            $data = ['is_deleted' => 1];
            return $this->builder->where('id', $product->id)->update($data);
        }
        return false;
    }

    //delete product permanently
    public function deleteProductPermanently($id)
    {
        $product = $this->getProduct($id);
        if (!empty($product)) {
            //delete product details
            $this->db->table('product_details')->where('product_id', $product->id)->delete();
            //delete product license keys
            $this->db->table('product_license_keys')->where('product_id', $product->id)->delete();
            //delete images
            $fileModel = new FileModel();
            $fileModel->deleteProductImages($product->id);
            //delete digital product
            if ($product->product_type == 'digital') {
                $fileModel->deleteDigitalFile($product->id);
            }
            //delete comments
            $this->db->table('comments')->where('product_id', $product->id)->delete();
            //delete reviews
            $this->db->table('reviews')->where('product_id', $product->id)->delete();
            //delete from wishlist
            $this->db->table('wishlist')->where('product_id', $product->id)->delete();
            //delete from custom fields
            $this->db->table('custom_fields_product')->where('product_id', $product->id)->delete();
            //delete variations
            $variations = $this->db->table('variations')->where('product_id', $product->id)->get()->getResult();
            if (!empty($variations)) {
                foreach ($variations as $variation) {
                    $this->db->table('variation_options')->where('variation_id', $variation->id)->delete();
                    $this->db->table('variations')->where('id', $variation->id)->delete();
                }
            }
            return $this->builder->where('id', $product->id)->delete();
        }
        return false;
    }

    //delete multi product
    public function deleteMultiProducts($productIds)
    {
        if (!empty($productIds)) {
            foreach ($productIds as $id) {
                $this->deleteProduct($id);
            }
        }
    }

    //delete multi product
    public function deleteSelectedProductsPermanently($productIds)
    {
        if (!empty($productIds)) {
            foreach ($productIds as $id) {
                $this->deleteProductPermanently($id);
            }
        }
    }

    //restore product
    public function restoreProduct($productId)
    {
        $product = $this->getProduct($productId);
        if (!empty($product)) {
            return $this->builder->where('id', $product->id)->update(['is_deleted' => 0]);
        }
        return false;
    }

    /*
     * --------------------------------------------------------------------
     * CSV Bulk Upload
     * --------------------------------------------------------------------
     */

    //generate CSV object
    public function generateCsvObject($filePath)
    {
        $array = array();
        $fields = array();
        $txtName = uniqid() . '-' . user()->id . '.txt';
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
        $array = unserializeData($content);
        $membershipModel = new MembershipModel();
        $productModel = new ProductModel();
        if (!empty($array)) {
            $listingType = inputPost('listing_type');
            $currency = inputPost('currency');
            $i = 1;
            foreach ($array as $item) {
                if (!empty($listingType) && !empty($currency)) {
                    if ($i == $index) {
                        if (!$membershipModel->isAllowedAddingProduct()) {
                            echo 'Upgrade your current plan if you want to upload more ads!';
                            exit();
                        }
                        $data = array();
                        $productTitle = getCsvValue($item, 'title');
                        $data['slug'] = !empty(getCsvValue($item, 'slug')) ? getCsvValue($item, 'slug') : strSlug($productTitle);
                        $data['product_type'] = 'physical';
                        $data['listing_type'] = !empty($listingType) ? $listingType : 'sell_on_site';
                        $data['sku'] = getCsvValue($item, 'sku');
                        $data['category_id'] = !empty(getCsvValue($item, 'category_id', 'int')) ? getCsvValue($item, 'category_id', 'int') : 1;
                        $data['price'] = $this->getCsvPrice(getCsvValue($item, 'price'));
                        $data['currency'] = !empty($currency) ? $currency : 'USD';
                        $data['discount_rate'] = getCsvValue($item, 'discount_rate', 'int');
                        $data['vat_rate'] = getCsvValue($item, 'vat_rate', 'int');
                        $data['user_id'] = user()->id;
                        $data['status'] = 0;
                        $data['is_promoted'] = 0;
                        $data['promote_start_date'] = '';
                        $data['promote_end_date'] = '';
                        $data['promote_plan'] = 'none';
                        $data['promote_day'] = 0;
                        $data['visibility'] = 1;
                        $data['rating'] = 0;
                        $data['pageviews'] = 0;
                        $data['demo_url'] = '';
                        $data['external_link'] = getCsvValue($item, 'external_link');
                        $data['files_included'] = '';
                        $data['stock'] = getCsvValue($item, 'stock');
                        $data['shipping_class_id'] = 0;
                        $data['shipping_delivery_time_id'] = 0;
                        $data['multiple_sale'] = 1;
                        $data['is_sold'] = 0;
                        $data['is_deleted'] = 0;
                        $data['is_draft'] = 0;
                        $data['is_free_product'] = 0;
                        $data['created_at'] = date('Y-m-d H:i:s');
                        if ($this->generalSettings->approve_before_publishing == 0 || hasPermission('products')) {
                            $data['status'] = 1;
                        }
                        if ($this->builder->insert($data)) {
                            //last id
                            $lastId = $this->db->insertID();
                            //update slug
                            $productModel->updateSlug($lastId);
                            //add product title description
                            $dataTitleDesc = [
                                'product_id' => $lastId,
                                'lang_id' => selectedLangId(),
                                'title' => $productTitle,
                                'description' => getCsvValue($item, 'description'),
                                'seo_title' => '',
                                'seo_description' => '',
                                'seo_keywords' => ''
                            ];
                            $this->db->table('product_details')->insert($dataTitleDesc);
                            //upload images
                            $this->uploadProductImagesCsv(getCsvValue($item, 'image_url'), $lastId);
                            return $productTitle;
                        }
                    }
                    $i++;
                }
            }
        }
    }

    //upload product csv images
    public function uploadProductImagesCsv($imageUrl, $productId)
    {
        if (!empty($imageUrl)) {
            $uploadModel = new UploadModel();
            $arrayImageUrls = explode(',', $imageUrl);
            if (!empty($arrayImageUrls)) {
                foreach ($arrayImageUrls as $url) {
                    $url = trim($url);
                    if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE) {
                        //upload images
                        $saveTo = FCPATH . 'uploads/temp/temp-' . user()->id . '.jpg';
                        @copy($url, $saveTo);
                        if (!empty($saveTo) && file_exists($saveTo)) {
                            $dataImage = [
                                'product_id' => $productId,
                                'image_default' => $uploadModel->uploadProductDefaultImage($saveTo, 'images'),
                                'image_big' => $uploadModel->uploadProductBigImage($saveTo, 'images'),
                                'image_small' => $uploadModel->uploadProductSmallImage($saveTo, 'images'),
                                'is_main' => 0,
                                'storage' => 'local'
                            ];
                            $uploadModel->deleteTempFile($saveTo);
                        }
                        //move to s3
                        if ($this->storageSettings->storage == 'aws_s3') {
                            $awsModel = new AwsModel();
                            $dataImage['storage'] = 'aws_s3';
                            if (!empty($dataImage['image_default'])) {
                                $awsModel->putProductObject($dataImage['image_default'], FCPATH . 'uploads/images/' . $dataImage['image_default']);
                                deleteFile('uploads/images/' . $dataImage['image_default']);
                            }
                            if (!empty($dataImage['image_big'])) {
                                $awsModel->putProductObject($dataImage['image_big'], FCPATH . 'uploads/images/' . $dataImage['image_big']);
                                deleteFile('uploads/images/' . $dataImage['image_big']);
                            }
                            if (!empty($dataImage['image_small'])) {
                                $awsModel->putProductObject($dataImage['image_small'], FCPATH . 'uploads/images/' . $dataImage['image_small']);
                                deleteFile('uploads/images/' . $dataImage['image_small']);
                            }
                        }
                        $this->db->reconnect();
                        $this->db->table('images')->insert($dataImage);
                    }
                }
            }
        }
    }

    //get csv price
    public function getCsvPrice($price)
    {
        if (!empty($price)) {
            $price = str_replace(',', '.', $price);
            $price = @preg_replace('/[^0-9\.,]/', '', $price);
            $price = @number_format($price, 2, '.', '');
            $price = @str_replace('.00', '', $price);
            $price = @floatval($price);
            if (!empty($price)) {
                return $price * 100;
            }
        }
        return 0;
    }

}
