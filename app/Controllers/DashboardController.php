<?php

namespace App\Controllers;

use App\Models\BiddingModel;
use App\Models\CouponModel;
use App\Models\EarningsModel;
use App\Models\FieldModel;
use App\Models\FileModel;
use App\Models\LocationModel;
use App\Models\MembershipModel;
use App\Models\OrderAdminModel;
use App\Models\OrderModel;
use App\Models\ProductAdminModel;
use App\Models\ProductModel;
use App\Models\ProfileModel;
use App\Models\PromoteModel;
use App\Models\ShippingModel;
use App\Models\UploadModel;
use App\Models\VariationModel;

class DashboardController extends BaseController
{
    protected $orderAdminModel;
    protected $orderModel;
    protected $productAdminModel;
    protected $membershipModel;
    protected $shippingModel;
    protected $couponModel;
    protected $earningsModel;
    protected $fileModel;
    protected $userId;
    protected $perPage;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        if (!authCheck()) {
            redirectToUrl(langBaseUrl());
        }
        if (!isVendor() && !hasPermission('products')) {
            if ($this->generalSettings->membership_plans_system == 1) {
                redirectToUrl(generateUrl('start_selling', 'select_membership_plan'));
            }
            redirectToUrl(generateUrl('start_selling'));
        }
        $this->orderAdminModel = new OrderAdminModel();
        $this->orderModel = new OrderModel();
        $this->productAdminModel = new ProductAdminModel();
        $this->membershipModel = new MembershipModel();
        $this->shippingModel = new ShippingModel();
        $this->couponModel = new CouponModel();
        $this->earningsModel = new EarningsModel();
        $this->fileModel = new FileModel();
        $this->userId = user()->id;
        $this->perPage = 15;
    }

    /**
     * Index
     */
    public function index()
    {
        $data['title'] = getUsername(user());
        $data['description'] = getUsername(user()) . ' - ' . $this->baseVars->appName;
        $data['keywords'] = getUsername(user()) . ',' . $this->baseVars->appName;
        $data['user'] = user();
        $data["userRating"] = calculateUserRating($this->userId);
        $data["activeTab"] = 'products';
        $data['activeSalesCount'] = $this->orderAdminModel->getActiveSalesCountBySeller($this->userId);
        $data['completedSalesCount'] = $this->orderAdminModel->getCompletedSalesCountBySeller($this->userId);
        $data['totalSalesCount'] = $data['activeSalesCount'] + $data['completedSalesCount'];
        $data['totalPageviewsCount'] = $this->productModel->getVendorTotalPageviewsCount($this->userId);
        $data['productsCount'] = $this->productModel->getUserTotalProductsCount($this->userId);
        $data['latestSales'] = $this->orderModel->getSalesBySellerLimited($this->userId, 6);
        $data['mostViewedProducts'] = $this->productModel->getVendorMostViewedProducts($this->userId, 6);
        $data['latestComments'] = $this->commonModel->getVendorCommentsPaginated($this->userId, 6, 0);
        
        $data['latestReviews'] = $this->commonModel->getVendorReviewsPaginated($this->userId, 6, 0);
        $data['salesSum'] = $this->orderAdminModel->getSalesSumByMonth($this->userId);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/index', $data);
        echo view('dashboard/includes/_footer');
    }

    /*
     * --------------------------------------------------------------------
     * Products
     * --------------------------------------------------------------------
     */

    /**
     * Add Product
     */
    public function addProduct()
    {
        $data = $this->setMetaData(trans("add_product"));
        $data['images'] = $this->fileModel->getSessProductImagesArray();
        $data["fileManagerImages"] = $this->fileModel->getUserFileManagerImages($this->userId);
        $data["activeProductSystemArray"] = $this->getActivatedProductSystem();
        $view = !$this->membershipModel->isAllowedAddingProduct() ? 'plan_expired' : 'add_product';
        
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/product/' . $view, $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Add Product Post
     */
    public function addProductPost()
    {
        if (!$this->membershipModel->isAllowedAddingProduct()) {
            setErrorMessage(trans("msg_plan_expired"));
            redirectToBackUrl();
        }
        //validate title
        if (empty(trim(inputPost('title_' . selectedLangId()) ?? ''))) {
            setErrorMessage(trans("msg_error"));
            redirectToBackUrl();
        }
        //add product
        if ($insertId = $this->productModel->addProduct()) {
            //add product title and desc
            $this->productModel->addProductTitleDesc($insertId);
            //update slug
            $this->productModel->updateSlug($insertId);
            //add product images
            $this->fileModel->addProductImages($insertId);
            return redirect()->to(generateDashUrl('product', 'product_details') . '/' . $insertId);
        } else {
            setErrorMessage(trans("msg_error"));
            redirectToBackUrl();
        }
    }

    /**
     * Edit Product
     */
    public function editProduct($id)
    {
        $product = $this->productAdminModel->getProduct($id);
        if (empty($product)) {
            return redirect()->to(dashboardUrl());
        }
        if ($product->is_deleted == 1) {
            if (!hasPermission('products')) {
                return redirect()->to(dashboardUrl());
            }
        }
        if ($product->user_id != $this->userId && !hasPermission('products')) {
            return redirect()->to(dashboardUrl());
        }
        $title = $product->is_draft == 1 ? trans('add_product') : trans('edit_product');
        $data = $this->setMetaData($title);
        $data['product'] = $product;
        $data['category'] = $this->categoryModel->getCategory($product->category_id);
        $data['productImages'] = $this->fileModel->getProductImages($product->id);
        $data['fileManagerImages'] = $this->fileModel->getUserFileManagerImages($this->userId);
        $data['parentCategories'] = $this->categoryModel->getParentCategories();
        $data['activeProductSystemArray'] = $this->getActivatedProductSystem();
        
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/product/edit_product', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Edit Product Post
     */
    public function editProductPost()
    {
        $productId = inputPost('id');
        $userId = 0;
        $product = $this->productAdminModel->getProduct($productId);
        if (!empty($product)) {
            if ($product->user_id != $this->userId && !hasPermission('products')) {
                return redirect()->to(dashboardUrl());
            }
            //validate title
            if (empty(trim(inputPost('title_' . selectedLangId()) ?? ''))) {
                setErrorMessage(trans("msg_error"));
                redirectToBackUrl();
            }

            //check slug is unique
            $slug = $product->slug;
            if (isAdmin()) {
                $slug = inputPost('slug');
                if (empty($slug)) {
                    $slug = 'product-' . $product->id;
                }
                if (!$this->productAdminModel->isProductSlugUnique($product->id, $slug)) {
                    setErrorMessage(trans("msg_product_slug_used"));
                    redirectToBackUrl();
                }
            }
            if ($this->productModel->editProduct($product, $slug)) {
                //edit product title and desc
                $this->productModel->editProductTitleDesc($product->id);
                if ($product->is_draft == 1) {
                    return redirect()->to(generateDashUrl('product', 'product_details') . '/' . $product->id);
                } else {
                    setSuccessMessage(trans("msg_updated"));
                    resetCacheDataOnChange();
                    redirectToBackUrl();
                }
            }
        }
        setErrorMessage(trans("msg_error"));
        redirectToBackUrl();
    }

    /**
     * Edit Product Details
     */
    public function editProductDetails($id)
    {
        $product = $this->productAdminModel->getProduct($id);
        if (empty($product)) {
            return redirect()->to(dashboardUrl());
        }
        if ($product->is_deleted == 1) {
            if (!hasPermission('products')) {
                return redirect()->to(dashboardUrl());
            }
        }
        if ($product->user_id != $this->userId && !hasPermission('products')) {
            return redirect()->to(dashboardUrl());
        }
        $title = $product->is_draft == 1 ? trans('add_product') : trans('edit_product');
        $data = $this->setMetaData($title);
        $data['product'] = $product;
        $fieldModel = new FieldModel();
        $data["customFields"] = $fieldModel->getCustomFieldsByCategory($product->category_id);
        $variationModel = new VariationModel();
        $data['productVariations'] = $variationModel->getProductVariations($product->id);
        $data['userVariations'] = $variationModel->getVariationsByUserId($product->user_id);
        $data['licenseKeys'] = $this->productModel->getProductLicenseKeys($product->id);
        $data['productVideo'] = $this->fileModel->getProductVideo($product->id);
        $data['productAudio'] = $this->fileModel->getProductAudio($product->id);
        $shippingModel = new ShippingModel();
        $data['shippingStatus'] = $this->productSettings->marketplace_shipping;
        
        if ($data['product']->listing_type == 'ordinary_listing' || $data['product']->product_type != 'physical') {
            $data['shippingStatus'] = 0;
        }
        $data['shippingClasses'] = $shippingModel->getActiveShippingClasses($product->user_id);
        $data['shippingDeliveryTimes'] = $shippingModel->getShippingDeliveryTimes($product->user_id);
        $shippingZones = $shippingModel->getShippingZones($product->user_id);
        $data['showShippingOptionsWarning'] = false;
        if ($data['shippingStatus'] == 1 && empty($shippingZones)) {
            $data['showShippingOptionsWarning'] = true;
        }

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/product/edit_product_details', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Edit Product Details Post
     */
    public function editProductDetailsPost()
    {
        $productId = inputPost('id');
        $product = $this->productAdminModel->getProduct($productId);
        if (empty($product)) {
            return redirect()->to(dashboardUrl());
        }
        if ($product->is_deleted == 1) {
            if (!hasPermission('products')) {
                return redirect()->to(dashboardUrl());
            }
        }
        if ($product->user_id != $this->userId && !hasPermission('products')) {
            return redirect()->to(dashboardUrl());
        }
        //check digital file
        if ($product->product_type == 'digital' && $product->listing_type != 'license_key') {
            if (empty($this->fileModel->getProductDigitalFile($product->id))) {
                setErrorMessage(trans("digital_file_required"));
                redirectToBackUrl();
            }
        }
        if ($this->productModel->editProductDetails($product->id)) {
            //edit custom fields
            $this->productModel->updateProductCustomFields($product->id);
            //reset cache
            resetCacheDataOnChange();
            if ($product->is_draft != 1) {
                setSuccessMessage(trans("msg_updated"));
                redirectToBackUrl();
            } else {
                //if draft
                if (inputPost('submit') == 'save_as_draft') {
                    setSuccessMessage(trans("draft_added"));
                } else {
                    if ($this->generalSettings->approve_before_publishing == 1 && !isAdmin()) {
                        setSuccessMessage(trans("product_added") . " " . trans("product_approve_published") . " <a href='" . generateProductUrl($product) . "' class='link-view-product'>" . trans("view_product") . "</a>");
                    } else {
                        setSuccessMessage(trans("product_added") . " <a href='" . generateProductUrl($product) . "' class='link-view-product' target='_blank'>" . trans("view_product") . "</a>");
                    }
                    //send email
                    if ($this->generalSettings->send_email_new_product == 1) {
                        $emailData = [
                            'email_type' => 'new_product',
                            'email_address' => $this->generalSettings->mail_options_account,
                            'email_subject' => trans("email_text_new_product"),
                            'template_path' => 'email/main',
                            'email_data' => serialize([
                                'content' => trans("email_text_see_product"),
                                'url' => generateProductUrl($product),
                                'buttonText' => trans("view_product")
                            ])
                        ];
                        addToEmailQueue($emailData);
                    }
                }
                return redirect()->to(generateDashUrl('add_product'));
            }
        } else {
            setErrorMessage(trans('msg_error'));
            redirectToBackUrl();
        }
    }

    //get activated product system
    public function getActivatedProductSystem()
    {
        $array = [
            'activeSystemCount' => 0,
            'activeSystemValue' => '',
        ];
        if ($this->generalSettings->marketplace_system == 1) {
            $array['activeSystemCount'] = $array['activeSystemCount'] + 1;
            $array['activeSystemValue'] = 'sell_on_site';
        }
        if ($this->generalSettings->classified_ads_system == 1) {
            $array['activeSystemCount'] = $array['activeSystemCount'] + 1;
            $array['activeSystemValue'] = 'ordinary_listing';
        }
        if ($this->generalSettings->bidding_system == 1) {
            $array['activeSystemCount'] = $array['activeSystemCount'] + 1;
            $array['activeSystemValue'] = 'bidding';
        }
        if ($this->generalSettings->selling_license_keys_system == 1) {
            $array['activeSystemCount'] = $array['activeSystemCount'] + 1;
            $array['activeSystemValue'] = 'license';
        }
        return $array;
    }

    /**
     * Products
     */
    public function products()
    {
        if (!$this->baseVars->isSaleActive) {
            return redirect()->to(dashboardUrl());
        }
        $st = inputGet('st');
        $status = 'active';
        $page = trans("products");
        if (!empty($st)) {
            if ($st == 'pending') {
                $status = 'pending';
                $page = trans("pending_products");
            }
            if ($st == 'hidden') {
                $status = 'hidden';
                $page = trans("hidden_products");
            }
            if ($st == 'expired') {
                $status = 'expired';
                $page = trans("expired_products");
            }
            if ($st == 'sold') {
                $status = 'sold';
                $page = trans("sold_products");
            }
            if ($st == 'draft') {
                $status = 'draft';
                $page = trans("drafts");
            }
        }
        $data = $this->setMetaData($page);
        $data['numRows'] = $this->productModel->getVendorProductsCount($this->userId, $status);
        $pager = paginate($this->perPage, $data['numRows']);
        $data['products'] = $this->productModel->getVendorProductsPaginated($this->userId, $status, $this->perPage, $pager->offset);
        $data['parentCategories'] = $this->categoryModel->getParentCategories();
        
        $data['productListStatus'] = $status;
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/product/products', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Delete Product
     */
    public function deleteProduct()
    {
        $id = inputPost('id');
        $userId = 0;
        $result = false;
        $product = $this->productAdminModel->getProduct($id);
        if (!empty($product)) {
            $userId = $product->user_id;
            if (hasPermission('products') || $this->userId == $userId) {
                if ($product->is_draft == 1) {
                    $result = $this->productAdminModel->deleteProductPermanently($id);
                } else {
                    $result = $this->productAdminModel->deleteProduct($id);
                }
            }
            if ($result) {
                setSuccessMessage(trans("msg_deleted"));
                resetCacheDataOnChange();
            } else {
                setErrorMessage(trans("msg_error"));
            }
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    //get subcategories
    public function getSubCategories()
    {
        $parentId = inputPost('parent_id');
        if (!empty($parentId)) {
            $subCategories = $this->categoryModel->getSubCategoriesByParentId($parentId);
            if (!empty($subCategories)) {
                foreach ($subCategories as $item) {
                    echo '<option value="' . $item->id . '">' . esc($item->name) . '</option>';
                }
            }
        }
    }

    /*
     * --------------------------------------------------------------------
     * License Keys
     * --------------------------------------------------------------------
     */

    /**
     * Add License Keys
     */
    //
    public function addLicenseKeys()
    {
        $productId = inputPost('product_id');
        $product = getProduct($productId);
        if (!empty($product)) {
            if ($this->userId == $product->user_id || hasPermission('products')) {
                $this->productModel->addLicenseKeys($productId);
                $data = [
                    'result' => 1,
                    'message' => trans("msg_add_license_keys")
                ];
                echo json_encode($data);
            }
        }
    }

    //delete license key
    public function deleteLicenseKey()
    {
        $id = inputPost('id');
        $productId = inputPost('product_id');
        $product = getProduct($productId);
        if (!empty($product)) {
            if ($this->userId == $product->user_id || hasPermission('products')) {
                $this->productModel->deleteLicenseKey($id);
            }
        }
    }

    //load license keys list
    public function loadLicenseKeysList()
    {
        $productId = inputPost('product_id');
        $vars['product'] = getProduct($productId);
        if (!empty($vars['product'])) {
            if ($this->userId == $vars['product']->user_id || hasPermission('products')) {
                $vars['licenseKeys'] = $this->productModel->getProductLicenseKeys($productId);
                $data = [
                    'result' => 1,
                    'htmlContent' => view('dashboard/product/license/_license_keys_list', $vars)
                ];
                echo json_encode($data);
            }
        } else {
            echo json_encode(['result' => 0]);
        }
    }

    /*
     * --------------------------------------------------------------------
     * Bulk Product Upload
     * --------------------------------------------------------------------
     */

    /**
     * Bulk Product Upload
     */
    public function bulkProductUpload()
    {
        $data = $this->setMetaData(trans("bulk_product_upload"));
        $view = !$this->membershipModel->isAllowedAddingProduct() ? 'plan_expired' : 'bulk_product_upload';
        if (!hasPermission('products') && $this->generalSettings->vendor_bulk_product_upload != 1) {
            return redirect()->to(dashboardUrl());
        }

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/product/' . $view, $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Download CSV Files Post
     */
    public function downloadCsvFilePost()
    {
        $submit = inputPost('submit');
        if ($submit == 'csv_template') {
            return $this->response->download(FCPATH . 'assets/file/csv_product_template.csv', null);
        } elseif ($submit == 'csv_example') {
            return $this->response->download(FCPATH . 'assets/file/csv_product_example.csv', null);
        }
        redirectToBackUrl();
    }

    /**
     * Generate CSV Object Post
     */
    public function generateCsvObjectPost()
    {
        //delete old txt files
        $files = glob(FCPATH . 'uploads/temp/*.txt');
        $now = time();
        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    if ($now - filemtime($file) >= 60 * 60 * 24) {
                        @unlink($file);
                    }
                }
            }
        }
        $file = null;
        if (isset($_FILES['file'])) {
            if (!empty($_FILES['file']['name'])) {
                $file = $_FILES['file'];
            }
        }
        $filePath = '';
        $uploadModel = new UploadModel();
        $tempFile = $uploadModel->uploadTempFile('file');
        if (!empty($tempFile) && !empty($tempFile['path'])) {
            $filePath = $tempFile['path'];
        }
        if (!empty($filePath)) {
            $csvObject = $this->productAdminModel->generateCsvObject($filePath);
            if (!empty($csvObject)) {
                $data = [
                    'result' => 1,
                    'number_of_items' => $csvObject->number_of_items,
                    'txt_file_name' => $csvObject->txt_file_name,
                ];
                echo json_encode($data);
                exit();
            }
        }
        $data = [
            'result' => 0
        ];
        echo json_encode($data);
    }

    /**
     * Import CSV Item Post
     */
    public function importCsvItemPost()
    {
        $txtFileName = inputPost('txt_file_name');
        $index = inputPost('index');
        $name = $this->productAdminModel->importCsvItem($txtFileName, $index);
        if (!empty($name)) {
            $data = [
                'result' => 1,
                'name' => $name,
                'index' => $index
            ];
            echo json_encode($data);
        } else {
            $data = [
                'result' => 0,
                'index' => $index
            ];
            echo json_encode($data);
        }
        resetCacheDataOnChange();
    }

    /*
     * --------------------------------------------------------------------
     * Promote
     * --------------------------------------------------------------------
     */

    /**
     * Promote Product Post
     */
    public function promoteProductPost()
    {
        $productId = inputPost('product_id');
        $product = getProduct($productId);

        if (!empty($product)) {
            if ($product->user_id != $this->userId) {
                setErrorMessage(trans("invalid_attempt"));
                redirectToBackUrl();
            }
            $planType = inputPost('plan_type');
            $pricePerDay = getPrice($this->paymentSettings->price_per_day, 'decimal');
            $pricePerMonth = getPrice($this->paymentSettings->price_per_month, 'decimal');
            $dayCount = inputPost('day_count');
            $monthCount = inputPost('month_count');
            $totalAmount = 0;
            if ($planType == 'daily') {
                $t = $dayCount * $pricePerDay;
                if (!empty($t)) {
                    $totalAmount = number_format($t, 2, '.', '') * 100;
                }
                $purchasedPlan = trans("daily_plan") . ' (' . $dayCount . ' ' . trans("days") . ')';
            }
            if ($planType == 'monthly') {
                $dayCount = $monthCount * 30;
                $t = $monthCount * $pricePerMonth;
                if (!empty($t)) {
                    $totalAmount = number_format($t, 2, '.', '') * 100;
                }
                $purchasedPlan = trans("monthly_plan") . ' (' . $dayCount . ' ' . trans("days") . ')';
            }
            $data = new \stdClass();
            $data->plan_type = inputPost('plan_type');
            $data->product_id = $productId;
            $data->day_count = $dayCount;
            $data->month_count = $monthCount;
            $data->total_amount = getPrice($totalAmount, 'decimal');
            $data->purchased_plan = $purchasedPlan;
            if ($this->paymentSettings->free_product_promotion == 1) {
                $promoteModel = new PromoteModel();
                $promoteModel->addToPromotedProducts($data);
                redirectToBackUrl();
            } else {
                helperSetSession('modesy_selected_promoted_plan', $data);
                return redirect()->to(generateUrl('cart', 'payment_method') . '?payment_type=promote');
            }
        }
        setErrorMessage(trans("invalid_attempt"));
        redirectToBackUrl();
    }

    /*
     * --------------------------------------------------------------------
     * Sales
     * --------------------------------------------------------------------
     */

    /**
     * Sales
     */
    public function sales()
    {
        if (!$this->baseVars->isSaleActive) {
            return redirect()->to(dashboardUrl());
        }
        $st = inputGet('st');
        $page = 'sales';
        $status = 'active';
        if ($st == 'completed') {
            $page = 'completed_sales';
            $status = 'completed';
        } elseif ($st == 'cancelled') {
            $page = 'cancelled_sales';
            $status = 'cancelled';
        }
        $data = $this->setMetaData(trans($page));
        $data['page'] = $page;
        $data['numRows'] = $this->orderModel->getSalesCount($status, $this->userId);
        $pager = paginate($this->perPage, $data['numRows']);
        $data['sales'] = $this->orderModel->getSalesPaginated($status, $this->userId, $this->perPage, $pager->offset);
        
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/sales/sales', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Sale
     */
    public function sale($orderNumber)
    {
        if (!$this->baseVars->isSaleActive) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("sale"));
        $data['order'] = $this->orderModel->getOrderByOrderNumber($orderNumber);
        if (empty($data['order'])) {
            return redirect()->to(dashboardUrl());
        }
        if (!$this->orderModel->checkOrderSeller($data['order']->id)) {
            return redirect()->to(dashboardUrl());
        }
        $data['orderProducts'] = $this->orderModel->getOrderProducts($data['order']->id);
        
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/sales/sale', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Update Order Product Status Post
     */
    public function updateOrderProductStatusPost()
    {
        $id = inputPost('id');
        $orderProduct = $this->orderModel->getOrderProduct($id);
        if ($this->userId != $orderProduct->seller_id) {
            return redirect()->to(dashboardUrl());
        }
        if (!empty($orderProduct)) {
            if ($this->orderModel->updateOrderProductStatus($id)) {
                $this->orderAdminModel->updateOrderStatusIfCompleted($orderProduct->order_id);
            }
        }
        redirectToBackUrl();
    }

    /*
     * --------------------------------------------------------------------
     * Earnings & Payouts
     * --------------------------------------------------------------------
     */

    /**
     * Earnings
     */
    public function earnings()
    {
        if (!$this->baseVars->isSaleActive) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("earnings"));
        $data['numRows'] = $this->earningsModel->getEarningsCount($this->userId);
        $pager = paginate($this->perPage, $data['numRows']);
        $data['earnings'] = $this->earningsModel->getEarningsPaginated($this->userId, $this->perPage, $pager->offset);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/earnings/earnings', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Payouts
     */
    public function payouts()
    {
        if (!$this->baseVars->isSaleActive) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("payouts"));
        $data['numRows'] = $this->earningsModel->getPayoutsCount($this->userId);
        $pager = paginate($this->perPage, $data['numRows']);
        $data['payouts'] = $this->earningsModel->getPaginatedPayouts($this->userId, $this->perPage, $pager->offset);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/earnings/payouts', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Withdraw Money
     */
    public function withdrawMoney()
    {
        if (!$this->baseVars->isSaleActive) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("withdraw_money"));
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/earnings/withdraw_money', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Withdraw Money Post
     */
    public function withdrawMoneyPost()
    {
        if (!$this->baseVars->isSaleActive) {
            return redirect()->to(dashboardUrl());
        }
        $data = [
            'user_id' => $this->userId,
            'payout_method' => inputPost('payout_method'),
            'amount' => inputPost('amount'),
            'currency' => inputPost('currency'),
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $data['amount'] = getPrice($data['amount'], 'database');
        //check active payouts
        $activePayouts = $this->earningsModel->getActivePayouts($this->userId);
        if (!empty($activePayouts)) {
            setErrorMessage(trans("active_payment_request_error"));
            redirectToBackUrl();
        }
        $min = 0;
        if ($data['payout_method'] == 'paypal') {
            //check PayPal email
            $payoutPaypalEmail = $this->earningsModel->getUserPayoutAccount($this->userId);
            if (empty($payoutPaypalEmail) || empty($payoutPaypalEmail->payout_paypal_email)) {
                setErrorMessage(trans("msg_payout_paypal_error"));
                redirectToBackUrl();
            }
            $min = $this->paymentSettings->min_payout_paypal;
        }
        if ($data['payout_method'] == 'bitcoin') {
            //check bitcoin address
            $payoutBitcoin = $this->earningsModel->getUserPayoutAccount($this->userId);
            if (empty($payoutBitcoin) || empty($payoutBitcoin->payout_bitcoin_address)) {
                setErrorMessage(trans("msg_payout_bitcoin_address_error"));
                redirectToBackUrl();
            }
            $min = $this->paymentSettings->min_payout_bitcoin;
        }
        if ($data['payout_method'] == 'iban') {
            $min = $this->paymentSettings->min_payout_iban;
        }
        if ($data['payout_method'] == 'swift') {
            $min = $this->paymentSettings->min_payout_swift;
        }
        if ($data['amount'] <= 0) {
            setErrorMessage(trans("msg_error"));
            redirectToBackUrl();
        }
        if ($data['amount'] < $min) {
            setErrorMessage(trans("invalid_withdrawal_amount"));
            redirectToBackUrl();
        }
        if ($data['amount'] > user()->balance) {
            setErrorMessage(trans("invalid_withdrawal_amount"));
            redirectToBackUrl();
        }
        if ($this->earningsModel->withdrawMoney($data)) {
            setSuccessMessage(trans("msg_request_sent"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Set Payout Account
     */
    public function setPayoutAccount()
    {
        if (!$this->baseVars->isSaleActive) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("set_payout_account"));
        $data['userPayout'] = $this->earningsModel->getUserPayoutAccount($this->userId);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/earnings/set_payout_account', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Set Paypal Payout Account Post
     */
    public function setPayoutAccountPost()
    {
        if (!$this->baseVars->isSaleActive) {
            return redirect()->to(dashboardUrl());
        }
        $submit = inputPost('submit');
        if ($this->earningsModel->setPayoutAccount($this->userId, $submit)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        return redirect()->to(generateDashUrl('set_payout_account') . '?tab=' . strSlug($submit));
    }

    /*
     * --------------------------------------------------------------------
     * Quote Requests
     * --------------------------------------------------------------------
     */

    /**
     * Quote Requests
     */
    public function quoteRequests()
    {
        if ($this->generalSettings->bidding_system != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("quote_requests"));
        $biddingModel = new BiddingModel();
        $data['numRows'] = $biddingModel->getVendorQuoteRequestsCount($this->userId);
        $pager = paginate($this->perPage, $data['numRows']);
        $data['quoteRequests'] = $biddingModel->getVendorQuoteRequestsPaginated($this->userId, $this->perPage, $pager->offset);
        
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/quote_requests', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Submit Quote
     */
    public function submitQuotePost()
    {
        $biddingModel = new BiddingModel();
        $id = inputPost('id');
        $quoteRequest = $biddingModel->getQuoteRequest($id);
        if ($biddingModel->submitQuote($quoteRequest)) {
            //send email
            $buyer = getUser($quoteRequest->buyer_id);
            if (!empty($buyer) && $this->generalSettings->send_email_bidding_system == 1) {
                $emailData = [
                    'email_type' => 'quote',
                    'email_address' => $buyer->email,
                    'email_subject' => trans("quote_request"),
                    'template_path' => 'email/main',
                    'email_data' => serialize([
                        'content' => trans("your_quote_request_replied") . "<br>" . trans("quote") . ": " . "<strong>#" . $quoteRequest->id . "</strong>",
                        'url' => generateUrl('quote_requests'),
                        'buttonText' => trans("view_details")
                    ])
                ];
                addToEmailQueue($emailData);
            }
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /*
     * --------------------------------------------------------------------
     * Coupons
     * --------------------------------------------------------------------
     */

    /**
     * Coupons
     */
    public function coupons()
    {
        $data = $this->setMetaData(trans("coupons"));
        $data['numRows'] = $this->couponModel->getCouponsCount($this->userId);
        $pager = paginate($this->perPage, $data['numRows']);
        $data['coupons'] = $this->couponModel->getCouponsPaginated($this->userId, $this->perPage, $pager->offset);
        
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/coupon/coupons', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Add Coupon
     */
    public function addCoupon()
    {
        $data = $this->setMetaData(trans("add_coupon"));
        $data['parentCategories'] = $this->categoryModel->getParentCategories();
        $data['categories'] = $this->categoryModel->getVendorCategories(null, $this->userId, true, false);
        $data['categoryIds'] = array();
        if (!empty($data['categories']) && !empty($data['categories'][0])) {
            foreach ($data['categories'] as $item) {
                array_push($data['categoryIds'], $item->id);
            }
        }
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/coupon/add_coupon', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Add Coupon Post
     */
    public function addCouponPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('coupon_code', trans("coupon_code"), 'required|max_length[49]');
        $val->setRule('discount_rate', trans("discount_rate"), 'required');
        $val->setRule('coupon_count', trans("number_of_coupons"), 'required');
        $val->setRule('expiry_date', trans("expiry_date"), 'required');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $code = inputPost('coupon_code');
            if (!empty($this->couponModel->getCouponByCode($code))) {
                setErrorMessage(trans("msg_coupon_code_added_before"));
                $this->session->setFlashdata('selectedProductsIds', $this->couponModel->getSelectedProductsArray());
                return redirect()->back()->withInput();
            }
            if ($this->couponModel->addCoupon()) {
                setSuccessMessage(trans("msg_added"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        redirectToBackUrl();
    }

    /**
     * Edit Coupon
     */
    public function editCoupon($id)
    {
        $data = $this->setMetaData(trans("edit_coupon"));
        $data['coupon'] = $this->couponModel->getCoupon($id);
        if (empty($data['coupon'])) {
            return redirect()->to(generateDashUrl('coupons'));
        }
        if ($data['coupon']->seller_id != $this->userId) {
            return redirect()->to(generateDashUrl('coupons'));
        }
        $data['parentCategories'] = $this->categoryModel->getParentCategories();
        $data['categories'] = $this->categoryModel->getVendorCategories(null, $this->userId, true, false);
        $data['categoryIds'] = array();
        if (!empty($data['categories']) && !empty($data['categories'][0])) {
            foreach ($data['categories'] as $item) {
                array_push($data['categoryIds'], $item->id);
            }
        }
        $data['selectedCategories'] = explode(',', $data['coupon']->category_ids);
        if (empty($data['selectedCategories'])) {
            $data['selectedCategories'] = array();
        }
        $data['selectedProducts'] = array();
        $selectedProducts = $this->couponModel->getCouponProducts($data['coupon']->id);
        if (!empty($selectedProducts)) {
            foreach ($selectedProducts as $item) {
                array_push($data['selectedProducts'], $item->product_id);
            }
        }

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/coupon/edit_coupon', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Edit Coupon Post
     */
    public function editCouponPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('coupon_code', trans("coupon_code"), 'required|max_length[49]');
        $val->setRule('discount_rate', trans("discount_rate"), 'required');
        $val->setRule('coupon_count', trans("number_of_coupons"), 'required');
        $val->setRule('expiry_date', trans("expiry_date"), 'required');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $couponId = inputPost('id');
            $coupon = $this->couponModel->getCoupon($couponId);
            if (empty($coupon) || ($coupon->seller_id != $this->userId)) {
                return redirect()->to(generateDashUrl('coupons'));
            }
            $code = inputPost('coupon_code');
            $couponByCode = $this->couponModel->getCouponByCode($code);
            if (!empty($couponByCode) && $couponByCode->id != $coupon->id) {
                setErrorMessage(trans("msg_coupon_code_added_before"));
                redirectToBackUrl();
            }
            if ($this->couponModel->editCoupon($couponId)) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        redirectToBackUrl();
    }

    /**
     * Delete Coupon Post
     */
    public function deleteCouponPost()
    {
        $id = inputPost('id');
        $coupon = $this->couponModel->getCoupon($id);
        if (empty($coupon)) {
            exit();
        }
        if ($coupon->seller_id != $this->userId) {
            exit();
        }
        if ($this->couponModel->deleteCoupon($coupon)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        exit();
    }

    /*
     * --------------------------------------------------------------------
     * Refund
     * --------------------------------------------------------------------
     */

    /**
     * Refund Requests
     */
    public function refundRequests()
    {
        $data = $this->setMetaData(trans("refund_requests"));
        $data['numRows'] = $this->orderModel->getRefundRequestCount($this->userId, 'seller');
        $pager = paginate($this->perPage, $data['numRows']);
        $data['refundRequests'] = $this->orderModel->getRefundRequestsPaginated($this->userId, 'seller', $this->perPage, $pager->offset);
        
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/refund/refund_requests', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Refund
     */
    public function refund($id)
    {
        $data = $this->setMetaData(trans("refund"));
        $data['refundRequest'] = $this->orderModel->getRefundRequest($id);
        if (empty($data['refundRequest']) || $data['refundRequest']->seller_id != $this->userId) {
            return redirect()->to(generateDashUrl('refund_requests'));
        }
        $data['product'] = getOrderProduct($data['refundRequest']->order_product_id);
        if (empty($data['product'])) {
            return redirect()->to(generateDashUrl('refund_requests'));
        }
        $data['messages'] = $this->orderModel->getRefundMessages($id);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/refund/refund', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Approve or Decline Refund Request
     */
    public function approveDeclineRefund()
    {
        if ($this->orderModel->approveDeclineRefund()) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /*
     * --------------------------------------------------------------------
     * Payment History
     * --------------------------------------------------------------------
     */

    /**
     * Payment History
     */
    public function paymentHistory()
    {
        $payment = inputGet('payment');
        if ($payment == 'membership') {
            if ($this->generalSettings->membership_plans_system != 1) {
                return redirect()->to(dashboardUrl());
            }
            $data = $this->setMetaData(trans("membership_payments"));
            $data['numRows'] = $this->membershipModel->getMembershipTransactionsCount($this->userId);
            $pager = paginate($this->perPage, $data['numRows']);
            $data['transactions'] = $this->membershipModel->getMembershipTransactionsPaginated($this->userId, $this->perPage, $pager->offset);
            echo view('dashboard/includes/_header', $data);
            echo view('dashboard/payment_history/membership_transactions', $data);
            echo view('dashboard/includes/_footer');
        } elseif ($payment == 'promotion') {
            $data = $this->setMetaData(trans("promotion_payments"));
            $promoteModel = new PromoteModel();
            $data['numRows'] = $promoteModel->getTransactionsCount($this->userId);
            $pager = paginate($this->perPage, $data['numRows']);
            $data['transactions'] = $promoteModel->getTransactionsPaginated($this->userId, $this->perPage, $pager->offset);
            echo view('dashboard/includes/_header', $data);
            echo view('dashboard/payment_history/promotion_transactions', $data);
            echo view('dashboard/includes/_footer');
        } else {
            return redirect()->to(dashboardUrl());
        }
    }

    /*
     * --------------------------------------------------------------------
     * Comments & Reviews
     * --------------------------------------------------------------------
     */

    /**
     * Comments
     */
    public function comments()
    {
        if ($this->generalSettings->product_comments != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("comments"));
        $data['numRows'] = $this->commonModel->getVendorCommentsCount($this->userId);
        $pager = paginate($this->perPage, $data['numRows']);
        $data['comments'] = $this->commonModel->getVendorCommentsPaginated($this->userId, $this->perPage, $pager->offset);
        
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/comments', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Reviews
     */
    public function reviews()
    {
        if ($this->generalSettings->reviews != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("reviews"));
        $data['numRows'] = $this->commonModel->getVendorReviewsCount($this->userId);
        $pager = paginate($this->perPage, $data['numRows']);
        $data['reviews'] = $this->commonModel->getVendorReviewsPaginated($this->userId, $this->perPage, $pager->offset);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/reviews', $data);
        echo view('dashboard/includes/_footer');
    }

    /*
     * --------------------------------------------------------------------
     * Shop Settings
     * --------------------------------------------------------------------
     */

    /**
     * Shop Settings
     */
    public function shopSettings()
    {
        $data = $this->setMetaData(trans("shop_settings"));
        $data['userPlan'] = $this->membershipModel->getUserPlanByUserId($this->userId);
        $data['daysLeft'] = $this->membershipModel->getUserPlanRemainingDaysCount($data['userPlan']);
        $data['adsLeft'] = $this->membershipModel->getUserPlanRemainingAdsCount($data['userPlan']);
        $data['states'] = array();
        $data['cities'] = array();
        
        $locationModel = new LocationModel();
        if (!empty(user()->country_id)) {
            $data['states'] = $locationModel->getStatesByCountry(user()->country_id);
        }
        if (!empty(user()->state_id)) {
            $data['cities'] = $locationModel->getCitiesByState(user()->state_id);
        }
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/shop_settings', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Shop Settings Post
     */
    public function shopSettingsPost()
    {
        $submit = inputPost('submit');
        $profileModel = new ProfileModel();
        if ($submit == 'cash_on_delivery') {
            if ($profileModel->updateCashOnDelivery()) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        } else {
            $shopName = removeSpecialCharacters(inputPost('shop_name'));
            if (!$this->authModel->isUniqueUsername($shopName, $this->userId)) {
                setErrorMessage(trans("msg_shop_name_unique_error"));
                return redirect()->to(generateDashUrl('shop_settings'));
            }
            if ($profileModel->updateShopSettings($shopName)) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        return redirect()->to(generateDashUrl('shop_settings'));
    }

    /*
     * --------------------------------------------------------------------
     * Shipping Settings
     * --------------------------------------------------------------------
     */

    /**
     * Shipping Settings
     */
    public function shippingSettings()
    {
        if (!$this->baseVars->isSaleActive || $this->generalSettings->physical_products_system != 1) {
            return redirect()->to(dashboardUrl());
        }
        $data = $this->setMetaData(trans("shipping_settings"));
        $data['shippingZones'] = $this->shippingModel->getShippingZones($this->userId);
        $data['shippingClasses'] = $this->shippingModel->getShippingClasses($this->userId);
        $data['shippingDeliveryTimes'] = $this->shippingModel->getShippingDeliveryTimes($this->userId, 'DESC');
        
        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/shipping/shipping_settings', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Add Shipping Zone
     */
    public function addShippingZone()
    {
        $data = $this->setMetaData(trans("add_shipping_zone"));
        $data['continents'] = getContinents();
        $data['shippingClasses'] = $this->shippingModel->getActiveShippingClasses($this->userId);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/shipping/add_shipping_zone', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Add Shipping Zone Post
     */
    public function addShippingZonePost()
    {
        if ($this->shippingModel->addShippingZone()) {
            setSuccessMessage(trans("msg_added"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Edit Shipping Zone
     */
    public function editShippingZone($id)
    {
        $data = $this->setMetaData(trans("edit_shipping_zone"));
        $data['shippingZone'] = $this->shippingModel->getShippingZone($id);
        if (empty($data['shippingZone'])) {
            return redirect()->to(generateDashUrl('shipping_settings'));
        }
        $data['continents'] = getContinents();
        $data['shippingClasses'] = $this->shippingModel->getActiveShippingClasses($this->userId);

        echo view('dashboard/includes/_header', $data);
        echo view('dashboard/shipping/edit_shipping_zone', $data);
        echo view('dashboard/includes/_footer');
    }

    /**
     * Edit Shipping Zone Post
     */
    public function editShippingZonePost()
    {
        $zoneId = inputPost('zone_id');
        $shippingZone = $this->shippingModel->getShippingZone($zoneId);
        if (empty($shippingZone)) {
            return redirect()->to(generateDashUrl('shipping_settings'));
        }
        if ($this->shippingModel->editShippingZone($zoneId)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Delete Shipping Location
     */
    public function deleteShippingLocationPost()
    {
        $id = inputPost('id');
        $this->shippingModel->deleteShippingLocation($id);
    }

    //select shipping method
    public function selectShippingMethod()
    {
        $selectedOption = inputPost('selected_option');
        $shippingClasses = $this->shippingModel->getActiveShippingClasses($this->userId);
        $vars = ['selectedOption' => $selectedOption, 'optionUniqueId' => uniqid(), 'shippingClasses' => $shippingClasses];
        $htmlContent = view('dashboard/shipping/_response_shipping_method', $vars);
        $data = [
            'result' => 1,
            'htmlContent' => $htmlContent
        ];
        echo json_encode($data);
    }

    /**
     * Add Shipping Class Post
     */
    public function addShippingClassPost()
    {
        if ($this->shippingModel->addShippingClass()) {
            setSuccessMessage(trans("msg_added"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Edit Shipping Class Post
     */
    public function editShippingClassPost()
    {
        $id = inputPost('id');
        if ($this->shippingModel->editShippingClass($id)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Delete Shipping Class
     */
    public function deleteShippingClassPost()
    {
        $id = inputPost('id');
        if ($this->shippingModel->deleteShippingClass($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Add Shipping Delivery Time Post
     */
    public function addShippingDeliveryTimePost()
    {
        if ($this->shippingModel->addShippingDeliveryTime()) {
            setSuccessMessage(trans("msg_added"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Edit Shipping Delivery Time Post
     */
    public function editShippingDeliveryTimePost()
    {
        $id = inputPost('id');
        if ($this->shippingModel->editShippingDeliveryTime($id)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Delete Shipping Method
     */
    public function deleteShippingMethodPost()
    {
        $id = inputPost('id');
        $this->shippingModel->deleteShippingMethod($id);
    }

    /**
     * Delete Shipping Delivery Time
     */
    public function deleteShippingDeliveryTimePost()
    {
        $id = inputPost('id');
        if ($this->shippingModel->deleteShippingDeliveryTime($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Delete Shipping Zone
     */
    public function deleteShippingZonePost()
    {
        $id = inputPost('id');
        $this->shippingModel->deleteShippingZone($id);
    }

    //set meta data
    private function setMetaData($title)
    {
        return [
            'title' => $title,
            'description' => $title . ' - ' . $this->baseVars->appName,
            'keywords' => $title . ',' . $this->baseVars->appName,
        ];
    }
}
