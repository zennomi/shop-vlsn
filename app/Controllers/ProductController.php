<?php

namespace App\Controllers;

use App\Models\BiddingModel;
use App\Models\ProductAdminModel;
use App\Models\ProductModel;
use App\Models\PromoteModel;

class ProductController extends BaseAdminController
{
    protected $productModel;
    protected $productAdminModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->productModel = new ProductModel();
        $this->productAdminModel = new ProductAdminModel();
    }

    /**
     * Products
     */
    public function products()
    {
        checkPermission('products');
        $data = array();
        $list = inputGet('list');
        if ($list == 'special') {
            $data = ['title' => trans("special_offers"), 'listType' => 'special_offers', 'view' => 'products', 'list' => $list];
        } elseif ($list == 'pending') {
            $data = ['title' => trans("pending_products"), 'listType' => 'pending_products', 'view' => 'pending_products', 'list' => $list];
        } elseif ($list == 'hidden') {
            $data = ['title' => trans("hidden_products"), 'listType' => 'hidden_products', 'view' => 'products', 'list' => $list];
        } elseif ($list == 'expired') {
            $data = ['title' => trans("expired_products"), 'listType' => 'expired_products', 'view' => 'products', 'list' => $list];
        } elseif ($list == 'sold') {
            $data = ['title' => trans("sold_products"), 'listType' => 'sold_products', 'view' => 'sold_products', 'list' => $list];
        } elseif ($list == 'drafts') {
            $data = ['title' => trans("drafts"), 'listType' => 'drafts', 'view' => 'drafts', 'list' => $list];
        } elseif ($list == 'deleted') {
            $data = ['title' => trans("deleted_products"), 'listType' => 'deleted_products', 'view' => 'deleted_products', 'list' => $list];
        } else {
            $data = ['title' => trans("products"), 'listType' => 'products', 'view' => 'products', 'list' => 'all'];
        }
        
        $numRows = $this->productAdminModel->getFilteredProductCount($data['listType']);
        $pager = paginate($this->perPage, $numRows);
        $data['products'] = $this->productAdminModel->getFilteredProductsPaginated($this->perPage, $pager->offset, $data['listType']);

        echo view('admin/includes/_header', $data);
        echo view('admin/product/' . $data['view'], $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Featured Products
     */
    public function featuredProducts()
    {
        checkPermission('products');
        $data = ['title' => trans("featured_products"), 'listType' => 'featured_products', 'list' => 'featured'];
        $numRows = $this->productAdminModel->getFilteredProductCount($data['listType']);
        $pager = paginate($this->perPage, $numRows);
        $data['products'] = $this->productAdminModel->getFilteredProductsPaginated($this->perPage, $pager->offset, $data['listType']);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/product/featured_products', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Featured Products Pricing
     */
    public function featuredProductsPricing()
    {
        checkPermission('products');
        $data['title'] = trans("pricing");

        echo view('admin/includes/_header', $data);
        echo view('admin/product/featured_products_pricing', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Featured Products Pricing Post
     */
    public function featuredProductsPricingPost()
    {
        checkPermission('products');
        if ($this->settingsModel->updateFeaturedProductsPricingSettings()) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Featured Products Transactions
     */
    public function featuredProductsTransactions()
    {
        checkPermission('products');
        $data['title'] = trans("featured_products_transactions");
        $model = new PromoteModel();
        $numRows = $model->getTransactionsCount(null);
        $pager = paginate($this->perPage, $numRows);
        $data['transactions'] = $model->getTransactionsPaginated(null, $this->perPage, $pager->offset);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/product/featured_products_transactions', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Delete Featured Transaction Post
     */
    public function deleteFeaturedTransactionPost()
    {
        checkPermission('products');
        $id = inputPost('id');
        $model = new PromoteModel();
        if ($model->deleteTransaction($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Product Details
     */
    public function productDetails($id)
    {
        checkPermission('products');
        $data['title'] = trans("product_details");
        $data['product'] = $this->productAdminModel->getProduct($id);
        if (empty($data['product'])) {
            return redirect()->to(adminUrl('products'));
        }
        $data['productDetails'] = $this->productModel->getProductDetails($data['product']->id, selectedLangId(), true);
        $data['reviewCount'] = $this->commonModel->getReviewCountByProductId($data['product']->id);
        $data['video'] = $this->fileModel->getProductVideo($data['product']->id);
        $data['audio'] = $this->fileModel->getProductAudio($data['product']->id);
        $data['digitalFile'] = $this->fileModel->getProductDigitalFile($data['product']->id);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/product/product_details', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Approve Product
     */
    public function approveProduct()
    {
        checkPermission('products');
        $id = inputPost('id');
        $isAjax = inputPost('isAjax');
        if ($this->productAdminModel->approveProduct($id)) {
            setSuccessMessage(trans("msg_product_approved"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        resetCacheDataOnChange();
        if (!$isAjax) {
            redirectToBackUrl();
        }
    }

    /**
     * Reject Product
     */
    public function rejectProduct()
    {
        checkPermission('products');
        $id = inputPost('id');
        if ($this->productAdminModel->rejectProduct($id)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        resetCacheDataOnChange();
        redirectToBackUrl();
    }

    /**
     * Restore Product
     */
    public function restoreProduct()
    {
        checkPermission('products');
        $id = inputPost('id');
        if ($this->productAdminModel->restoreProduct($id)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        resetCacheDataOnChange();
    }

    /**
     * Delete Product
     */
    public function deleteProduct()
    {
        checkPermission('products');
        $id = inputPost('id');
        if ($this->productAdminModel->deleteProduct($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        resetCacheDataOnChange();
    }

    /**
     * Delete Product Permanently
     */
    public function deleteProductPermanently()
    {
        checkPermission('products');
        $id = inputPost('id');
        if ($this->productAdminModel->deleteProductPermanently($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        resetCacheDataOnChange();
    }

    /**
     * Delete Selected Products
     */
    public function deleteSelectedProducts()
    {
        checkPermission('products');
        $productIds = inputPost('product_ids');
        $this->productAdminModel->deleteMultiProducts($productIds);
        resetCacheDataOnChange();
    }

    /**
     * Delete Selected Products Permanently
     */
    public function deleteSelectedProductsPermanently()
    {
        checkPermission('products');
        $productIds = inputPost('product_ids');
        $this->productAdminModel->deleteSelectedProductsPermanently($productIds);
        resetCacheDataOnChange();
    }

    /**
     * Add Remove Featured Product
     */
    public function addRemoveFeaturedProduct()
    {
        checkPermission('products');
        $productId = inputPost('product_id');
        $transactionId = inputPost('transaction_id');
        $dayCount = inputPost('day_count');
        $isAjax = inputPost('is_ajax');
        if ($this->productAdminModel->addRemoveFeaturedProduct($productId, $dayCount)) {
            setSuccessMessage(trans("msg_updated"));
            resetCacheDataOnChange();
        } else {
            setErrorMessage(trans("msg_error"));
        }
        if ($isAjax == 0) {
            redirectToBackUrl();
        }
    }

    /**
     * Add Remove Special Offers
     */
    public function addRemoveSpecialOffer()
    {
        checkPermission('products');
        $productId = inputPost('product_id');
        if ($this->productAdminModel->addRemoveSpecialOffer($productId)) {
            setSuccessMessage(trans("msg_updated"));
            resetCacheDataOnChange();
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Quote Requests
     */
    public function quoteRequests()
    {
        checkPermission('quote_requests');
        $data['title'] = trans("quote_requests");
        
        $model = new BiddingModel();
        $numRows = $model->getQuoteRequestCountAdmin();
        $pager = paginate($this->perPage, $numRows);
        $data['quoteRequests'] = $model->getQuoteRequestsPaginatedAdmin($this->perPage, $pager->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/bidding/quote_requests', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Delete Quote Request
     */
    public function deleteQuoteRequestPost()
    {
        checkPermission('quote_requests');
        $id = inputPost('id');
        $model = new BiddingModel();
        if ($model->deleteQuoteRequestAdmin($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Comments
     */
    public function comments()
    {
        checkPermission('comments');
        $data['title'] = trans("approved_comments");
        $data['topButtonText'] = trans("pending_comments");
        $data['topButtonUrl'] = adminUrl('pending-product-comments');
        $data['showApproveButton'] = false;
        $numRows = $this->commonModel->getCommentCount(1);
        $pager = paginate($this->perPage, $numRows);
        $data['comments'] = $this->commonModel->getCommentsPaginated(1, $this->perPage, $pager->offset);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/comment/comments', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Pending Comments
     */
    public function pendingComments()
    {
        checkPermission('comments');
        $data['title'] = trans("pending_comments");
        $data['topButtonText'] = trans("approved_comments");
        $data['topButtonUrl'] = adminUrl('product-comments');
        $data['showApproveButton'] = true;
        
        $numRows = $this->commonModel->getCommentCount(0);
        $pager = paginate($this->perPage, $numRows);
        $data['comments'] = $this->commonModel->getCommentsPaginated(0, $this->perPage, $pager->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/comment/comments', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Aprrove Comment Post
     */
    public function approveCommentPost()
    {
        checkPermission('comments');
        $id = inputPost('id');
        if ($this->commonModel->approveComment($id)) {
            setSuccessMessage(trans("msg_comment_approved"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Approve Selected Comments
     */
    public function approveSelectedComments()
    {
        checkPermission('comments');
        $commentIds = inputPost('comment_ids');
        $this->commonModel->approveMultiComments($commentIds);
    }

    /**
     * Delete Comment
     */
    public function deleteCommentPost()
    {
        checkPermission('comments');
        $id = inputPost('id');
        if ($this->commonModel->deleteComment($id)) {
            setSuccessMessage(trans("msg_comment_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Delete Selected Comments
     */
    public function deleteSelectedComments()
    {
        checkPermission('comments');
        $commentIds = inputPost('comment_ids');
        $this->commonModel->deleteMultiComments($commentIds);
    }

    /**
     * Reviews
     */
    public function reviews()
    {
        checkPermission('reviews');
        $data['title'] = trans("reviews");
        $numRows = $this->commonModel->getReviewCount();
        $pager = paginate($this->perPage, $numRows);
        $data['reviews'] = $this->commonModel->getReviewsPaginated($this->perPage, $pager->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/review/reviews', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Delete Review
     */
    public function deleteReviewPost()
    {
        checkPermission('reviews');
        $id = inputPost('id');
        if ($this->commonModel->deleteReview($id)) {
            setSuccessMessage(trans("msg_review_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Delete Selected Reviews
     */
    public function deleteSelectedReviews()
    {
        checkPermission('reviews');
        $reviewIds = inputPost('review_ids');
        $this->commonModel->deleteSelectedReviews($reviewIds);
    }
}