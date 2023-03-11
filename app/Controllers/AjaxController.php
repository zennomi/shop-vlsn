<?php

namespace App\Controllers;

use App\Models\BlogModel;
use App\Models\EmailModel;
use App\Models\FileModel;
use App\Models\LocationModel;
use App\Models\NewsletterModel;
use App\Models\ShippingModel;
use App\Models\VariationModel;
use Config\Globals;

class AjaxController extends BaseController
{
    protected $commentLimit;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        if (!$this->request->isAJAX()) {
            exit();
        }
        $this->commentLimit = 6;
    }

    /**
     * Load More Promoted Products
     */
    public function loadMorePromotedProducts()
    {
        $productLimit = $this->generalSettings->index_promoted_products_count;
        $offset = clrNum(inputPost('offset'));
        $promotedProducts = $this->productModel->getPromotedProductsLimited($productLimit, $offset);
        $dataJson = [
            'result' => 0,
            'htmlContent' => '',
            'offset' => $offset + $productLimit,
            'hideButton' => 0,
        ];
        $htmlContent = '';
        if (!empty($promotedProducts)) {
            foreach ($promotedProducts as $product) {
                $vars = [
                    'product' => $product,
                    'promoted_badge' => false
                ];
                $htmlContent .= '<div class="col-6 col-sm-4 col-md-3 col-mds-5 col-product">' . view('product/_product_item', $vars) . '</div>';
            }
            $dataJson['result'] = 1;
            $dataJson['htmlContent'] = $htmlContent;
            if ($offset + $productLimit >= $this->productModel->getPromotedProductsCount()) {
                $dataJson['hideButton'] = 1;
            }
        }
        echo json_encode($dataJson);
    }

    /**
     * Hide Cookies Warning
     */
    public function hideCookiesWarning()
    {
        helperSetCookie('cks_warning', '1', time() + (86400 * 365));
    }

    /*
     * --------------------------------------------------------------------
     * Location
     * --------------------------------------------------------------------
     */

    //search location
    public function searchLocation()
    {
        if ($this->generalSettings->location_search_header != 1) {
            exit();
        }
        $inputValue = removeSpecialCharacters(inputPost('input_value'));
        $data = [
            'result' => 0,
            'response' => ''
        ];
        $inputValue = str_replace(',', '', $inputValue ?? '');
        if (!empty($inputValue)) {
            $response = '<ul>';
            $countries = $this->locationModel->searchCountries($inputValue);
            if (!empty($countries)) {
                $data['result'] = 1;
                foreach ($countries as $country) {
                    $response .= '<li><a href="javascript:void(0)" data-country="' . $country->id . '"><i class="icon-map-marker"></i>' . esc($country->name) . '</a></li>';
                }
            }
            $states = $this->locationModel->searchStates($inputValue);
            if (!empty($states)) {
                $data['result'] = 1;
                foreach ($states as $state) {
                    $response .= '<li><a href="javascript:void(0)" data-country="' . $state->country_id . '" data-state="' . $state->id . '"><i class="icon-map-marker"></i>' . esc($state->name) . ', ' . esc($state->country_name) . '</a></li>';
                }
            }
            $cities = $this->locationModel->searchCities($inputValue);
            if (!empty($cities)) {
                $data['result'] = 1;
                foreach ($cities as $city) {
                    $response .= '<li><a href="javascript:void(0)" data-country="' . $city->country_id . '" data-state="' . $city->state_id . '" data-city="' . $city->id . '"><i class="icon-map-marker"></i>' . esc($city->name) . ', ' . esc($city->state_name) . ', ' . esc($city->country_name) . '</a></li>';
                }
            }
            $response .= '</ul>';
            $data['response'] = $response;
        }
        echo json_encode($data);
    }

    /**
     * Set Location
     */
    public function setDefaultLocation()
    {
        $this->locationModel->setDefaultLocation();
    }

    //get states
    public function getStates()
    {
        $countryId = inputPost('country_id');
        $states = $this->locationModel->getStatesByCountry($countryId);
        $status = 0;
        $content = '<option value="">' . trans('state') . '</option>';
        if (!empty($states)) {
            $status = 1;
            foreach ($states as $item) {
                $content .= '<option value="' . $item->id . '">' . esc($item->name) . '</option>';
            }
        }
        $data = [
            'result' => $status,
            'content' => $content
        ];
        echo json_encode($data);
    }

    //get cities
    public function getCities()
    {
        $stateId = inputPost('state_id');
        $cities = $this->locationModel->getCitiesByState($stateId);
        $status = 0;
        $content = '<option value="">' . trans("city") . '</option>';
        if (!empty($cities)) {
            $status = 1;
            foreach ($cities as $item) {
                $content .= '<option value="' . $item->id . '">' . esc($item->name) . '</option>';
            }
        }
        $data = [
            'result' => $status,
            'content' => $content
        ];
        echo json_encode($data);
    }

    //get countries by continent
    public function getCountriesByContinent()
    {
        $key = inputPost('key');
        $model = new LocationModel();
        $countries = $model->getCountriesByContinent($key);
        if (!empty($countries)) {
            foreach ($countries as $country) {
                echo "<option value='" . $country->id . "'>" . esc($country->name) . "</option>";
            }
        }
    }

    //get states by country
    public function getStatesByCountry()
    {
        $countryId = inputPost('country_id');
        $model = new LocationModel();
        $states = $model->getStatesByCountry($countryId);
        if (!empty($states)) {
            foreach ($states as $state) {
                echo "<option value='" . $state->id . "'>" . esc($state->name) . "</option>";
            }
        }
    }

    //get product shipping cost
    public function getProductShippingCost()
    {
        $stateId = inputPost('state_id');
        $productId = inputPost('product_id');
        $shippingModel = new ShippingModel();
        $shippingModel->getProductShippingCost($stateId, $productId);
    }

    /*
     * --------------------------------------------------------------------
     * Search
     * --------------------------------------------------------------------
     */

    //ajax search
    public function ajaxSearch()
    {
        $langBaseUrl = inputPost('lang_base_url');
        $inputValue = inputPost('input_value');
        $category = clrNum(inputPost('category'));
        if (empty($category)) {
            $category = 'all';
        }
        $inputValue = cleanStr($inputValue);
        $data = [
            'result' => 0,
            'response' => ''
        ];
        if (!empty($inputValue)) {
            $data['result'] = 1;
            $response = '<div class="search-results-product"><ul>';
            $products = $this->productModel->searchProducts($inputValue, $category);
            if (!empty($products)) {
                foreach ($products as $product) {
                    $price = '';
                    if ($product->listing_type != 'bidding') {
                        if ($product->is_free_product == 1) {
                            $price = trans("free");
                        } else {
                            if (!empty($product->price)) {
                                if ($product->listing_type == 'ordinary_listing') {
                                    $price = priceFormatted(calculateProductPrice($product->price, $product->discount_rate), $product->currency, false);
                                } else {
                                    $price = priceFormatted(calculateProductPrice($product->price, $product->discount_rate), $product->currency);
                                }
                            }
                        }
                    }
                    $response .= '<li>';
                    $response .= '<a href="' . $langBaseUrl . '/' . $product->slug . '"><div class="left"><div class="search-image"><img src="' . getProductItemImage($product) . '" alt=""></div></div>';
                    $response .= '<div class="search-product"><p class="m-0">' . getProductTitle($product) . '</p><strong class="price">' . $price . '</strong></div></a></li>';
                }
            } else {
                $response .= '<li><a href="javascript:void(0)">' . $inputValue . '</a></li>';
            }
            $response .= '</ul></div>';
            $data['response'] = $response;
        }
        echo json_encode($data);
    }

    //search categories
    public function searchCategories()
    {
        $categoryName = inputPost('category_name');
        $categories = $this->categoryModel->searchCategoriesByName($categoryName);
        $content = '<ul>';
        if (!empty($categories)) {
            foreach ($categories as $item) {
                $content .= '<li>' . esc($item->name) . ' - <strong>' . trans("id") . ': ' . $item->id . '</strong></li>';
            }
            $content .= '</ul>';
        } else {
            $content = '<p class="m-t-15 text-center text-muted">' . trans("no_records_found") . '</p>';
        }
        $data = [
            'result' => 1,
            'content' => $content
        ];
        echo json_encode($data);
    }

    //get subcategories
    public function getSubCategories()
    {
        $parentId = inputPost('parent_id');
        $langId = inputPost('lang_id');
        $htmlContent = '';
        if (!empty($parentId)) {
            $subCategories = $this->categoryModel->getSubCategoriesByParentId($parentId, $langId);
            foreach ($subCategories as $item) {
                $htmlContent .= "<option value='" . $item->id . "'>" . $item->name . "</option>";
            }
        }
        $data = [
            'result' => 1,
            'htmlContent' => $htmlContent,
        ];
        echo json_encode($data);
    }

    /*
     * --------------------------------------------------------------------
     * Variations
     * --------------------------------------------------------------------
     */

    //select variation option
    public function selectProductVariationOption()
    {
        $variationArray = inputPost('variation_array');
        $variationModel = new VariationModel();
        $isInStock = true;
        if (!empty($variationArray)) {
            foreach ($variationArray as $variation) {
                if (!empty($variation['var_option_id'])) {
                    $option = $variationModel->getVariationOption($variation['var_option_id']);
                    if (!empty($option) && $option->is_default != 1) {
                        if ($option->stock <= 0) {
                            $isInStock = false;
                        }
                    }
                }
            }
        }

        $variationId = inputPost('variation_id');
        $selectedOptionId = inputPost('selected_option_id');
        $variation = $variationModel->getVariation($variationId);
        $option = $variationModel->getVariationOption($selectedOptionId);
        $data = [
            'status' => 0,
            'htmlContentSlider' => '',
            'htmlContentPrice' => '',
            'htmlContentStock' => '',
            'stockStatus' => 1,
        ];
        if (!empty($variation) && !empty($option)) {
            $product = $this->productModel->getProduct($variation->product_id);
            //slider content response
            if ($variation->show_images_on_slider) {
                $productImages = $variationModel->getVariationOptionImages($selectedOptionId);
                if (empty($productImages)) {
                    $fileModel = new FileModel();
                    $productImages = $fileModel->getProductImages($variation->product_id);
                }
                $vars = [
                    'product' => $product,
                    'productImages' => $productImages
                ];
                $data['htmlContentSlider'] = view('product/details/_preview', $vars);
            }
            //price content response
            if ($variation->use_different_price == 1) {
                $price = $product->price;
                $discountRate = $product->discount_rate;
                if (isset($option->price)) {
                    $price = $option->price;
                }
                if (isset($option->discount_rate)) {
                    $discountRate = $option->discount_rate;
                }
                if (empty($price)) {
                    $price = $product->price;
                    $discountRate = $product->discount_rate;
                }
                $vars = [
                    'product' => $product,
                    'price' => $price,
                    'discountRate' => $discountRate
                ];
                $data['htmlContentPrice'] = view('product/details/_price', $vars);
            }
            //stock content response
            if ($isInStock) {
                $data['htmlContentStock'] = '<span class="text-success">' . trans("in_stock") . '</span>';
            } else {
                $data['htmlContentStock'] = '<span class="text-danger">' . trans("out_of_stock") . '</span>';
                $data['stockStatus'] = 0;
            }
            $data['status'] = 1;
        }
        echo json_encode($data);
    }

    //get sub variation options
    public function getSubVariationOptions()
    {
        $variationId = inputPost('variation_id');
        $selectedOptionId = inputPost('selected_option_id');
        $variationModel = new VariationModel();
        $subvariation = $variationModel->getProductSubVariation($variationId);
        $content = null;
        $data = [
            'status' => 0,
            'subVariationId' => '',
            'htmlContent' => ''
        ];
        if (!empty($subvariation)) {
            $options = $variationModel->getVariationSubOptions($selectedOptionId);
            if (!empty($options)) {
                $content .= '<option value="">' . trans("select") . '</option>';
                foreach ($options as $option) {
                    $option_name = getVariationOptionName($option->option_names, selectedLangId());
                    $content .= '<option value="' . $option->id . '">' . esc($option_name) . '</option>';
                }
            }
            $data['status'] = 1;
            $data['subVariationId'] = $subvariation->id;
            $data['htmlContent'] = $content;
        }
        echo json_encode($data);
    }

    /*
     * --------------------------------------------------------------------
     * Wishlist
     * --------------------------------------------------------------------
     */

    //add or remove wishlist
    public function addRemoveWishlist()
    {
        $productId = inputPost('product_id');
        $this->productModel->addRemoveWishlist($productId);
    }

    /*
     * --------------------------------------------------------------------
     * Product Comment
     * --------------------------------------------------------------------
     */

    //add comment
    public function addComment()
    {
        if ($this->generalSettings->product_comments != 1) {
            exit();
        }
        $limit = inputPost('limit');
        $productId = inputPost('product_id');
        if (authCheck()) {
            $this->commonModel->addComment();
        } else {
            if (reCAPTCHA('validate') != 'invalid') {
                $this->commonModel->addComment();
            }
        }
        if (hasPermission('comments')) {
            $this->generateCommentHtmlContent($productId, $limit);
            exit();
        }
        if ($this->generalSettings->comment_approval_system == 1) {
            $data = [
                'type' => 'message',
                'htmlContent' => "<p class='comment-success-message'><i class='icon-check'></i>&nbsp;&nbsp;" . trans("msg_comment_sent_successfully") . "</p>"
            ];
            echo json_encode($data);
        } else {
            $this->generateCommentHtmlContent($productId, $limit);
        }
    }

    //load more comments
    public function loadMoreComments()
    {
        $productId = inputPost('product_id');
        $limit = inputPost('limit');
        $newLimit = $limit + $this->commentLimit;
        $this->generateCommentHtmlContent($productId, $newLimit);
    }

    //delete comment
    public function deleteComment()
    {
        $id = inputPost('id');
        $productId = inputPost('product_id');
        $limit = inputPost('limit');
        $comment = $this->commonModel->getComment($id);
        if (authCheck() && !empty($comment)) {
            if (hasPermission('comments') || user()->id == $comment->user_id) {
                $this->commonModel->deleteComment($id);
            }
        }
        $this->generateCommentHtmlContent($productId, $limit);
    }

    //load subcomment form
    public function loadSubCommentForm()
    {
        $commentId = inputPost('comment_id');
        $limit = inputPost('limit');
        $vars = [
            'parentComment' => $this->commonModel->getComment($commentId),
            'commentLimit' => $limit
        ];
        $data = [
            'type' => 'form',
            'htmlContent' => view('product/details/_add_subcomment', $vars),
        ];
        echo json_encode($data);
    }

    //generate comment html content
    private function generateCommentHtmlContent($productId, $limit)
    {
        $vars = [
            'product' => $this->productModel->getProduct($productId),
            'commentCount' => $this->commonModel->getProductCommentCount($productId),
            'comments' => $this->commonModel->getCommentsByProductId($productId, $limit),
            'commentLimit' => $limit
        ];
        $data = [
            'type' => 'comments',
            'htmlContent' => view('product/details/_comments', $vars),
            'commentLimit' => $vars['commentLimit']
        ];
        echo json_encode($data);
    }

    /*
     * --------------------------------------------------------------------
     * Blog
     * --------------------------------------------------------------------
     */

    /**
     * Get Blog Categories by Language
     */
    public function getBlogCategoriesByLang()
    {
        $model = new BlogModel();
        $langId = inputPost('lang_id');
        if (!empty($langId)) {
            $categories = $model->getCategoriesByLang($langId);
            if (!empty($categories)) {
                foreach ($categories as $item) {
                    echo '<option value="' . $item->id . '">' . esc($item->name) . '</option>';
                }
            }
        }
    }

    /**
     * Add Blog Comment
     */
    public function addBlogComment()
    {
        if ($this->generalSettings->blog_comments != 1) {
            exit();
        }
        $postId = inputPost('post_id');
        $limit = inputPost('limit');
        $blogModel = new BlogModel();
        if (authCheck()) {
            $blogModel->addComment();
        } else {
            if (reCAPTCHA('validate') != 'invalid') {
                $blogModel->addComment();
            }
        }
        if ($this->generalSettings->comment_approval_system == 1) {
            $data = [
                'type' => 'message',
                'htmlContent' => "<p class='comment-success-message'><i class='icon-check'></i>&nbsp;&nbsp;" . trans("msg_comment_sent_successfully") . "</p>"
            ];
            echo json_encode($data);
        } else {
            $this->generateCommentBlogHtmlContent($blogModel, $postId, $limit);
        }
    }

    /**
     * Delete Blog Comment
     */
    public function deleteBlogComment()
    {
        $commentId = inputPost('comment_id');
        $postId = inputPost('post_id');
        $limit = inputPost('limit');
        $blogModel = new BlogModel();
        $comment = $blogModel->getComment($commentId);
        if (authCheck() && !empty($comment)) {
            if (hasPermission('comments') || user()->id == $comment->user_id) {
                $blogModel->deleteComment($comment->id);
            }
        }
        $this->generateCommentBlogHtmlContent($blogModel, $postId, $limit);
    }

    /**
     * Load More Comments
     */
    public function loadMoreBlogComments()
    {
        $blogModel = new BlogModel();
        $postId = inputPost('post_id');
        $limit = inputPost('limit');
        $newLimit = $limit + $this->commentLimit;
        $this->generateCommentBlogHtmlContent($blogModel, $postId, $newLimit);
    }

    //generate blog comment html content
    private function generateCommentBlogHtmlContent($blogModel, $postId, $limit)
    {
        $vars = [
            'comments' => $blogModel->getCommentsByPostId($postId, $limit),
            'commentPostId' => $postId,
            'commentsCount' => $blogModel->getCommentCount($postId),
            'commentLimit' => $limit
        ];
        $data = [
            'type' => 'comments',
            'htmlContent' => view('blog/_blog_comments', $vars),
        ];
        echo json_encode($data);
    }

    /*
     * --------------------------------------------------------------------
     * Abuse Reports
     * --------------------------------------------------------------------
     */

    //report abuse
    public function reportAbusePost()
    {
        if (!authCheck()) {
            exit();
        }
        $data = [
            'message' => "<p class='text-danger'>" . trans("msg_error") . "</p>"
        ];
        if ($this->commonModel->reportAbuse()) {
            $data['message'] = "<p class='text-success'>" . trans("abuse_report_msg") . "</p>";
        }
        echo json_encode($data);
    }

    /*
     * --------------------------------------------------------------------
     * Newsletter
     * --------------------------------------------------------------------
     */

    /**
     * Add to Newsletter
     */
    public function addToNewsletter()
    {
        $vld = inputPost('url');
        if (!empty($vld)) {
            exit();
        }
        $data = [
            'result' => 0,
            'response' => '',
            'isSuccess' => '',
        ];
        $email = cleanStr(inputPost('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $data['response'] = '<p class="text-danger m-t-5">' . trans("msg_invalid_email") . '</p>';
        } else {
            if ($email) {
                $newsletterModel = new NewsletterModel();
                if (empty($newsletterModel->getSubscriber($email))) {
                    if ($newsletterModel->addSubscriber($email)) {
                        $data['response'] = '<p class="text-success m-t-5">' . trans("msg_newsletter_success") . '</p>';
                        $data['is_success'] = 1;
                    }
                } else {
                    $data['response'] = '<p class="text-danger m-t-5">' . trans("msg_newsletter_error") . '</p>';
                }
            }
        }
        $data['result'] = 1;
        echo json_encode($data);
    }

    /*
     * --------------------------------------------------------------------
     * Email Functions
     * --------------------------------------------------------------------
     */

    /**
     * Run Email Queue
     */
    public function runEmailQueue()
    {
        $emailModel = new EmailModel();
        $emailModel->runEmailQueue();
    }
}
