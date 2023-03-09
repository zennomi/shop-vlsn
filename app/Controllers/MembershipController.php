<?php

namespace App\Controllers;

use App\Models\LocationModel;
use App\Models\MembershipModel;
use App\Models\ProfileModel;

class MembershipController extends BaseAdminController
{
    protected $membershipModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        checkPermission('membership');
        $this->membershipModel = new MembershipModel();
    }

    /**
     * Users
     */
    public function users()
    {
        $data['title'] = trans("users");
        $numRows = $this->membershipModel->getUserCount();
        $pager = paginate($this->perPage, $numRows);
        $data['users'] = $this->membershipModel->getPaginatedUsers($this->perPage, $pager->offset);
        $data['roles'] = $this->membershipModel->getRoles();
        $data['membershipPlans'] = $this->membershipModel->getPlans();
        
        echo view('admin/includes/_header', $data);
        echo view('admin/membership/users');
        echo view('admin/includes/_footer');

    }

    /**
     * Add User
     */
    public function addUser()
    {
        $data['title'] = trans("add_user");
        $data['roles'] = $this->membershipModel->getRoles();

        echo view('admin/includes/_header', $data);
        echo view('admin/membership/add_user');
        echo view('admin/includes/_footer');
    }

    /**
     * Add User Post
     */
    public function addUserPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('email', trans("email"), 'required|max_length[200]');
        $val->setRule('password', trans("password"), 'required|min_length[4]|max_length[50]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $email = inputPost('email');
            $username = inputPost('username');
            //is username unique
            if (!$this->authModel->isUniqueUsername($username)) {
                setErrorMessage(trans("msg_username_unique_error"));
                return redirect()->back()->withInput();
            }
            //is email unique
            if (!$this->authModel->isEmailUnique($email)) {
                setErrorMessage(trans("msg_email_unique_error"));
                return redirect()->back()->withInput();
            }
            //add user
            if ($this->authModel->addUser()) {
                setSuccessMessage(trans("msg_administrator_added"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
            redirectToBackUrl();
        }
    }

    /**
     * Edit User
     */
    public function editUser($id)
    {
        $data['title'] = trans("edit_user");
        $data['user'] = getUser($id);
        if (empty($data['user'])) {
            return redirect()->to(adminUrl('members'));
        }
        $role = getRoleById($data['user']->role_id);
        if (empty($role)) {
            return redirect()->to(adminUrl('members'));
        }
        if ($role->is_super_admin == 1) {
            $activeUserRole = getRoleById(user()->role_id);
            if (!empty($activeUserRole) && $activeUserRole->is_super_admin != 1) {
                return redirect()->to(adminUrl('members'));
            }
        }
        $locationModel = new LocationModel();
        $data['countries'] = $locationModel->getCountries();
        $data['states'] = $locationModel->getStatesByCountry($data['user']->country_id);
        $data['cities'] = $locationModel->getCitiesByState($data['user']->state_id);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/membership/edit_user', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit User Post
     */
    public function editUserPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('email', trans("email"), 'required|max_length[200]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $data = [
                'id' => inputPost('id'),
                'username' => inputPost('username'),
                'slug' => inputPost('slug'),
                'email' => inputPost('email')
            ];
            $user = getUser($data['id']);
            if (!empty($user)) {
                $role = getRoleById($user->role_id);
                if (empty($role)) {
                    return redirect()->to(adminUrl('members'));
                }
                if ($role->is_super_admin == 1) {
                    $activeUserRole = getRoleById(user()->role_id);
                    if (!empty($activeUserRole) && $activeUserRole->is_super_admin != 1) {
                        return redirect()->to(adminUrl('members'));
                    }
                }
                //is email unique
                if (!$this->authModel->isEmailUnique($data['email'], $data['id'])) {
                    setErrorMessage(trans("msg_email_unique_error"));
                    redirectToBackUrl();
                }
                //is username unique
                if (!$this->authModel->isUniqueUsername($data['username'], $data['id'])) {
                    setErrorMessage(trans("msg_username_unique_error"));
                    redirectToBackUrl();
                }
                //is slug unique
                if (!$this->authModel->isSlugUnique($data['slug'], $data['id'])) {
                    setErrorMessage(trans("msg_slug_unique_error"));
                    redirectToBackUrl();
                }
                $profileModel = new ProfileModel();
                if ($profileModel->editUser($data['id'])) {
                    setSuccessMessage(trans("msg_updated"));
                    redirectToBackUrl();
                }
            }
        }
        setErrorMessage(trans("msg_error"));
        redirectToBackUrl();
    }

    /**
     * Shop Opening Requests
     */
    public function shopOpeningRequests()
    {
        $data['title'] = trans("shop_opening_requests");
        $numRows = $this->membershipModel->getShopOpeningRequestsCount();
        $pager = paginate($this->perPage, $numRows);
        $data['users'] = $this->membershipModel->getShopOpeningRequestsPaginated($this->perPage, $pager->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/membership/shop_opening_requests');
        echo view('admin/includes/_footer');
    }

    /**
     * Approve Shop Opening Request
     */
    public function approveShopOpeningRequest()
    {
        $userId = inputPost('id');
        if ($this->membershipModel->approveShopOpeningRequest($userId)) {
            $submit = inputPost('submit');
            $emailContent = trans("your_shop_opening_request_approved");
            $emailButtonText = trans("start_selling");
            if ($submit == 0) {
                $emailContent = trans("msg_shop_request_declined");
                $emailButtonText = trans("view_site");
            }
            //send email
            $user = getUser($userId);
            if (!empty($user) && $this->generalSettings->send_email_shop_opening_request == 1) {
                $emailData = [
                    'email_type' => 'new_shop',
                    'email_address' => $user->email,
                    'email_subject' => trans("shop_opening_request"),
                    'template_path' => 'email/main',
                    'email_data' => serialize(['content' => $emailContent, 'url' => base_url(), 'buttonText' => $emailButtonText])
                ];
                addToEmailQueue($emailData);
            }
            setSuccessMessage(trans("msg_updated"));
            redirectToBackUrl();
        }
        setErrorMessage(trans("msg_error"));
        redirectToBackUrl();
    }

    /**
     * Assign Membership Plan
     */
    public function assignMembershipPlanPost()
    {
        $userId = inputPost('user_id');
        $planId = inputPost('plan_id');
        $user = getUser($userId);
        $plan = $this->membershipModel->getPlan($planId);
        if (!empty($plan) && !empty($user)) {
            $dataTransaction = [
                'payment_method' => '',
                'payment_status' => ''
            ];
            if ($plan->is_free == 1) {
                $this->membershipModel->addUserFreePlan($plan, $user->id);
            } else {
                $this->membershipModel->addUserPlan($dataTransaction, $plan, $user->id);
            }
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Confirm User Email
     */
    public function confirmUserEmail()
    {
        $id = inputPost('id');
        $user = getUser($id);
        if ($this->authModel->verifyEmail($user)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Ban or Remove User Ban
     */
    public function banRemoveBanUser()
    {
        $id = inputPost('id');
        if ($this->authModel->banUser($id)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Delete User
     */
    public function deleteUserPost()
    {
        $id = inputPost('id');
        if ($this->authModel->deleteUser($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /*
     * --------------------------------------------------------------------
     * Membership Plans
     * --------------------------------------------------------------------
     */

    /**
     * Membership Plans
     */
    public function membershipPlans()
    {
        $data['title'] = trans("membership_plans");
        $data['membershipPlans'] = $this->membershipModel->getPlans();
        
        echo view('admin/includes/_header', $data);
        echo view('admin/membership/membership_plans');
        echo view('admin/includes/_footer');
    }

    /**
     * Add Plan Post
     */
    public function addPlanPost()
    {
        if ($this->membershipModel->addPlan()) {
            setSuccessMessage(trans("msg_added"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Edit Plan
     */
    public function editPlan($id)
    {
        $data['title'] = trans("edit_plan");
        $data['plan'] = $this->membershipModel->getPlan($id);
        if (empty($data['plan'])) {
            return redirect()->to(adminUrl('membership-plans'));
        }
        echo view('admin/includes/_header', $data);
        echo view('admin/membership/edit_plan');
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Plan Post
     */
    public function editPlanPost()
    {
        $id = inputPost('id', true);
        if ($this->membershipModel->editPlan($id)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Settings Post
     */
    public function settingsPost()
    {
        if ($this->membershipModel->updateSettings()) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Delete Plan Post
     */
    public function deletePlanPost()
    {
        $id = inputPost('id');
        $this->membershipModel->deletePlan($id);
    }

    /**
     * Membership Transactions
     */
    public function transactionsMembership()
    {
        $data['title'] = trans("membership_transactions");
        $data['numRows'] = $this->membershipModel->getMembershipTransactionsCount(null);
        $pager = paginate($this->perPage, $data['numRows']);
        $data['transactions'] = $this->membershipModel->getMembershipTransactionsPaginated(null, $this->perPage, $pager->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/membership/transactions');
        echo view('admin/includes/_footer');
    }

    /**
     * Approve Payment Post
     */
    public function approvePaymentPost()
    {
        $id = inputPost('id');
        $this->membershipModel->approveTransactionPayment($id);
        setSuccessMessage(trans("msg_updated"));
        redirectToBackUrl();
    }

    /**
     * Delete Transactions Post
     */
    public function deleteTransactionPost()
    {
        $id = inputPost('id');
        $this->membershipModel->deleteTransaction($id);
    }

    /*
     * --------------------------------------------------------------------
     * Roles & Permissions
     * --------------------------------------------------------------------
     */

    /**
     * Add Role
     */
    public function addRole()
    {
        $data['title'] = trans("add_role");
        echo view('admin/includes/_header', $data);
        echo view('admin/membership/add_role');
        echo view('admin/includes/_footer');
    }


    /**
     * Add Role Post
     */
    public function addRolePost()
    {
        if ($this->membershipModel->addRole()) {
            setSuccessMessage(trans("msg_added"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Roles Permissions
     */
    public function rolesPermissions()
    {
        $data['title'] = trans("roles_permissions");
        $data['description'] = trans("roles_permissions");
        $data['keywords'] = trans("roles_permissions");
        $data['roles'] = $this->membershipModel->getRoles();
        
        echo view('admin/includes/_header', $data);
        echo view('admin/membership/roles_permissions');
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Role
     */
    public function editRole($id)
    {
        $data['title'] = trans("edit_role");
        $data['role'] = $this->membershipModel->getRole($id);
        if (empty($data['role'])) {
            return redirect()->to(adminUrl('roles-permissions'));
        }
        echo view('admin/includes/_header', $data);
        echo view('admin/membership/edit_role');
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Role Post
     */
    public function editRolePost()
    {
        $id = inputPost('id');
        if ($this->membershipModel->editRole($id)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Change User Role Post
     */
    public function changeUserRolePost()
    {
        $userId = inputPost('user_id');
        $roleId = inputPost('role_id');
        if ($this->membershipModel->changeUserRole($userId, $roleId)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Delete Role Post
     */
    public function deleteRolePost()
    {
        $id = inputPost('id');
        $role = $this->membershipModel->getRole($id);
        if (!empty($role)) {
            if ($role->is_default == 1) {
                setErrorMessage(trans("msg_error"));
                exit();
            }
            if ($this->membershipModel->deleteRole($id)) {
                setSuccessMessage(trans("msg_deleted"));
                exit();
            }
        }
        setErrorMessage(trans("msg_error"));
    }
}
