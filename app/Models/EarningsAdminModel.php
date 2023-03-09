<?php namespace App\Models;

use CodeIgniter\Model;

class EarningsAdminModel extends BaseModel
{
    protected $builder;
    protected $builderPayouts;
    protected $builderUsers;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('earnings');
        $this->builderPayouts = $this->db->table('payouts');
        $this->builderUsers = $this->db->table('users');
    }

    //get earnings count
    public function getEarningsCount()
    {
        $this->filterEarnings();
        return $this->builder->countAllResults();
    }

    //get paginated earnings
    public function getEarningsPaginated($perPage, $offset)
    {
        $this->filterEarnings();
        return $this->builder->orderBy('earnings.created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter earnings
    public function filterEarnings()
    {
        $q = inputGet('q');
        if (!empty($q)) {
            $q = str_replace('#', '', $q);
            $this->builder->where('earnings.order_number', $q);
        }
    }

    //get earning
    public function getEarning($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get balances count
    public function getBalancesCount()
    {
        $this->filterBalances();
        return $this->builderUsers->countAllResults();
    }

    //get paginated balances
    public function getBalancesPaginated($perPage, $offset)
    {
        $this->filterBalances();
        return $this->builderUsers->orderBy('balance DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter seller balances
    public function filterBalances()
    {
        $q = inputGet('q');
        if (!empty($q)) {
            $this->builderUsers->like('users.username', $q);
        }
    }

    //delete earning
    public function deleteEarning($id)
    {
        $earning = $this->getEarning($id);
        if (!empty($earning)) {
            return $this->builder->where('id', $earning->id)->delete();
        }
        return false;
    }

    //get payout
    public function getPayout($id)
    {
        return $this->builderPayouts->where('id', clrNum($id))->get()->getRow();
    }

    //get payout requests count
    public function getPayoutRequestsCount()
    {
        $this->filterPayouts();
        return $this->builderPayouts->countAllResults();
    }

    //get paginated payout requests
    public function getPayoutRequestsPaginated($perPage, $offset)
    {
        $this->filterPayouts();
        return $this->builderPayouts->orderBy('payouts.created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter payouts
    public function filterPayouts()
    {
        $q = inputGet('q');
        if (!empty($q)) {
            $this->builderPayouts->like('payouts.user_id', $q);
        }
        $status = inputGet('status');
        if (inputGet('status') == 'completed') {
            $this->builderPayouts->where('payouts.status', 1);
        } elseif (inputGet('status') == 'pending') {
            $this->builderPayouts->where('payouts.status', 0);
        }
    }

    //add payout
    public function addPayout($userId, $amount)
    {
        $data = [
            'user_id' => $userId,
            'payout_method' => inputPost('payout_method'),
            'amount' => $amount,
            'currency' => $this->paymentSettings->default_currency,
            'status' => inputPost('status'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        if ($data['status'] == 1) {
            if ($this->builderPayouts->insert($data)) {
                return $this->reduceUserBalance($userId, $amount);
            }
        } else {
            return $this->builderPayouts->insert($data);
        }
        return false;
    }

    //complete payout
    public function completePayout($payoutId, $userId, $amount)
    {
        if ($this->builderPayouts->where('id', clrNum($payoutId))->update(['status' => 1])) {
            return $this->reduceUserBalance($userId, $amount);
        }
        return false;
    }

    //check user balance
    public function checkUserBalance($userId, $amount)
    {
        $user = getUser($userId);
        if (!empty($user)) {
            if ($user->balance >= $amount) {
                return true;
            }
        }
        return false;
    }

    //reduce user balance
    public function reduceUserBalance($userId, $amount)
    {
        $user = getUser($userId);
        if (!empty($user)) {
            $balance = $user->balance - $amount;
            return $this->builderUsers->where('id', $user->id)->update(['balance' => $balance]);
        }
        return false;
    }

    //update user balance
    public function editSellerBalance()
    {
        $userId = inputPost('user_id');
        $user = getUser($userId);
        if (!empty($user)) {
            $data = [
                'number_of_sales' => inputPost('number_of_sales'),
                'balance' => inputPost('balance')
            ];
            $data['balance'] = getPrice($data['balance'], 'database');
            return $this->builderUsers->where('id', $user->id)->update($data);
        }
        return false;
    }

    //update payout settings
    public function updatePayoutSettings()
    {
        $submit = inputPost('submit');
        if ($submit == 'paypal') {
            $data = [
                'payout_paypal_enabled' => inputPost('payout_paypal_enabled'),
                'min_payout_paypal' => inputPost('min_payout_paypal')
            ];
            $data['min_payout_paypal'] = getPrice($data['min_payout_paypal'], 'database');
        } elseif ($submit == 'bitcoin') {
            $data = [
                'payout_bitcoin_enabled' => inputPost('payout_bitcoin_enabled'),
                'min_payout_bitcoin' => inputPost('min_payout_bitcoin')
            ];
            $data['min_payout_bitcoin'] = getPrice($data['min_payout_bitcoin'], 'database');
        } elseif ($submit == 'iban') {
            $data = [
                'payout_iban_enabled' => inputPost('payout_iban_enabled'),
                'min_payout_iban' => inputPost('min_payout_iban')
            ];
            $data['min_payout_iban'] = getPrice($data['min_payout_iban'], 'database');
        } elseif ($submit == 'swift') {
            $data = [
                'payout_swift_enabled' => inputPost('payout_swift_enabled'),
                'min_payout_swift' => inputPost('min_payout_swift')
            ];
            $data['min_payout_swift'] = getPrice($data['min_payout_swift'], 'database');
        }
        if (!empty($data)) {
            return $this->db->table('payment_settings')->where('id', 1)->update($data);
        }
        return false;
    }

    //delete payout
    public function deletePayout($id)
    {
        $payout = $this->getPayout($id);
        if (!empty($payout)) {
            return $this->builderPayouts->where('id', $payout->id)->delete();
        }
        return false;
    }
}