<?php namespace App\Models;

use CodeIgniter\Model;

class ProfileModel extends BaseModel
{
    protected $builder;
    protected $builderShippingAddresses;
    protected $builderFollowers;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('users');
        $this->builderShippingAddresses = $this->db->table('shipping_addresses');
        $this->builderFollowers = $this->db->table('followers');
    }

    //update profile
    public function editProfile($data)
    {
        $uploadModel = new UploadModel();
        $file = $uploadModel->uploadTempFile('file', true);
        if (!empty($file) && !empty($file['path'])) {
            $data['avatar'] = $uploadModel->uploadAvatar($file['path']);
            deleteFile(FCPATH . user()->avatar);
            $uploadModel->deleteTempFile($file['path']);
        }
        $imageCover = $uploadModel->uploadTempFile('image_cover', true);
        if (!empty($imageCover) && !empty($imageCover['path'])) {
            $data['cover_image'] = $uploadModel->uploadCoverImage($imageCover['path']);
            deleteFile(FCPATH . user()->cover_image);
            $uploadModel->deleteTempFile($imageCover['path']);
        }
        if (empty($data['show_email'])) {
            $data['show_email'] = 0;
        }
        if (empty($data['show_phone'])) {
            $data['show_phone'] = 0;
        }
        if (empty($data['show_location'])) {
            $data['show_location'] = 0;
        }
        if ($this->generalSettings->email_verification == 1) {
            if (user()->email != $data['email']) {
                $data['email_status'] = 0;
                $authModel = new AuthModel();
                $authModel->addActivationEmail(user(), $data['email']);
            }
        }
        return $this->builder->where('id', user()->id)->update($data);
    }

    //edit user
    public function editUser($id)
    {
        $user = getUser($id);
        if (!empty($user)) {
            $data = [
                'username' => inputPost('username'),
                'email' => inputPost('email'),
                'slug' => inputPost('slug'),
                'first_name' => inputPost('first_name'),
                'last_name' => inputPost('last_name'),
                'phone_number' => inputPost('phone_number'),
                'about_me' => inputPost('about_me'),
                'country_id' => inputPost('country_id'),
                'state_id' => inputPost('state_id'),
                'city_id' => inputPost('city_id'),
                'address' => inputPost('address'),
                'zip_code' => inputPost('zip_code'),
                'personal_website_url' => inputPost('personal_website_url'),
                'facebook_url' => inputPost('facebook_url'),
                'twitter_url' => inputPost('twitter_url'),
                'instagram_url' => inputPost('instagram_url'),
                'pinterest_url' => inputPost('pinterest_url'),
                'linkedin_url' => inputPost('linkedin_url'),
                'vk_url' => inputPost('vk_url'),
                'whatsapp_url' => inputPost('whatsapp_url'),
                'telegram_url' => inputPost('telegram_url'),
                'youtube_url' => inputPost('youtube_url')
            ];
            $uploadModel = new UploadModel();
            $tempFile = $uploadModel->uploadTempFile('file');
            if (!empty($tempFile) && !empty($tempFile['path'])) {
                $data['avatar'] = $uploadModel->uploadAvatar($tempFile['path']);
                $uploadModel->deleteTempFile($tempFile['path']);
                deleteFile($user->avatar);
            }
            return $this->builder->where('id', $user->id)->update($data);
        }
    }

    //update shop settings
    public function updateShopSettings($shopName)
    {
        $data = [
            'username' => $shopName,
            'about_me' => inputPost('about_me'),
            'show_rss_feeds' => inputPost('show_rss_feeds'),
            'country_id' => inputPost('country_id'),
            'state_id' => inputPost('state_id'),
            'city_id' => inputPost('city_id'),
            'address' => inputPost('address'),
            'zip_code' => inputPost('zip_code')
        ];
        $data['country_id'] = !empty($data['country_id']) ? $data['country_id'] : 0;
        $data['state_id'] = !empty($data['state_id']) ? $data['state_id'] : 0;
        $data['city_id'] = !empty($data['city_id']) ? $data['city_id'] : 0;
        $data['address'] = !empty($data['address']) ? $data['address'] : '';
        $data['zip_code'] = !empty($data['zip_code']) ? $data['zip_code'] : '';
        return $this->builder->where('id', user()->id)->update($data);
    }

    //update cash on delivery
    public function updateCashOnDelivery()
    {
        if (authCheck()) {
            $status = 0;
            if (inputPost('cash_on_delivery') == 1) {
                $status = 1;
            }
            return $this->builder->where('id', user()->id)->update(['cash_on_delivery' => $status]);
        }
        return false;
    }

    //shipping address input values
    public function shippingAddressInputValues()
    {
        return [
            'title' => inputPost('title'),
            'first_name' => inputPost('first_name'),
            'last_name' => inputPost('last_name'),
            'email' => inputPost('email'),
            'phone_number' => inputPost('phone_number'),
            'address' => inputPost('address'),
            'country_id' => inputPost('country_id'),
            'state_id' => inputPost('state_id'),
            'city' => inputPost('city'),
            'zip_code' => inputPost('zip_code')
        ];
    }

    //add shipping address
    public function addShippingAddress()
    {
        $data = $this->shippingAddressInputValues();
        $data['user_id'] = user()->id;
        return $this->builderShippingAddresses->insert($data);
    }

    //edit shipping address
    public function editShippingAddress()
    {
        $id = inputPost('id');
        $row = $this->getShippingAddressById($id, user()->id);
        if (!empty($row) && user()->id == $row->user_id) {
            $data = $this->shippingAddressInputValues();
            return $this->builderShippingAddresses->where('id', $row->id)->update($data);
        }
        return false;
    }

    //get shipping address
    public function getShippingAddressById($addressId, $userId)
    {
        return $this->builderShippingAddresses->where('id', clrNum($addressId))->where('user_id', clrNum($userId))->get()->getRow();
    }

    //delete shipping address
    public function deleteShippingAddress()
    {
        $id = inputPost('id');
        $row = $this->getShippingAddressById($id, user()->id);
        if (!empty($row) && user()->id == $row->user_id) {
            return $this->builderShippingAddresses->where('id', $row->id)->delete();
        }
        return false;
    }

    //update update social media
    public function updateSocialMedia()
    {
        $data = [
            'personal_website_url' => inputPost('personal_website_url'),
            'facebook_url' => inputPost('facebook_url'),
            'twitter_url' => inputPost('twitter_url'),
            'instagram_url' => inputPost('instagram_url'),
            'pinterest_url' => inputPost('pinterest_url'),
            'linkedin_url' => inputPost('linkedin_url'),
            'vk_url' => inputPost('vk_url'),
            'whatsapp_url' => inputPost('whatsapp_url'),
            'telegram_url' => inputPost('telegram_url'),
            'youtube_url' => inputPost('youtube_url')
        ];
        foreach ($data as $key => $value) {
            if (!empty(trim($value))) {
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    return false;
                }
            }
        }
        return $this->builder->where('id', user()->id)->update($data);
    }

    //change password
    public function changePassword()
    {
        $data = [
            'old_password' => inputPost('old_password'),
            'password' => inputPost('password'),
            'password_confirm' => inputPost('password_confirm')
        ];
        if (!empty(user()->password)) {
            if (!password_verify($data['old_password'], user()->password)) {
                setErrorMessage(trans("msg_wrong_old_password"));
                redirectToUrl(generateUrl('settings', 'change_password'));
            }
        }
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        if ($this->builder->where('id', user()->id)->update(['password' => $password])) {
            $user = getUser(user()->id);
            $authModel = new AuthModel();
            $authModel->loginUser($user);
            return true;
        }
        return false;
    }

    //follow user
    public function followUnfollowUser()
    {
        $data = [
            'following_id' => inputPost('user_id'),
            'follower_id' => user()->id
        ];
        $follow = $this->getFollow($data['following_id'], $data['follower_id']);
        if (empty($follow)) {
            $this->builderFollowers->insert($data);
        } else {
            $this->builderFollowers->where('id', $follow->id)->delete();
        }
    }

    //get shipping addresses
    public function getShippingAddresses()
    {
        return $this->builderShippingAddresses->where('user_id', user()->id)->get()->getResult();
    }

    //follow
    public function getFollow($followingId, $followerId)
    {
        return $this->builderFollowers->where('following_id', clrNum($followingId))->where('follower_id', clrNum($followerId))->get()->getRow();
    }

    //is user follows
    public function isUserFollows($followingId, $followerId)
    {
        if (empty($this->getFollow($followingId, $followerId))) {
            return false;
        }
        return true;
    }

    //get followers
    public function getFollowers($followingId)
    {
        return $this->builderFollowers->join('users', 'followers.follower_id = users.id')->select('users.*')->where('following_id', clrNum($followingId))->get()->getResult();
    }

    //get followers count
    public function getFollowersCount($followingId)
    {
        return $this->builderFollowers->join('users', 'followers.follower_id = users.id')->select('users.*')->where('following_id', clrNum($followingId))->countAllResults();
    }

    //get followed users
    public function getFollowedUsers($followerId)
    {
        return $this->builderFollowers->join('users', 'followers.following_id = users.id')->select('users.*')->where('follower_id', clrNum($followerId))->get()->getResult();
    }

    //get following users
    public function getFollowingUsersCount($followerId)
    {
        return $this->builderFollowers->join('users', 'followers.following_id = users.id')->select('users.*')->where('follower_id', clrNum($followerId))->countAllResults();
    }
}
