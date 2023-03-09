<?php namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('users');
    }

    //input values
    public function inputValues()
    {
        return [
            'username' => removeSpecialCharacters(inputPost('username')),
            'email' => inputPost('email'),
            'first_name' => inputPost('first_name'),
            'last_name' => inputPost('last_name'),
            'password' => inputPost('password')
        ];
    }

    //login
    public function login()
    {
        $data = $this->inputValues();
        $user = $this->getUserByEmail($data['email']);
        if (!empty($user)) {
            if (!password_verify($data['password'], $user->password)) {
                setErrorMessage(trans("login_error"));
                return false;
            }
            if ($user->email_status != 1) {
                setErrorMessage(trans("msg_confirmed_required") . "&nbsp;<a href='javascript:void(0)' class='color-link link-underlined link-mobile-alert' onclick=\"sendActivationEmail('" . $user->token . "', 'login');\">" . trans("resend_activation_email") . "</a>");
                return false;
            }
            if ($user->banned == 1) {
                setErrorMessage(trans("msg_ban_error"));
                return false;
            }
            $this->loginUser($user);
            return true;
        }
        setErrorMessage(trans("login_error"));
        return false;
    }

    //login user
    public function loginUser($user)
    {
        if (!empty($user)) {
            $userData = [
                'mds_ses_id' => $user->id,
                'mds_ses_role_id' => $user->role_id,
                'mds_ses_pass' => md5($user->password ?? '')
            ];
            $this->session->set($userData);
        }
    }

    //login with facebook
    public function loginWithFacebook($fbUser)
    {
        if (!empty($fbUser)) {
            $user = $this->getUserByEmail($fbUser->email);
            if (empty($user)) {
                if (empty($fbUser->name)) {
                    $fbUser->name = 'user-' . uniqid();
                }
                $username = $this->generateUniqueUsername($fbUser->name);
                $slug = $this->generateUniqueSlug($username);
                $data = [
                    'facebook_id' => $fbUser->id,
                    'email' => $fbUser->email,
                    'email_status' => 1,
                    'token' => generateToken(),
                    'role_id' => 3,
                    'username' => $username,
                    'first_name' => $fbUser->firstName,
                    'last_name' => $fbUser->lastName,
                    'slug' => $slug,
                    'avatar' => '',
                    'user_type' => 'facebook',
                    'last_seen' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                if ($this->generalSettings->vendor_verification_system != 1) {
                    $data['role_id'] = 2;
                }
                if (!empty($data['email'])) {
                    $this->builder->insert($data);
                    $user = $this->getUserByEmail($fbUser->email);
                    if (!empty($user)) {
                        $this->downloadSocialProfileImage($user, $fbUser->pictureURL);
                    }
                }
            }
        }
        if (!empty($user)) {
            if ($user->banned == 1) {
                setErrorMessage(trans("msg_ban_error"));
                return false;
            }
            $this->loginUser($user);
        }
    }

    //login with google
    public function loginWithGoogle($gUser)
    {
        if (!empty($gUser)) {
            $user = $this->getUserByEmail($gUser->email);
            if (empty($user)) {
                if (empty($gUser->name)) {
                    $gUser->name = 'user-' . uniqid();
                }
                $username = $this->generateUniqueUsername($gUser->name);
                $slug = $this->generateUniqueSlug($username);
                $data = [
                    'google_id' => $gUser->id,
                    'email' => $gUser->email,
                    'email_status' => 1,
                    'token' => generateToken(),
                    'role_id' => 3,
                    'username' => $username,
                    'first_name' => $gUser->firstName,
                    'last_name' => $gUser->lastName,
                    'slug' => $slug,
                    'avatar' => '',
                    'user_type' => 'google',
                    'last_seen' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                if ($this->generalSettings->vendor_verification_system != 1) {
                    $data['role_id'] = 2;
                }
                if (!empty($data['email'])) {
                    $this->builder->insert($data);
                    $user = $this->getUserByEmail($gUser->email);
                    if (!empty($user)) {
                        $this->downloadSocialProfileImage($user, $gUser->avatar);
                    }
                }
            }
        }
        if (!empty($user)) {
            if ($user->banned == 1) {
                setErrorMessage(trans("msg_ban_error"));
                return false;
            }
            $this->loginUser($user);
        }
    }

    //login with vk
    public function loginWithVK($vkUser)
    {
        if (!empty($vkUser)) {
            $user = $this->getUserByEmail($vkUser->email);
            if (empty($user)) {
                if (empty($vkUser->name)) {
                    $vkUser->name = 'user-' . uniqid();
                }
                $username = $this->generateUniqueUsername($vkUser->name);
                $slug = $this->generateUniqueSlug($username);
                $data = [
                    'vkontakte_id' => $vkUser->id,
                    'email' => $vkUser->email,
                    'email_status' => 1,
                    'token' => generateToken(),
                    'role_id' => 3,
                    'username' => $username,
                    'first_name' => $vkUser->firstName,
                    'last_name' => $vkUser->lastName,
                    'slug' => $slug,
                    'avatar' => '',
                    'user_type' => 'vkontakte',
                    'last_seen' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                if ($this->generalSettings->vendor_verification_system != 1) {
                    $data['role_id'] = 2;
                }
                if (!empty($data['email'])) {
                    $this->builder->insert($data);
                    $user = $this->getUserByEmail($vkUser->email);
                    if (!empty($user)) {
                        $this->downloadSocialProfileImage($user, $vkUser->avatar);
                    }
                }
            }
        }
        if (!empty($user)) {
            if ($user->banned == 1) {
                setErrorMessage(trans("msg_ban_error"));
                return false;
            }
            $this->loginUser($user);
        }
    }

    //download social profile image
    public function downloadSocialProfileImage($user, $imgURL)
    {
        if (!empty($user) && !empty($imgURL)) {
            $uploadModel = new UploadModel();
            $tempPath = $uploadModel->downloadTempImage($imgURL, 'jpg', 'profile_temp');
            if (!empty($tempPath) && file_exists($tempPath)) {
                $data['avatar'] = $uploadModel->uploadAvatar($tempPath);
            }
            if (!empty($data) && !empty($data['avatar'])) {
                $this->builder->where('id', $user->id)->update($data);
            }
        }
    }

    //register
    public function register()
    {
        $data = $this->inputValues();
        $data['username'] = $data['first_name'] . ' ' . $data['last_name'];
        $data['slug'] = $this->generateUniqueSlug($data['username']);
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['role_id'] = 3;
        $data['user_type'] = 'registered';
        $data['banned'] = 0;
        $data['token'] = generateToken();
        $data['last_seen'] = date('Y-m-d H:i:s');
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['email_status'] = 1;
        if ($this->generalSettings->email_verification == 1) {
            $data['email_status'] = 0;
        }
        if ($this->generalSettings->vendor_verification_system != 1) {
            $data['role_id'] = 2;
        }
        $user = null;
        if ($this->builder->insert($data)) {
            $id = $this->db->insertID();
            $this->updateSlug($id);
            $user = $this->getUser($id);
            if (!empty($user)) {
                if ($this->generalSettings->email_verification == 1) {
                    $this->addActivationEmail($user);
                    redirectToUrl(generateUrl('register_success') . '?u=' . $user->token);
                } else {
                    $this->loginUser($user);
                }
            }
            return true;
        }
        return false;
    }

    //generate unique username
    public function generateUniqueUsername($username)
    {
        $newUsername = $username;
        if (!empty($this->getUserByUsername($newUsername))) {
            $newUsername = $username . ' 1';
            if (!empty($this->getUserByUsername($newUsername))) {
                $newUsername = $username . ' 2';
                if (!empty($this->getUserByUsername($newUsername))) {
                    $newUsername = $username . ' 3';
                    if (!empty($this->getUserByUsername($newUsername))) {
                        $newUsername = $username . '-' . uniqid();
                    }
                }
            }
        }
        return $newUsername;
    }

    //generate uniqe slug
    public function generateUniqueSlug($username)
    {
        $slug = strSlug($username);
        if (!empty($this->getUserBySlug($slug))) {
            $slug = strSlug($username . '-1');
            if (!empty($this->getUserBySlug($slug))) {
                $slug = strSlug($username . '-2');
                if (!empty($this->getUserBySlug($slug))) {
                    $slug = strSlug($username . '-3');
                    if (!empty($this->getUserBySlug($slug))) {
                        $slug = strSlug($username . '-' . uniqid());
                    }
                }
            }
        }
        return $slug;
    }

    //add user
    public function addUser()
    {
        $data = $this->inputValues();
        $data['username'] = $this->generateUniqueUsername($data['first_name'] . ' ' . $data['last_name']);
        $data['slug'] = $this->generateUniqueSlug($data['username']);
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['role_id'] = inputPost('role_id');
        $data['user_type'] = 'registered';
        $data['banned'] = 0;
        $data['email_status'] = 1;
        $data['token'] = generateToken();
        $data['last_seen'] = date('Y-m-d H:i:s');
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->builder->insert($data);
    }

    //logout
    public function logout()
    {
        $this->session->remove('mds_ses_id');
        $this->session->remove('mds_ses_role_id');
        $this->session->remove('mds_ses_pass');
    }

    //reset password
    public function resetPassword($user)
    {
        if (!empty($user)) {
            $data = [
                'password' => password_hash(inputPost('password'), PASSWORD_DEFAULT),
                'token' => generateToken()
            ];
            return $this->builder->where('id', $user->id)->update($data);
        }
        return false;
    }

    //update last seen time
    public function updateLastSeen()
    {
        if (authCheck()) {
            $this->builder->where('id', user()->id)->update(['last_seen' => date('Y-m-d H:i:s')]);
        }
    }

    //query user
    public function buildQueryUser()
    {
        $this->builder->resetQuery();
        $this->builder->select('users.*, (SELECT permissions FROM roles_permissions WHERE roles_permissions.id = users.role_id LIMIT 1) AS permissions');
    }

    //get user by id
    public function getUser($id)
    {
        $this->buildQueryUser();
        return $this->builder->where('users.id', clrNum($id))->get()->getRow();
    }

    //get user by email
    public function getUserByEmail($email)
    {
        $this->buildQueryUser();
        return $this->builder->where('users.email', removeSpecialCharacters($email))->get()->getRow();
    }

    //get user by username
    public function getUserByUsername($username)
    {
        $this->buildQueryUser();
        return $this->builder->where('users.username', removeSpecialCharacters($username))->get()->getRow();
    }

    //get user by slug
    public function getUserBySlug($slug)
    {
        $this->buildQueryUser();
        return $this->builder->where('users.slug', removeSpecialCharacters($slug))->get()->getRow();
    }

    //get user by token
    public function getUserByToken($token)
    {
        $this->buildQueryUser();
        return $this->builder->where('users.token', removeSpecialCharacters($token))->get()->getRow();
    }

    //get users
    public function getUsers()
    {
        return $this->builder->orderBy('id')->get()->getResult();
    }

    //get users count
    public function getUsersCount()
    {
        return $this->builder->countAllResults();
    }

    //get users count by role
    public function getVendorsCount()
    {
        $this->filterVendors();
        return $this->builder->countAllResults();
    }

    //get paginated vendors
    public function getVendorsPaginated($perPage, $offset)
    {
        $this->filterVendors();
        return $this->builder->orderBy('num_products DESC, created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter vendor
    public function filterVendors()
    {
        $q = removeSpecialCharacters(inputGet('q'));
        $this->builder->select('users.*, (SELECT COUNT(id) FROM products WHERE users.id = products.user_id AND products.status = 1 AND products.visibility = 1 AND products.is_draft = 0 AND products.is_deleted = 0) AS num_products');
        if ($this->generalSettings->vendor_verification_system == 1) {
            $this->builder->where('(SELECT COUNT(id) FROM products WHERE users.id = products.user_id AND products.status = 1 AND products.visibility = 1 AND products.is_draft = 0 AND products.is_deleted = 0) > 0');
        }
        $this->builder->groupStart()->where('banned', 0)->groupEnd();
        if (!empty($q)) {
            $this->builder->groupStart()->like('users.username', cleanStr($q))->groupEnd();
        }
    }

    //get latest users
    public function getLatestUsers($limit)
    {
        return $this->builder->orderBy('id DESC')->get(clrNum($limit))->getResult();
    }

    //update slug
    public function updateSlug($id)
    {
        $user = $this->getUser($id);
        if (empty($user->slug) || $user->slug == '-') {
            $this->builder->where('id', $user->id)->update(['slug' => 'user-' . $user->id]);
        } else {
            if (!$this->isSlugUnique($user->slug, $id) == true) {
                $this->builder->where('id', $user->id)->update([$user->slug . '-' . $user->id]);
            }
        }
    }

    //is slug unique
    public function isSlugUnique($slug, $id)
    {
        if (!empty($this->builder->where('id !=', clrNum($id))->where('slug', cleanStr($slug))->get()->getRow())) {
            return false;
        }
        return true;
    }

    //check if email is unique
    public function isEmailUnique($email, $userId = 0)
    {
        $user = $this->getUserByEmail($email);
        if ($userId == 0) {
            if (!empty($user)) {
                return false;
            }
            return true;
        } else {
            if (!empty($user) && $user->id != $userId) {
                return false;
            }
            return true;
        }
    }

    //check if username is unique
    public function isUniqueUsername($username, $userId = 0)
    {
        $user = $this->getUserByUsername($username);
        if ($userId == 0) {
            if (!empty($user)) {
                return false;
            }
            return true;
        } else {
            if (!empty($user) && $user->id != $userId) {
                return false;
            }
            return true;
        }
    }

    //update user token
    public function updateUserToken($id, $token)
    {
        return $this->builder->where('id', clrNum($id))->update(['token' => $token]);
    }

    //verify email
    public function verifyEmail($user)
    {
        if (!empty($user)) {
            $data = [
                'email_status' => 1,
                'token' => generateToken()
            ];
            return $this->builder->where('id', $user->id)->update($data);
        }
        return false;
    }

    //ban user
    public function banUser($id)
    {
        $user = $this->getUser($id);
        if (!empty($user)) {
            if ($user->banned == 1) {
                $data = ['banned' => 0];
            } else {
                $data = ['banned' => 1];
            }
            return $this->builder->where('id', $user->id)->update($data);
        }
        return false;
    }

    //delete cover image
    public function deleteCoverImage()
    {
        if (authCheck()) {
            return $this->builder->where('id', user()->id)->update(['cover_image' => '']);
        }
    }

    //delete user
    public function deleteUser($id)
    {
        $user = $this->getUser($id);
        if (!empty($user)) {
            deleteFile($user->avatar);
            if (!empty($user)) {
                //delete products
                $productAdminModel= new ProductAdminModel();
                $products = $this->db->table('products')->where('user_id', $user->id)->get()->getResult();
                if (!empty($products)) {
                    foreach ($products as $product) {
                        $productAdminModel->deleteProductPermanently($product->id);
                    }
                }
                return $this->builder->where('id', $user->id)->delete();
            }
        }
        return false;
    }

    //add activation email
    public function addActivationEmail($user, $email = null)
    {
        if ($this->generalSettings->email_verification == 1 && !empty($user)) {
            if (empty($email)) {
                $email = $user->email;
            }
            $token = $user->token;
            if (empty($token)) {
                $token = generateToken();
                $this->updateUserToken($user->id, $token);
            }
            $emailData = [
                'email_type' => 'activation',
                'email_address' => $email,
                'email_data' => serialize([
                    'content' => trans("msg_confirmation_email"),
                    'url' => base_url() . '/confirm-account?token=' . $token,
                    'buttonText' => trans("confirm_your_account")
                ]),
                'email_priority' => 1,
                'email_subject' => trans("confirm_your_account"),
                'template_path' => 'email/main'
            ];
            addToEmailQueue($emailData);
        }
    }
}
