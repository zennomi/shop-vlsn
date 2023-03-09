<?php namespace App\Models;

use CodeIgniter\Model;

class MembershipModel extends BaseModel
{
    protected $builderUsers;
    protected $builderRoles;
    protected $builderMembershipPlans;
    protected $builderUsersMembershipPlans;
    protected $builderMembershipTransactions;

    public function __construct()
    {
        parent::__construct();
        $this->builderUsers = $this->db->table('users');
        $this->builderRoles = $this->db->table('roles_permissions');
        $this->builderMembershipPlans = $this->db->table('membership_plans');
        $this->builderUsersMembershipPlans = $this->db->table('users_membership_plans');
        $this->builderMembershipTransactions = $this->db->table('membership_transactions');
    }

    //get users count
    public function getUserCount()
    {
        $this->filterUsers();
        return $this->builderUsers->countAllResults();
    }

    //get paginated users
    public function getPaginatedUsers($perPage, $offset)
    {
        $this->filterUsers();
        return $this->builderUsers->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //users filter
    public function filterUsers()
    {
        $q = inputGet('q');
        if (!empty($q)) {
            $this->builderUsers->groupStart()->like('users.username', cleanStr($q))->orLike('users.first_name', cleanStr($q))->orLike('users.last_name', cleanStr($q))->orLike('email', cleanStr($q))->groupEnd();
        }
        $roleId = clrNum(inputGet('role'));
        if (!empty($roleId)) {
            $this->builderUsers->where('users.role_id', $roleId);
        }
        $status = inputGet('status');
        if (!empty($status)) {
            $banned = $status == 'banned' ? 1 : 0;
            $this->builderUsers->where('users.banned', $banned);
        }
        $emailStatus = inputGet('email_status');
        if (!empty($emailStatus)) {
            $status = $emailStatus == 'confirmed' ? 1 : 0;
            $this->builderUsers->where('users.email_status', $status);
        }
    }

    //add membership transaction
    public function addMembershipTransaction($dataTransaction, $plan)
    {
        $data = [
            'payment_method' => $dataTransaction['payment_method'],
            'payment_id' => $dataTransaction['payment_id'],
            'user_id' => user()->id,
            'plan_id' => $plan->id,
            'plan_title' => $this->getMembershipPlanTitle($plan),
            'payment_amount' => $dataTransaction['payment_amount'],
            'currency' => $dataTransaction['currency'],
            'payment_status' => $dataTransaction['payment_status'],
            'ip_address' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $ip = getIPAddress();
        if (!empty($ip)) {
            $data['ip_address'] = $ip;
        }
        if ($this->builderMembershipTransactions->insert($data)) {
            helperSetSession('mds_membership_transaction_insert_id', $this->db->insertID());
            if (!isVendor()) {
                $this->builderUsers->where('id', user()->id)->update(['is_active_shop_request' => 1]);
            }
        }
    }

    //add membership transaction bank
    public function addMembershipTransactionBank($dataTransaction, $plan)
    {
        $price = getPrice($plan->price, 'decimal');
        $price = convertCurrencyByExchangeRate($price, getSelectedCurrency()->exchange_rate);
        $data = [
            'payment_method' => $dataTransaction['payment_method'],
            'payment_id' => $dataTransaction['payment_id'],
            'user_id' => user()->id,
            'plan_id' => $plan->id,
            'plan_title' => $this->getMembershipPlanTitle($plan),
            'payment_amount' => $price,
            'currency' => getSelectedCurrency()->code,
            'payment_status' => $dataTransaction['payment_status'],
            'ip_address' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $ip = getIPAddress();
        if (!empty($ip)) {
            $data['ip_address'] = $ip;
        }
        if ($this->builderMembershipTransactions->insert($data)) {
            helperSetSession('mds_membership_transaction_insert_id', $this->db->insertID());
            if (!isVendor()) {
                $this->builderUsers->where('id', user()->id)->update(['is_active_shop_request' => 1]);
            }
        }
    }

    //add shop opening email
    public function addShopOpeningEmail()
    {
        if ($this->generalSettings->send_email_shop_opening_request == 1) {
            $emailData = [
                'email_type' => 'new_shop',
                'email_address' => $this->generalSettings->mail_options_account,
                'email_subject' => trans("shop_opening_request"),
                'template_path' => 'email/main',
                'email_data' => serialize([
                    'content' => trans("there_is_shop_opening_request") . '<br>' . trans("user") . ': ' . '<strong>' . getUsername(user()) . '</strong>',
                    'url' => adminUrl('shop-opening-requests'),
                    'buttonText' => trans("view_details")
                ])
            ];
            addToEmailQueue($emailData);
        }
    }

    //get membership transaction
    public function getMembershipTransaction($id)
    {
        return $this->builderMembershipTransactions->where('id', clrNum($id))->get()->getRow();
    }

    //get membership plan title
    public function getMembershipPlanTitle($plan)
    {
        $title = trans("membership_plan");
        if (!empty($plan)) {
            $title = getMembershipPlanName($plan->title_array, selectedLangId());
            $title .= ' (';
            if ($plan->is_unlimited_number_of_ads == 1) {
                $title .= trans("number_of_ads") . ': ' . trans("unlimited");
            } else {
                $title .= trans("number_of_ads") . ': ' . $plan->number_of_ads;
            }
            if ($plan->is_unlimited_time == 1) {
                $title .= ', ' . trans("number_of_days") . ': ' . trans("unlimited");
            } else {
                $title .= ', ' . trans("number_of_days") . ': ' . $plan->number_of_days;
            }
            $title .= ')';
        }
        return $title;
    }

    //add user plan
    public function addUserPlan($dataTransaction, $plan, $userId)
    {
        $oldPlan = $this->builderUsersMembershipPlans->where('user_id', clrNum($userId))->get()->getRow();
        if (!empty($oldPlan)) {
            $this->builderUsersMembershipPlans->where('user_id', clrNum($userId))->delete();
        }
        $data = [
            'plan_id' => $plan->id,
            'plan_title' => $this->getMembershipPlanTitle($plan),
            'number_of_ads' => $plan->number_of_ads,
            'number_of_days' => $plan->number_of_days,
            'price' => $plan->price,
            'currency' => $this->paymentSettings->default_currency,
            'is_free' => $plan->is_free,
            'is_unlimited_number_of_ads' => $plan->is_unlimited_number_of_ads,
            'is_unlimited_time' => $plan->is_unlimited_time,
            'payment_method' => $dataTransaction['payment_method'],
            'payment_status' => $dataTransaction['payment_status'],
            'plan_status' => 1,
            'plan_start_date' => date('Y-m-d H:i:s')
        ];
        if ($plan->is_unlimited_time == 1) {
            $data['plan_end_date'] = '';
        } else {
            $data['plan_end_date'] = strtotime($data['plan_start_date'] . '+ ' . $plan->number_of_days . ' days');
            $data['plan_end_date'] = date('Y-m-d H:i:s', $data['plan_end_date']);
        }
        if ($dataTransaction["payment_status"] == 'awaiting_payment') {
            $data['plan_status'] = 0;
        }

        $data['user_id'] = clrNum($userId);
        $this->builderUsersMembershipPlans->insert($data);

        //update user plan status
        $this->builderUsers->where('id', clrNum($userId))->update(['is_membership_plan_expired' => 0]);
    }

    //add user free plan
    public function addUserFreePlan($plan, $userId)
    {
        $oldPlan = $this->builderUsersMembershipPlans->where('user_id', clrNum($userId))->get()->getRow();
        if (!empty($oldPlan)) {
            $this->builderUsersMembershipPlans->where('user_id', clrNum($userId))->delete();
        }
        $data = [
            'plan_id' => $plan->id,
            'plan_title' => $this->getMembershipPlanTitle($plan),
            'number_of_ads' => $plan->number_of_ads,
            'number_of_days' => $plan->number_of_days,
            'price' => 0,
            'currency' => $this->paymentSettings->default_currency,
            'is_free' => $plan->is_free,
            'is_unlimited_number_of_ads' => $plan->is_unlimited_number_of_ads,
            'is_unlimited_time' => $plan->is_unlimited_time,
            'payment_method' => '',
            'payment_status' => '',
            'plan_status' => 1,
            'plan_start_date' => date('Y-m-d H:i:s')
        ];
        if ($plan->is_unlimited_time == 1) {
            $data['plan_end_date'] = '';
        } else {
            $data['plan_end_date'] = strtotime($data['plan_start_date'] . '+ ' . $plan->number_of_days . ' days');
            $data['plan_end_date'] = date('Y-m-d H:i:s', $data['plan_end_date']);
        }

        $data['user_id'] = clrNum($userId);
        $this->builderUsersMembershipPlans->insert($data);

        //update user plan status
        $this->builderUsers->where('id', clrNum($userId))->update(['is_membership_plan_expired' => 0, 'is_used_free_plan' => 1]);
    }

    //get user plan by user id
    public function getUserPlanByUserId($userId, $onlyActive = true)
    {
        if ($onlyActive) {
            $this->builderUsersMembershipPlans->where('plan_status', 1);
        }
        return $this->builderUsersMembershipPlans->where('user_id', clrNum($userId))->get()->getRow();
    }

    //get user plan days remaining
    public function getUserPlanRemainingDaysCount($plan)
    {
        $daysLeft = 0;
        if (!empty($plan)) {
            if (!empty($plan->plan_end_date)) {
                $daysLeft = dateDifference($plan->plan_end_date, date('Y-m-d H:i:s'));
            }
        }
        return $daysLeft;
    }

    //get user plan ads remaining
    public function getUserPlanRemainingAdsCount($plan)
    {
        $adsLeft = 0;
        if (!empty($plan)) {
            $productsCount = $this->getUserAdsCount($plan->user_id);
            $adsLeft = @($plan->number_of_ads - $productsCount);
            if (empty($adsLeft) || $adsLeft < 0) {
                $adsLeft = 0;
            }
        }
        return $adsLeft;
    }

    //get user ads count
    public function getUserAdsCount($userId)
    {
        return $this->db->table('products')->where('products.is_deleted', 0)->where('products.is_draft', 0)->where('products.user_id', clrNum($userId))->countAllResults();
    }

    //is allowed adding product
    public function isAllowedAddingProduct()
    {
        if (isSuperAdmin()) {
            return true;
        }
        if ($this->generalSettings->membership_plans_system == 1) {
            if (user()->is_membership_plan_expired == 1) {
                return false;
            }
            $userPlan = $this->getUserPlanByUserId(user()->id);
            if (!empty($userPlan)) {
                if ($userPlan->is_unlimited_number_of_ads == 1) {
                    return true;
                }
                if ($this->getUserPlanRemainingAdsCount($userPlan) > 0) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    //check membership plans expired
    public function checkMembershipPlansExpired()
    {
        $plans = $this->builderUsersMembershipPlans->join('users', 'users_membership_plans.user_id = users.id AND users.is_membership_plan_expired = 0')->select('users_membership_plans.*')->get()->getResult();
        if (!empty($plans)) {
            foreach ($plans as $plan) {
                if ($plan->is_unlimited_time != 1) {
                    if ($this->getUserPlanRemainingDaysCount($plan) <= -3) {
                        //update user plan status
                        $this->builderUsers->where('id', $plan->user_id)->update(['is_membership_plan_expired' => 1]);
                    }
                }
            }
        }
    }

    /*
     * --------------------------------------------------------------------
     * Back-end
     * --------------------------------------------------------------------
     */

    //prepare data
    public function preparePlanData()
    {
        $data = [
            'number_of_ads' => inputPost('number_of_ads'),
            'number_of_days' => inputPost('number_of_days'),
            'price' => inputPost('price'),
            'is_free' => inputPost('is_free'),
            'is_unlimited_number_of_ads' => inputPost('is_unlimited_number_of_ads'),
            'is_unlimited_time' => inputPost('is_unlimited_time'),
            'plan_order' => inputPost('plan_order'),
            'is_popular' => inputPost('is_popular')
        ];
        $arrayTitle = array();
        $arrayFeatures = array();
        foreach ($this->activeLanguages as $language) {
            //add titles
            $item = [
                'lang_id' => $language->id,
                'title' => inputPost('title_' . $language->id)
            ];
            array_push($arrayTitle, $item);
            //add features
            $features = inputPost('feature_' . $language->id);
            $array = array();
            if (!empty($features)) {
                foreach ($features as $feature) {
                    $feature = trim($feature ?? '');
                    if (!empty($feature)) {
                        array_push($array, $feature);
                    }
                }
            }
            $itemFeature = [
                'lang_id' => $language->id,
                'features' => $array
            ];
            array_push($arrayFeatures, $itemFeature);
        }
        $data['price'] = getPrice($data['price'], 'database');
        if (empty($data['price'])) {
            $data['price'] = 0;
        }
        $data['title_array'] = serialize($arrayTitle);
        $data['features_array'] = serialize($arrayFeatures);
        if (empty($data['number_of_ads'])) {
            $data['number_of_ads'] = 0;
        }
        if (empty($data['number_of_days'])) {
            $data['number_of_days'] = 0;
        }
        if (!empty($data['is_unlimited_number_of_ads'])) {
            $data['number_of_ads'] = 0;
        } else {
            $data['is_unlimited_number_of_ads'] = 0;
        }
        if (!empty($data['is_unlimited_time'])) {
            $data['number_of_days'] = 0;
        } else {
            $data['is_unlimited_time'] = 0;
        }
        if (!empty($data['is_free'])) {
            $data['price'] = 0;
        } else {
            $data['is_free'] = 0;
        }
        //update other plans
        if (!empty($data['is_popular'])) {
            $this->builderMembershipPlans->update(['is_popular' => 0]);
        } else {
            $data['is_popular'] = 0;
        }
        return $data;
    }

    //add plan
    public function addPlan()
    {
        $data = $this->preparePlanData();
        return $this->builderMembershipPlans->insert($data);
    }

    //edit plan
    public function editPlan($id)
    {
        $plan = $this->getPlan($id);
        if (!empty($plan)) {
            $data = $this->preparePlanData();
            return $this->builderMembershipPlans->where('id', $plan->id)->update($data);
        }
        return false;
    }

    //get plan
    public function getPlan($id)
    {
        return $this->builderMembershipPlans->where('id', clrNum($id))->get()->getRow();
    }

    //get plans
    public function getPlans()
    {
        return $this->builderMembershipPlans->orderBy('plan_order')->get()->getResult();
    }

    //update settings
    public function updateSettings()
    {
        $data = [
            'membership_plans_system' => inputPost('membership_plans_system')
        ];
        return $this->db->table('general_settings')->where('id', 1)->update($data);
    }

    //get membership transactions count
    public function getMembershipTransactionsCount($userId)
    {
        $this->filterTransactions($userId);
        return $this->builderMembershipTransactions->countAllResults();
    }

    //get paginated membership transactions
    public function getMembershipTransactionsPaginated($userId, $perPage, $offset)
    {
        $this->filterTransactions($userId);
        return $this->builderMembershipTransactions->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter membership transactions
    public function filterTransactions($userId)
    {
        $this->builderMembershipTransactions->join('users', 'users.id = membership_transactions.user_id')->select('membership_transactions.*');
        if (!empty($userId)) {
            $this->builderMembershipTransactions->where('user_id', clrNum($userId));
        }
        $q = inputGet('q');
        if (!empty($q)) {
            $this->builderMembershipTransactions->groupStart()->like('users.username', $q)->orLike('membership_transactions.plan_title', $q)->orLike('membership_transactions.payment_method', $q)->orLike('membership_transactions.payment_id', $q)
                ->orLike('membership_transactions.payment_amount', $q)->orLike('membership_transactions.currency', $q)->orLike('membership_transactions.payment_status', $q)->orLike('membership_transactions.ip_address', $q)->groupEnd();
        }
    }

    //approve payment
    public function approveTransactionPayment($id)
    {
        $transaction = $this->getMembershipTransaction($id);
        if (!empty($transaction)) {
            $data = [
                'payment_status' => 'payment_received'
            ];
            $this->builderMembershipTransactions->where('id', $transaction->id)->update($data);
            //update user plan
            $userPlan = $this->builderUsersMembershipPlans->where('user_id', $transaction->user_id)->get()->getRow();
            if (!empty($userPlan)) {
                $data = [
                    'payment_status' => 'payment_received',
                    'plan_status' => 1,
                    'plan_start_date' => date('Y-m-d H:i:s')
                ];
                if ($userPlan->is_unlimited_time == 1) {
                    $data['plan_end_date'] = '';
                } else {
                    $data['plan_end_date'] = strtotime($data['plan_start_date'] . '+ ' . $userPlan->number_of_days . ' days');
                    $data['plan_end_date'] = date('Y-m-d H:i:s', $data['plan_end_date']);
                }
                $this->builderUsersMembershipPlans->where('id', $userPlan->id)->update($data);
            }
            return true;
        }
        return false;
    }

    //delete transaction
    public function deleteTransaction($id)
    {
        $transaction = $this->getMembershipTransaction($id);
        if (!empty($transaction)) {
            return $this->builderMembershipTransactions->where('id', $transaction->id)->delete();
        }
        return false;
    }

    //delete plan
    public function deletePlan($id)
    {
        $plan = $this->getPlan($id);
        if (!empty($plan)) {
            return $this->builderMembershipPlans->where('id', $plan->id)->delete();
        }
        return false;
    }

    /*
     * --------------------------------------------------------------------
     * Shop Opening Requests
     * --------------------------------------------------------------------
     */

    //get shop opening requests count
    public function getShopOpeningRequestsCount()
    {
        return $this->builderUsers->where('is_active_shop_request', 1)->countAllResults();
    }

    //get paginated users
    public function getShopOpeningRequestsPaginated($perPage, $offset)
    {
        return $this->builderUsers->where('is_active_shop_request', 1)->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //add shop opening request
    public function addShopOpeningRequest($data)
    {
        if (empty($data['country_id'])) {
            $data['country_id'] = 0;
        }
        if (empty($data['state_id'])) {
            $data['state_id'] = 0;
        }
        if (empty($data['city_id'])) {
            $data['city_id'] = 0;
        }
        return $this->builderUsers->where('id', user()->id)->update($data);
    }

    //approve shop opening request
    public function approveShopOpeningRequest($userId)
    {
        //approve request
        if (inputPost('submit') == 1) {
            $dataShop = [
                'role_id' => 2,
                'is_active_shop_request' => 0,
            ];
            //update user plan
            $userPlan = $this->getUserPlanByUserId($userId);
            if (!empty($userPlan)) {
                $data = [
                    'payment_status' => 'payment_received',
                    'plan_status' => 1,
                    'plan_start_date' => date('Y-m-d H:i:s')
                ];
                if ($userPlan->is_unlimited_time == 1) {
                    $data['plan_end_date'] = '';
                } else {
                    $data['plan_end_date'] = strtotime($data['plan_start_date'] . '+ ' . $userPlan->number_of_days . ' days');
                    $data['plan_end_date'] = date('Y-m-d H:i:s', $data['plan_end_date']);
                }
                $this->builderUsersMembershipPlans->where('id', $userPlan->id)->update($data);
            }
        } else {
            //decline request
            $dataShop = [
                'is_active_shop_request' => 2,
            ];
        }
        return $this->builderUsers->where('id', clrNum($userId))->update($dataShop);
    }

    /*
     * --------------------------------------------------------------------
     * Roles & Permissions
     * --------------------------------------------------------------------
     */

    //add role
    public function addRole()
    {
        $nameArray = array();
        $permissionsArray = array();
        foreach ($this->activeLanguages as $language) {
            $item = [
                'lang_id' => $language->id,
                'name' => inputPost('role_name_' . $language->id, true)
            ];
            array_push($nameArray, $item);
        }
        $permissions = inputPost('permissions');
        $pushAdminPanel = false;
        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                array_push($permissionsArray, $permission);
                if ($permission != 2) {
                    $pushAdminPanel = true;
                }
            }
        }
        if ($pushAdminPanel && !in_array(1, $permissions)) {
            array_push($permissionsArray, 1);
        }
        $permissionsStr = implode(',', $permissionsArray);
        $data = [
            'role_name' => serialize($nameArray),
            'permissions' => $permissionsStr,
            'is_default' => 0,
            'is_super_admin' => 0,
            'is_admin' => 0,
            'is_vendor' => 0,
            'is_member' => 0
        ];
        if (!empty($permissions) && in_array(1, $permissions)) {
            $data['is_admin'] = 1;
        }
        if (!empty($permissions) && in_array(2, $permissions)) {
            $data['is_vendor'] = 1;
        }
        if (empty($permissions)) {
            $data['is_member'] = 1;
        }
        return $this->builderRoles->insert($data);
    }

    //edit role
    public function editRole($id)
    {
        $role = $this->getRole($id);
        if (!empty($role)) {
            $nameArray = array();
            $permissionsArray = array();
            foreach ($this->activeLanguages as $language) {
                $item = [
                    'lang_id' => $language->id,
                    'name' => inputPost('role_name_' . $language->id)
                ];
                array_push($nameArray, $item);
            }

            $data = ['role_name' => serialize($nameArray)];
            if ($role->is_default != 1) {
                $permissions = inputPost('permissions');
                $pushAdminPanel = false;
                if (!empty($permissions)) {
                    foreach ($permissions as $permission) {
                        array_push($permissionsArray, $permission);
                        if ($permission != 2) {
                            $pushAdminPanel = true;
                        }
                    }
                }
                if ($pushAdminPanel && !in_array(1, $permissions)) {
                    array_push($permissionsArray, 1);
                }
                $permissionsStr = implode(',', $permissionsArray);
                $data['permissions'] = $permissionsStr;
                $data['is_admin'] = 0;
                $data['is_vendor'] = 0;
                if (!empty($permissions) && in_array(1, $permissions)) {
                    $data['is_admin'] = 1;
                    $data['is_member'] = 0;
                }
                if (!empty($permissions) && in_array(2, $permissions)) {
                    $data['is_vendor'] = 1;
                    $data['is_member'] = 0;
                }
                if (empty($permissions)) {
                    $data['is_member'] = 1;
                }
            }
            return $this->builderRoles->where('id', $role->id)->update($data);
        }
        return false;
    }

    //get role
    public function getRole($id)
    {
        return $this->builderRoles->where('id', clrNum($id))->get()->getRow();
    }

    //get roles
    public function getRoles()
    {
        return $this->builderRoles->orderBy('id')->get()->getResult();
    }

    //change user role
    public function changeUserRole($userId, $roleId)
    {
        $user = getUser($userId);
        if (!empty($user)) {
            $role = $this->getRole($roleId);
            if (!empty($role)) {
                return $this->builderUsers->where('id', $user->id)->update(['role_id' => $role->id]);
            }
        }
        return false;
    }

    //delete role
    public function deleteRole($id)
    {
        $role = $this->getRole($id);
        if (!empty($role)) {
            $users = $this->builderUsers->where('role_id', $role->id)->get()->getResult();
            if (!empty($users)) {
                foreach ($users as $user) {
                    $this->builderUsers->where('id', $user->id)->update(['role_id' => 3]);
                }
            }
            return $this->builderRoles->where('id', $role->id)->delete();
        }
        return false;
    }
}
