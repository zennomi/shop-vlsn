<?php

namespace App\Controllers;

use App\Models\ProfileModel;

class ProfileController extends BaseController
{
    protected $profileModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->profileModel = new ProfileModel();
    }

    /**
     * Profile
     */
    public function profile($slug)
    {
        $data['user'] = $this->authModel->getUserBySlug($slug);
        if (empty($data['user'])) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = getUsername($data['user']);
        $data['description'] = getUsername($data['user']) . ' - ' . $this->baseVars->appName;
        $data['keywords'] = getUsername($data['user']) . ',' . $this->baseVars->appName;
        $data['showOgTags'] = true;
        $data['ogTitle'] = $data['title'];
        $data['og_Description'] = $data['description'];
        $data['ogType'] = 'article';
        $data['ogUrl'] = generateProfileUrl($data['user']->slug);
        $data['ogImage'] = getUserAvatar($data['user']);
        $data['ogWidth'] = '200';
        $data['ogHeight'] = '200';
        $data['ogCreator'] = $data['title'];
        $data['activeTab'] = 'products';
        $data['userRating'] = calculateUserRating($data['user']->id);
        $data['queryStringArray'] = getQueryStringArray(null);
        $data['queryStringObjectArray'] = convertQueryStringToObjectArray($data['queryStringArray']);
        $data['category'] = null;
        $data['parentCategory'] = null;
        $categoryId = inputGet('p_cat');
        if (!empty($categoryId)) {
            $data['category'] = $this->categoryModel->getCategory($categoryId);
            if (!empty($data['category']) && $data['category']->parent_id != 0) {
                $data['parentCategory'] = $this->categoryModel->getCategory($data['category']->parent_id);
            }
        }
        $data['categories'] = $this->categoryModel->getVendorCategories($data['category'], $data['user']->id, true, true);
        
        $data['numRows'] = $this->productModel->getProfileProductsCount($data['user']->id, $data['category']);
        $pager = paginate($this->baseVars->perPageProducts, $data['numRows']);
        $data['products'] = $this->productModel->getProfileProductsPaginated($data['user']->id, $data['category'], $this->baseVars->perPageProducts, $pager->offset);

        echo view('partials/_header', $data);
        echo view('profile/profile', $data);
        echo view('partials/_footer');
    }

    /**
     * Wishlist
     */
    public function wishlist($slug)
    {
        $data['user'] = $this->authModel->getUserBySlug($slug);
        if (empty($data['user'])) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("wishlist");
        $data['description'] = trans("wishlist") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("wishlist") . ',' . $this->baseVars->appName;
        $data["activeTab"] = 'wishlist';
        $data['userRating'] = calculateUserRating($data['user']->id);
        
        $data['numRows'] = $this->productModel->getUserWishlistProductsCount($data['user']->id);
        $pager = paginate($this->baseVars->perPageProducts, $data['numRows']);
        $data['products'] = $this->productModel->getPaginatedUserWishlistProducts($data['user']->id, $this->baseVars->perPageProducts, $pager->offset);

        echo view('partials/_header', $data);
        echo view('profile/wishlist', $data);
        echo view('partials/_footer');
    }

    /**
     * Downloads
     */
    public function downloads()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        if (!isSaleActive()) {
            return redirect()->to(langBaseUrl());
        }
        if ($this->generalSettings->digital_products_system != 1) {
            return redirect()->to(langBaseUrl());
        }
        $data['user'] = user();
        $data['title'] = trans("downloads");
        $data['description'] = trans("downloads") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("downloads") . ',' . $this->baseVars->appName;
        $data['activeTab'] = 'downloads';
        $data['userRating'] = calculateUserRating($data['user']->id);
        
        $data['numRows'] = $this->productModel->getUserDownloadsCount($data['user']->id);
        $pager = paginate($this->baseVars->perPage, $data['numRows']);
        $data['items'] = $this->productModel->getUserDownloadsPaginated($data['user']->id, $this->baseVars->perPage, $pager->offset);

        echo view('partials/_header', $data);
        echo view('profile/downloads', $data);
        echo view('partials/_footer');
    }

    /**
     * Followers
     */
    public function followers($slug)
    {
        $data['user'] = $this->authModel->getUserBySlug($slug);
        if (empty($data['user'])) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("followers");
        $data['description'] = trans("followers") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("followers") . ',' . $this->baseVars->appName;
        $data['activeTab'] = 'followers';
        $data['userRating'] = calculateUserRating($data['user']->id);
        $data['followers'] = $this->profileModel->getFollowers($data['user']->id);

        echo view('partials/_header', $data);
        echo view('profile/followers', $data);
        echo view('partials/_footer');
    }

    /**
     * Following
     */
    public function following($slug)
    {
        $data['user'] = $this->authModel->getUserBySlug($slug);
        if (empty($data['user'])) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("following");
        $data['description'] = trans("following") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("following") . ',' . $this->baseVars->appName;
        $data['activeTab'] = "following";
        $data['userRating'] = calculateUserRating($data['user']->id);
        $data['followers'] = $this->profileModel->getFollowedUsers($data['user']->id);

        echo view('partials/_header', $data);
        echo view('profile/followers', $data);
        echo view('partials/_footer');
    }

    /**
     * Reviews
     */
    public function reviews($slug)
    {
        if ($this->generalSettings->reviews != 1) {
            return redirect()->to(langBaseUrl());
        }
        $data['user'] = $this->authModel->getUserBySlug($slug);
        if (empty($data['user']) || !isVendor($data['user'])) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = getUsername($data['user']) . ' ' . trans("reviews");
        $data['description'] = getUsername($data['user']) . ' ' . trans("reviews") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = getUsername($data['user']) . ' ' . trans("reviews") . ',' . $this->baseVars->appName;
        $data["activeTab"] = 'reviews';
        $data['userRating'] = calculateUserRating($data['user']->id);
        
        $numRows = $this->commonModel->getVendorReviewsCount($data['user']->id);
        $pager = paginate($this->baseVars->perPage, $numRows);
        $data['reviews'] = $this->commonModel->getVendorReviewsPaginated($data['user']->id, $this->baseVars->perPage, $pager->offset);

        echo view('partials/_header', $data);
        echo view('profile/reviews', $data);
        echo view('partials/_footer');
    }

    /**
     * Update Profile
     */
    public function editProfile()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("update_profile");
        $data['description'] = trans("update_profile") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("update_profile") . ',' . $this->baseVars->appName;
        $data["activeTab"] = 'edit_profile';
        
        echo view('partials/_header', $data);
        echo view('settings/edit_profile', $data);
        echo view('partials/_footer');
    }

    /**
     * Update Profile Post
     */
    public function editProfilePost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $action = inputPost('submit');
        $val = \Config\Services::validation();
        $val->setRule('email', trans("email"), 'required|max_length[255]');
        $val->setRule('slug', trans("slug"), 'required|max_length[255]');
        $val->setRule('first_name', trans("first_name"), 'required|max_length[255]');
        $val->setRule('last_name', trans("last_name"), 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $data = [
                'slug' => strSlug(inputPost('slug')),
                'email' => inputPost('email'),
                'first_name' => inputPost('first_name'),
                'last_name' => inputPost('last_name'),
                'phone_number' => inputPost('phone_number'),
                'send_email_new_message' => inputPost('send_email_new_message'),
                'cover_image_type' => inputPost('cover_image_type'),
                'show_email' => inputPost('show_email'),
                'show_phone' => inputPost('show_phone'),
                'show_location' => inputPost('show_location')
            ];
            //is email unique
            if (!$this->authModel->isEmailUnique($data['email'], user()->id)) {
                setErrorMessage(trans("msg_email_unique_error"));
                return redirect()->to(generateUrl('settings', 'edit_profile'));
            }
            //is slug unique
            if (!$this->authModel->isSlugUnique($data['slug'], user()->id)) {
                setErrorMessage(trans("msg_slug_unique_error"));
                return redirect()->to(generateUrl('settings', 'edit_profile'));
            }
            if ($this->profileModel->editProfile($data, user()->id)) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
            return redirect()->to(generateUrl('settings', 'edit_profile'));
        }
    }

    //delete cover image
    public function deleteCoverImagePost()
    {
        $this->authModel->deleteCoverImage();
    }

    /**
     * Shipping Address
     */
    public function shippingAddress()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("shipping_address");
        $data['description'] = trans("shipping_address") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("shipping_address") . ',' . $this->baseVars->appName;
        $data["activeTab"] = 'shipping_address';
        $data['shippingAddresses'] = $this->profileModel->getShippingAddresses();
        $data['states'] = $this->locationModel->getStatesByCountry(1);
        
        echo view('partials/_header', $data);
        echo view('settings/shipping_address', $data);
        echo view('partials/_footer');
    }

    /**
     * Add Shipping Address Post
     */
    public function addShippingAddressPost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        if (!$this->profileModel->addShippingAddress()) {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Edit Shipping Address Post
     */
    public function editShippingAddressPost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        if ($this->profileModel->editShippingAddress()) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Delete Shipping Address Post
     */
    public function deleteShippingAddressPost()
    {
        if (!authCheck()) {
            exit();
        }
        if ($this->profileModel->deleteShippingAddress()) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Social Media
     */
    public function socialMedia()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("social_media");
        $data['description'] = trans("social_media") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("social_media") . ',' . $this->baseVars->appName;
        $data['activeTab'] = 'social_media';
        
        echo view('partials/_header', $data);
        echo view('settings/social_media', $data);
        echo view('partials/_footer');
    }

    /**
     * Social Media Post
     */
    public function socialMediaPost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        if ($this->profileModel->updateSocialMedia()) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        return redirect()->to(generateUrl('settings', 'social_media'));
    }

    /**
     * Change Password
     */
    public function changePassword()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("change_password");
        $data['description'] = trans("change_password") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("change_password") . ',' . $this->baseVars->appName;
        $data['activeTab'] = 'change_password';
        
        echo view('partials/_header', $data);
        echo view('settings/change_password', $data);
        echo view('partials/_footer');
    }

    /**
     * Change Password Post
     */
    public function changePasswordPost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $val = \Config\Services::validation();
        if (!empty(user()->password)) {
            $val->setRule('old_password', trans("old_password"), 'required|max_length[255]');
        }
        $val->setRule('password', trans("password"), 'required|min_length[4]|max_length[100]');
        $val->setRule('password_confirm', trans("password_confirm"), 'required|matches[password]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            if ($this->profileModel->changePassword()) {
                setSuccessMessage(trans("msg_change_password_success"));
            } else {
                setErrorMessage(trans("msg_change_password_error"));
            }
        }
        return redirect()->to(generateUrl('settings', 'change_password'));
    }

    /**
     * Follow Unfollow User
     */
    public function followUnfollowUser()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $this->profileModel->followUnfollowUser();
        redirectToBackUrl();
    }
}