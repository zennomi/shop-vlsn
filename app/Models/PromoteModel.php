<?php namespace App\Models;

use CodeIgniter\Model;

class PromoteModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('promoted_transactions');
    }

    //add promote transaction
    public function addPromoteTransaction($dataTransaction)
    {
        $promotedPlan = helperGetSession('modesy_selected_promoted_plan');
        $data = [
            'payment_method' => $dataTransaction['payment_method'],
            'payment_id' => $dataTransaction['payment_id'],
            'user_id' => user()->id,
            'product_id' => $promotedPlan->product_id,
            'currency' => $dataTransaction['currency'],
            'payment_amount' => $dataTransaction['payment_amount'],
            'payment_status' => $dataTransaction['payment_status'],
            'purchased_plan' => $promotedPlan->purchased_plan,
            'day_count' => $promotedPlan->day_count,
            'ip_address' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $ip = getIPAddress();
        if (!empty($ip)) {
            $data['ip_address'] = $ip;
        }
        $this->builder->insert($data);
        helperSetSession('mds_promoted_transaction_insert_id', $this->db->insertID());
    }

    //add promote transaction bank
    public function addPromoteTransactionBank($promotedPlan, $transactionNumber)
    {
        $price = convertCurrencyByExchangeRate($promotedPlan->total_amount, $this->selectedCurrency->exchange_rate);
        $data = [
            'payment_method' => 'Bank Transfer',
            'payment_id' => $transactionNumber,
            'user_id' => user()->id,
            'product_id' => $promotedPlan->product_id,
            'currency' => $this->selectedCurrency->code,
            'payment_amount' => $price,
            'payment_status' => 'awaiting_payment',
            'purchased_plan' => $promotedPlan->purchased_plan,
            'day_count' => $promotedPlan->day_count,
            'ip_address' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $ip = getIPAddress();
        if (!empty($ip)) {
            $data['ip_address'] = $ip;
        }
        $this->builder->insert($data);
        helperSetSession('mds_promoted_transaction_insert_id', $this->db->insertID());
    }

    //add to promoted products
    public function addToPromotedProducts($promotedPlan)
    {
        $product = getProduct($promotedPlan->product_id);
        if (!empty($product)) {
            $date = date('Y-m-d H:i:s');
            $endDate = date('Y-m-d H:i:s', strtotime($date . ' + ' . $promotedPlan->day_count . ' days'));
            $data = [
                'promote_plan' => $promotedPlan->purchased_plan,
                'promote_day' => $promotedPlan->day_count,
                'is_promoted' => 1,
                'promote_start_date' => $date,
                'promote_end_date' => $endDate
            ];
            return $this->db->table('products')->where('id', $promotedPlan->product_id)->update($data);
        }
        return false;
    }

    //get transactions count
    public function getTransactionsCount($userId)
    {
        $this->filterTransactions($userId);
        return $this->builder->countAllResults();
    }

    //get transactions paginated
    public function getTransactionsPaginated($userId, $perPage, $offset)
    {
        $this->filterTransactions($userId);
        return $this->builder->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter transactions
    public function filterTransactions($userId)
    {
        $q = inputGet('q');
        if (!empty($q)) {
            $this->builder->where('promoted_transactions.payment_id', $q);
        }
        if (!empty($userId)) {
            $this->builder->where('user_id', clrNum($userId));
        }
    }

    //get transaction
    public function getTransaction($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //delete transaction
    public function deleteTransaction($id)
    {
        $transaction = $this->getTransaction($id);
        if (!empty($transaction)) {
            return $this->builder->where('id', $transaction->id)->delete();
        }
        return false;
    }

}
