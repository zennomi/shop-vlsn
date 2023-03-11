<?php

namespace App\Controllers;

use App\Models\CurrencyModel;
use App\Models\EarningsAdminModel;
use App\Models\EarningsModel;

class EarningsController extends BaseAdminController
{
    protected $earningsModel;
    protected $earningsAdminModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->earningsModel = new EarningsModel();
        $this->earningsAdminModel = new EarningsAdminModel();
    }

    /**
     * Earnings
     */
    public function earnings()
    {
        checkPermission('earnings');
        $data['title'] = trans("earnings");
        $numRows = $this->earningsAdminModel->getEarningsCount();
        $pager = paginate($this->perPage, $numRows);
        $data['earnings'] = $this->earningsAdminModel->getEarningsPaginated($this->perPage, $pager->offset);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/earnings/earnings', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Delete Earnings Post
     */
    public function deleteEarningPost()
    {
        checkPermission('earnings');
        $id = inputPost('id');
        if ($this->earningsAdminModel->deleteEarning($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Seller Balances
     */
    public function sellerBalances()
    {
        checkPermission('earnings');
        $data['title'] = trans("seller_balances");
        $numRows = $this->earningsAdminModel->getBalancesCount();
        $pager = paginate($this->perPage, $numRows);
        $data['balances'] = $this->earningsAdminModel->getBalancesPaginated($this->perPage, $pager->offset);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/earnings/seller_balances', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Seller Balance Post
     */
    public function editSellerBalancePost()
    {
        checkPermission('earnings');
        if ($this->earningsAdminModel->editSellerBalance()) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Payout Requests
     */
    public function payoutRequests()
    {
        checkPermission('payouts');
        $data['title'] = trans("payout_requests");
        $numRows = $this->earningsAdminModel->getPayoutRequestsCount();
        $pager = paginate($this->perPage, $numRows);
        $data['payoutRequests'] = $this->earningsAdminModel->getPayoutRequestsPaginated($this->perPage, $pager->offset);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/earnings/payout_requests', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Payout
     */
    public function addPayout()
    {
        checkPermission('payouts');
        $data['title'] = trans("add_payout");
        $data['users'] = $this->authModel->getUsers();
        $model = new CurrencyModel();
        $data['currencies'] = $model->getCurrencies();

        echo view('admin/includes/_header', $data);
        echo view('admin/earnings/add_payout', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Payout Post
     */
    public function addPayoutPost()
    {
        checkPermission('payouts');
        $userId = inputPost('user_id');
        $amount = inputPost('amount');
        $amount = getPrice($amount, 'database');
        if (!$this->earningsAdminModel->checkUserBalance($userId, $amount)) {
            setErrorMessage(trans("msg_insufficient_balance"));
        } else {
            if ($this->earningsAdminModel->addPayout($userId, $amount)) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        redirectToBackUrl();
    }

    /**
     * Payout Settings
     */
    public function payoutSettings()
    {
        checkPermission('payouts');
        $data['title'] = trans("payout_settings");
        
        echo view('admin/includes/_header', $data);
        echo view('admin/earnings/payout_settings', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Payout Paypal Settings Post
     */
    public function payoutSettingsPost()
    {
        checkPermission('payouts');
        if ($this->earningsAdminModel->updatePayoutSettings()) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Complete Payout Request Post
     */
    public function completePayoutRequestPost()
    {
        checkPermission('payouts');
        $payoutId = inputPost('payout_id');
        $userId = inputPost('user_id');
        $amount = inputPost('amount');
        if (!$this->earningsAdminModel->checkUserBalance($userId, $amount)) {
            setErrorMessage(trans("msg_insufficient_balance"));
        } else {
            if ($this->earningsAdminModel->completePayout($payoutId, $userId, $amount)) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        redirectToBackUrl();
    }

    /**
     * Delete Payout Post
     */
    public function deletePayoutPost()
    {
        checkPermission('payouts');
        $id = inputPost('id');
        if ($this->earningsAdminModel->deletePayout($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }
}