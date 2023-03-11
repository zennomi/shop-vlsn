<?php namespace App\Models;

use CodeIgniter\Model;

class CommonModel extends BaseModel
{
    protected $builderSlider;
    protected $builderBanners;
    protected $builderAbuseReports;
    protected $builderReviews;
    protected $builderComments;
    protected $builderContact;
    protected $builderAds;

    public function __construct()
    {
        parent::__construct();
        $this->builderSlider = $this->db->table('slider');
        $this->builderBanners = $this->db->table('homepage_banners');
        $this->builderAbuseReports = $this->db->table('abuse_reports');
        $this->builderReviews = $this->db->table('reviews');
        $this->builderComments = $this->db->table('comments');
        $this->builderContact = $this->db->table('contacts');
        $this->builderAds = $this->db->table('ad_spaces');
    }

    /*
     * --------------------------------------------------------------------
     * Slider
     * --------------------------------------------------------------------
     */

    //add item
    public function addSliderItem()
    {
        $data = [
            'lang_id' => inputPost('lang_id'),
            'title' => inputPost('title'),
            'description' => inputPost('description'),
            'link' => inputPost('link'),
            'item_order' => inputPost('item_order'),
            'button_text' => inputPost('button_text'),
            'text_color' => inputPost('text_color'),
            'button_color' => inputPost('button_color'),
            'button_text_color' => inputPost('button_text_color'),
            'animation_title' => inputPost('animation_title'),
            'animation_description' => inputPost('animation_description'),
            'animation_button' => inputPost('animation_button')
        ];
        $uploadModel = new UploadModel();
        $tempFile = $uploadModel->uploadTempFile('file');
        if (!empty($tempFile) && !empty($tempFile['path'])) {
            $data['image'] = $uploadModel->uploadSliderImage($tempFile['path'], false);
            $uploadModel->deleteTempFile($tempFile['path']);
        }
        $tempFileMobile = $uploadModel->uploadTempFile('file_mobile');
        if (!empty($tempFileMobile) && !empty($tempFileMobile['path'])) {
            $data['image_mobile'] = $uploadModel->uploadSliderImage($tempFileMobile['path'], true);
            $uploadModel->deleteTempFile($tempFileMobile['path']);
        }
        return $this->builderSlider->insert($data);
    }

    //edit slider item
    public function editSliderItem($id)
    {
        $item = $this->getSliderItem($id);
        if (!empty($item)) {
            $data = [
                'lang_id' => inputPost('lang_id'),
                'title' => inputPost('title'),
                'description' => inputPost('description'),
                'link' => inputPost('link'),
                'item_order' => inputPost('item_order'),
                'button_text' => inputPost('button_text'),
                'text_color' => inputPost('text_color'),
                'button_color' => inputPost('button_color'),
                'button_text_color' => inputPost('button_text_color'),
                'animation_title' => inputPost('animation_title'),
                'animation_description' => inputPost('animation_description'),
                'animation_button' => inputPost('animation_button')
            ];
            $uploadModel = new UploadModel();
            $tempFile = $uploadModel->uploadTempFile('file');
            if (!empty($tempFile) && !empty($tempFile['path'])) {
                deleteFile($item->image);
                $data['image'] = $uploadModel->uploadSliderImage($tempFile['path'], false);
            }
            $tempFileMobile = $uploadModel->uploadTempFile('file_mobile');
            if (!empty($tempFileMobile) && !empty($tempFileMobile['path'])) {
                deleteFile($item->image_mobile);
                $data['image_mobile'] = $uploadModel->uploadSliderImage($tempFileMobile['path'], true);
            }
            return $this->builderSlider->where('id', $item->id)->update($data);
        }
        return false;
    }

    //get slider item
    public function getSliderItem($id)
    {
        return $this->builderSlider->where('id', clrNum($id))->get()->getRow();
    }

    //get slider items
    public function getSliderItems()
    {
        return $this->builderSlider->orderBy('item_order')->get()->getResult();
    }

    //get slider items by languages
    public function getSliderItemsByLang($langId)
    {
        return $this->builderSlider->where('lang_id', clrNum($langId))->orderBy('item_order')->get()->getResult();
    }

    //edit slider settings
    public function editSliderSettings()
    {
        $data = [
            'slider_status' => inputPost('slider_status'),
            'slider_type' => inputPost('slider_type'),
            'slider_effect' => inputPost('slider_effect')
        ];
        return $this->db->table('general_settings')->where('id', 1)->update($data);
    }

    //delete slider item
    public function deleteSliderItem($id)
    {
        $item = $this->getSliderItem($id);
        if (!empty($item)) {
            deleteFile($item->image);
            deleteFile($item->image_mobile);
            return $this->builderSlider->where('id', $item->id)->delete();
        }
        return false;
    }

    /*
     * --------------------------------------------------------------------
     * Index Banners
     * --------------------------------------------------------------------
     */

    //add index banner
    public function addIndexBanner()
    {
        $data = [
            'banner_url' => addHTTPS(inputPost('banner_url')),
            'banner_order' => inputPost('banner_order'),
            'banner_width' => inputPost('banner_width'),
            'banner_location' => inputPost('banner_location')
        ];
        if ($data['banner_width'] > 100) {
            $data['banner_width'] = 100;
        }
        $uploadModel = new UploadModel();
        $file = $uploadModel->uploadAd('file');
        if (!empty($file) && !empty($file['path'])) {
            $data['banner_image_path'] = $file['path'];
        }
        return $this->builderBanners->insert($data);
    }

    //edit index banner
    public function editIndexBanner($id)
    {
        $banner = $this->getIndexBanner($id);
        if (!empty($banner)) {
            $data = [
                'banner_url' => addHTTPS(inputPost('banner_url')),
                'banner_order' => inputPost('banner_order'),
                'banner_width' => inputPost('banner_width'),
                'banner_location' => inputPost('banner_location')
            ];
            if ($data['banner_width'] > 100) {
                $data['banner_width'] = 100;
            }
            $uploadModel = new UploadModel();
            $file = $uploadModel->uploadAd('file');
            if (!empty($file) && !empty($file['path'])) {
                $data['banner_image_path'] = $file['path'];
            }
            return $this->builderBanners->where('id', $banner->id)->update($data);
        }
        return false;
    }

    //get index banner
    public function getIndexBanner($id)
    {
        return $this->builderBanners->where('id', clrNum($id))->get()->getRow();
    }

    //get index banners
    public function getIndexBanners()
    {
        return $this->builderBanners->orderBy('banner_order')->get()->getResult();
    }

    //get index banners array
    public function getIndexBannersArray()
    {
        $banners = $this->getIndexBanners();
        $array = array();
        if (!empty($banners)) {
            foreach ($banners as $banner) {
                @$array[$banner->banner_location][] = $banner;
            }
        }
        return $array;
    }

    //delete index banner
    public function deleteIndexBanner($id)
    {
        $banner = $this->getIndexBanner($id);
        if (!empty($banner)) {
            deleteFile($banner->banner_image_path);
            return $this->builderBanners->where('id', $banner->id)->delete();
        }
        return false;
    }

    /*
     * --------------------------------------------------------------------
     * Abuse Reports
     * --------------------------------------------------------------------
     */

    //report abuse
    public function reportAbuse()
    {
        $data = [
            'item_type' => inputPost('item_type'),
            'item_id' => inputPost('id'),
            'report_user_id' => user()->id,
            'description' => inputPost('description'),
            'created_at' => date("Y-m-d H:i:s")
        ];
        if (empty($data['item_id'])) {
            $data['item_id'] = 0;
        }
        return $this->builderAbuseReports->insert($data);
    }

    //get abuse reports count
    public function getAbuseReportsCount()
    {
        return $this->builderAbuseReports->countAllResults();
    }

    //get paginated abuse reports
    public function getAbuseReportsPaginated($perPage, $offset)
    {
        return $this->builderAbuseReports->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //delete abuse report
    public function deleteAbuseReport($id)
    {
        return $this->builderAbuseReports->where('id', clrNum($id))->delete();
    }

    /*
     * --------------------------------------------------------------------
     * Ad Spaces
     * --------------------------------------------------------------------
     */

    public function updateAdSpaces($id)
    {
        $adSpace = $this->getAdSpaceById($id);
        if (!empty($adSpace)) {
            $uploadModel = new UploadModel();
            $data = [
                'ad_code_desktop' => inputPost('ad_code_desktop'),
                'ad_code_mobile' => inputPost('ad_code_mobile'),
                'desktop_width' => inputPost('desktop_width'),
                'desktop_height' => inputPost('desktop_height'),
                'mobile_width' => inputPost('mobile_width'),
                'mobile_height' => inputPost('mobile_height')
            ];
            $adURL = inputPost('url_ad_code_desktop');
            $file = $uploadModel->uploadAd('file_ad_code_desktop');
            if (!empty($file) && !empty($file['path'])) {
                $data['ad_code_desktop'] = $this->createAdCode($adURL, $file['path'], $data['desktop_width'], $data['desktop_height']);
            }
            $adURL = inputPost('url_ad_code_mobile');
            $file = $uploadModel->uploadAd('file_ad_code_mobile');
            if (!empty($file) && !empty($file['path'])) {
                $data['ad_code_mobile'] = $this->createAdCode($adURL, $file['path'], $data['mobile_width'], $data['mobile_height']);
            }
            return $this->builderAds->where('id', $adSpace->id)->update($data);
        }
        return false;
    }

    //get ad spaces
    public function getAdSpaces()
    {
        return $this->builderAds->get()->getResult();
    }

    //get ad spaces by lang
    public function getAdSpacesByLang($langId)
    {
        return $this->builderAds->where('lang_id', clrNum($langId))->get()->getResult();
    }

    //get ad spaces by id
    public function getAdSpaceById($id)
    {
        return $this->builderAds->where('id', clrNum($id))->get()->getRow();
    }

    //get ad space
    public function getAdSpace($adSpace, $adSpaceArray)
    {
        $row = $this->builderAds->where('ad_space', cleanStr($adSpace))->get()->getRow();
        if (!empty($row)) {
            return $row;
        }
        $addNew = false;
        foreach ($adSpaceArray as $key => $value) {
            if ($key == strSlug($adSpace)) {
                $addNew = true;
            }
        }
        if ($addNew) {
            $data = [
                'ad_space' => strSlug($adSpace),
                'ad_code_desktop' => '',
                'desktop_width' => 728,
                'desktop_height' => 90,
                'ad_code_mobile' => '',
                'mobile_width' => 300,
                'mobile_height' => 250,
                'mobile_width' => 300,
            ];
            if ($adSpace == 'sidebar_1' || $adSpace == 'sidebar_2') {
                $data['desktop_width'] = 336;
                $data['desktop_height'] = 280;
            }
            $this->builderAds->insert($data);
            return $this->builderAds->where('ad_space', cleanStr($adSpace))->get()->getRow();
        }
        return false;
    }

    //create ad code
    public function createAdCode($url, $imgPath, $width, $height)
    {
        return '<a href="' . $url . '" aria-label="link-bn' . '"><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="' . base_url($imgPath) . '" width="' . $width . '" height="' . $height . '" alt="" class="lazyload"></a>';
    }

    //update google adsense code
    public function updateGoogleAdsenseCode()
    {
        return $this->db->table('general_settings')->where('id', 1)->update(['google_adsense_code' => inputPost('google_adsense_code')]);
    }

    /*
     * --------------------------------------------------------------------
     * Reviews
     * --------------------------------------------------------------------
     */

    //add review
    public function addReview($rating, $productId, $reviewText)
    {
        $data = [
            'product_id' => $productId,
            'user_id' => user()->id,
            'rating' => $rating,
            'review' => $reviewText,
            'ip_address' => 0,
            'created_at' => date("Y-m-d H:i:s")
        ];
        $ip = getIPAddress();
        if (!empty($ip)) {
            $data['ip_address'] = $ip;
        }
        if (!empty($data['product_id']) && !empty($data['user_id']) && !empty($data['rating'])) {
            $this->builderReviews->insert($data);
            $this->updateProductRating($productId);
        }
    }

    //update review
    public function updateReview($review_id, $rating, $productId, $reviewText)
    {
        $data = [
            'rating' => $rating,
            'review' => $reviewText,
            'ip_address' => 0,
            'created_at' => date("Y-m-d H:i:s")
        ];
        $ip = getIPAddress();
        if (!empty($ip)) {
            $data['ip_address'] = $ip;
        }
        if (!empty($data['rating']) && !empty($data['review'])) {
            $this->builderReviews->where('product_id', clrNum($productId))->where('user_id', user()->id)->update($data);
            $this->updateProductRating($productId);
        }
    }

    //get review count
    public function getReviewCount()
    {
        $this->filterReviews();
        return $this->builderReviews->join('users', 'users.id = reviews.user_id')->join('products', 'products.id = reviews.product_id')
            ->select('reviews.*, users.username as user_username, users.slug as user_slug')->countAllResults();
    }

    //get paginated reviews
    public function getReviewsPaginated($perPage, $offset)
    {
        $this->filterReviews();
        return $this->builderReviews->join('users', 'users.id = reviews.user_id')->join('products', 'products.id = reviews.product_id')
            ->select('reviews.*, users.username as user_username, users.slug as user_slug')->orderBy('reviews.created_at DESC')
            ->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter reviews
    public function filterReviews()
    {
        $q = inputGet('q');
        if (!empty($q)) {
            $this->builderReviews->like('review', cleanStr($q))->orLike('users.username', cleanStr($q));
        }
    }

    //get review count
    public function getReviewCountByProductId($productId)
    {
        return $this->builderReviews->join('users', 'users.id = reviews.user_id')->where('reviews.product_id', clrNum($productId))->countAllResults();
    }

    //get reviews
    public function getReviewsByProductId($productId)
    {
        return $this->builderReviews->join('users', 'users.id = reviews.user_id')->select('reviews.*, users.username as user_username, users.slug as user_slug')
            ->where('reviews.product_id', clrNum($productId))->orderBy('reviews.created_at DESC')->get()->getResult();
    }

    //get latest reviews
    public function getLatestReviews($limit)
    {
        return $this->builderReviews->join('users', 'users.id = reviews.user_id')->select('reviews.*, users.username as user_username')
            ->orderBy('reviews.created_at DESC')->get(clrNum($limit))->getResult();
    }

    //get review
    public function getReview($productId, $userId)
    {
        return $this->builderReviews->join('users', 'users.id = reviews.user_id')->select('reviews.*, users.username as user_username, users.slug as user_slug')
            ->where('reviews.product_id', $productId)->where('users.id', $userId)->get()->getRow();
    }

    //get review by id
    public function getReviewById($id)
    {
        return $this->builderReviews->where('id', clrNum($id))->get()->getRow();
    }

    //update product rating
    public function updateProductRating($productId)
    {
        $reviews = $this->getReviewsByProductId($productId);
        $data = array();
        if (!empty($reviews)) {
            $count = countItems($reviews);
            $total = 0;
            foreach ($reviews as $review) {
                $total += $review->rating;
            }
            $data['rating'] = round($total / $count);
        } else {
            $data['rating'] = 0;
        }
        $this->db->table('products')->where('id', clrNum($productId))->update($data);
    }

    //get vendor reviews count
    public function getVendorReviewsCount($userId)
    {
        return $this->builderReviews->join('users', 'users.id = reviews.user_id')->join('products', 'products.id = reviews.product_id')
            ->select('reviews.*, users.username as user_username, users.slug as user_slug')->where('products.user_id', clrNum($userId))->countAllResults();
    }

    //get paginated vendor reviews
    public function getVendorReviewsPaginated($userId, $perPage, $offset)
    {
        return $this->builderReviews->join('users', 'users.id = reviews.user_id')->join('products', 'products.id = reviews.product_id')->select('reviews.*, users.username AS user_username, users.slug AS user_slug')
            ->where('products.user_id', clrNum($userId))->orderBy('reviews.created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //calculate user rating
    public function calculateUserRating($userId)
    {
        $std = new \stdClass();
        $std->count = 0;
        $std->rating = 0;
        $row = $this->builderReviews->join('users', 'users.id = reviews.user_id')->join('products', 'products.id = reviews.product_id')->select('COUNT(reviews.id) AS count, SUM(reviews.rating) AS total')
            ->where('products.user_id', clrNum($userId))->get()->getRow();
        if (!empty($row)) {
            $total = $row->total;
            $count = $row->count;
            if (!empty($total) && !empty($count)) {
                $avg = round($total / $count);
                $std->count = $count;
                $std->rating = $avg;
            }
        }
        return $std;
    }

    //delete review
    public function deleteReview($id, $productId = null)
    {
        $review = $this->getReviewById($id);
        if (!empty($review)) {
            if ($this->builderReviews->where('id', $review->id)->delete()) {
                $this->updateProductRating($review->product_id);
                return true;
            }
        }
        return false;
    }

    //delete multi reviews
    public function deleteSelectedReviews($reviewIds)
    {
        if (!empty($reviewIds)) {
            foreach ($reviewIds as $id) {
                $this->deleteReview($id);
            }
        }
    }

    /*
     * --------------------------------------------------------------------
     * Comments
     * --------------------------------------------------------------------
     */

    //add comment
    public function addComment()
    {
        $data = [
            'parent_id' => inputPost('parent_id'),
            'product_id' => inputPost('product_id'),
            'user_id' => 0,
            'name' => inputPost('name'),
            'email' => inputPost('email'),
            'comment' => inputPost('comment'),
            'status' => 0,
            'ip_address' => 0,
            'created_at' => date("Y-m-d H:i:s")
        ];
        if ($this->generalSettings->comment_approval_system != 1) {
            $data['status'] = 1;
        }
        if (empty($data['parent_id'])) {
            $data['parent_id'] = 0;
        }
        if (authCheck()) {
            $data['user_id'] = user()->id;
            $data['name'] =  getUsername(user());
            $data['email'] = user()->email;
            if (hasPermission('comments')) {
                $data['status'] = 1;
            }
        } else {
            if (empty($data['name']) || empty($data['email'])) {
                return false;
            }
        }
        if (empty($data['name'])) {
            $data['name'] = '';
        }
        if (empty($data['email'])) {
            $data['email'] = '';
        }
        $ip = getIPAddress();
        if (!empty($ip)) {
            $data['ip_address'] = $ip;
        }
        $data['parent_id'] = clrNum($data['parent_id']);
        $data['product_id'] = clrNum($data['product_id']);
        if (!empty($data['product_id']) && !empty($data['comment'])) {
            $this->builderComments->insert($data);
        }
    }

    //get comment count
    public function getCommentCount($status)
    {
        return $this->builderComments->where('status', clrNum($status))->countAllResults();
    }

    //get paginated comments
    public function getCommentsPaginated($status, $perPage, $offset)
    {
        return $this->builderComments->where('status', clrNum($status))->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //latest comments
    public function getLatestComments($limit)
    {
        return $this->builderComments->orderBy('created_at DESC')->get(clrNum($limit))->getResult();
    }

    //comments
    public function getCommentsByProductId($productId, $limit)
    {
        return $this->builderComments->join('users', 'comments.user_id = users.id', 'left')
            ->select('comments.*, users.username AS user_username, users.slug AS user_slug, users.avatar AS user_avatar, users.user_type AS user_type')
            ->where('comments.product_id', clrNum($productId))->where('comments.parent_id', 0)->where('comments.status', 1)
            ->orderBy('comments.created_at DESC')->get(clrNum($limit))->getResult();
    }

    //subomments
    public function getSubComments($parentId)
    {
        return $this->builderComments->join('users', 'comments.user_id = users.id', 'left')
            ->select('comments.*, users.username AS user_username, users.slug AS user_slug, users.avatar AS user_avatar, users.user_type AS user_type')
            ->where('comments.parent_id', clrNum($parentId))->where('comments.status', 1)->orderBy('comments.created_at DESC')->get()->getResult();
    }

    //comment
    public function getComment($id)
    {
        return $this->builderComments->where('id', clrNum($id))->get()->getRow();
    }

    //product comment count
    public function getProductCommentCount($productId)
    {
        return $this->builderComments->where('product_id', clrNum($productId))->where('parent_id', 0)->where('status', 1)->countAllResults();
    }

    //get vendor comments count
    public function getVendorCommentsCount($userId)
    {
        return $this->builderComments->join('products', 'comments.product_id = products.id')->where('products.user_id', clrNum($userId))->where('products.status', 1)
            ->where('products.visibility', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)->where('comments.user_id !=', clrNum($userId))->countAllResults();
    }

    //get paginated vendor comments
    public function getVendorCommentsPaginated($userId, $perPage, $offset)
    {
        return $this->builderComments->join('products', 'comments.product_id = products.id')->select('comments.*, products.slug AS product_slug, (SELECT users.slug FROM users WHERE comments.user_id = users.id LIMIT 1) AS user_slug')
            ->where('products.user_id', clrNum($userId))->where('products.status', 1)->where('products.visibility', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)
            ->where('comments.user_id !=', clrNum($userId))->orderBy('comments.created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //approve comment
    public function approveComment($id)
    {
        $comment = $this->getComment($id);
        if (!empty($comment)) {
            return $this->builderComments->where('id', $comment->id)->update(['status' => 1]);
        }
        return false;
    }

    //approve multi comments
    public function approveMultiComments($commentIds)
    {
        if (!empty($commentIds)) {
            foreach ($commentIds as $id) {
                $this->approveComment($id);
            }
        }
    }

    //delete comment
    public function deleteComment($id)
    {
        $comment = $this->getComment($id);
        if (!empty($comment)) {
            $this->builderComments->where('parent_id', $comment->id)->delete();
            return $this->builderComments->where('id', $comment->id)->delete();
        }
        return false;
    }

    //delete multi comments
    public function deleteMultiComments($commentIds)
    {
        if (!empty($commentIds)) {
            foreach ($commentIds as $id) {
                $this->deleteComment($id);
            }
        }
    }

    /*
     * --------------------------------------------------------------------
     * Contact Messages
     * --------------------------------------------------------------------
     */

    //add contact message
    public function addContactMessage()
    {
        $data = [
            'name' => inputPost('name'),
            'email' => inputPost('email'),
            'message' => inputPost('message'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        //send email
        if ($this->generalSettings->send_email_contact_messages == 1) {
            $emailData = [
                'email_type' => 'contact',
                'email_address' => $this->generalSettings->mail_options_account,
                'email_data' => serialize(['messageName' => $data['name'], 'messageEmail' => $data['email'], 'messageText' => $data['message']]),
                'email_subject' => trans("contact_message"),
                'template_path' => 'email/contact_message'
            ];
            addToEmailQueue($emailData);
        }
        return $this->builderContact->insert($data);
    }

    //get contact messages
    public function getContactMessages()
    {
        return $this->builderContact->orderBy('id DESC')->get()->getResult();
    }

    //get contact message
    public function getContactMessage($id)
    {
        return $this->builderContact->where('id', clrNum($id))->get()->getRow();
    }

    //get lastest contact messages
    public function getLastestContactMessages()
    {
        return $this->builderContact->orderBy('id DESC')->get(5)->getResult();
    }

    //delete contact message
    public function deleteContactMessage($id)
    {
        $contact = $this->getContactMessage($id);
        if (!empty($contact)) {
            return $this->builderContact->where('id', $contact->id)->delete();
        }
        return false;
    }
}
