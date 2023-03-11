<?php namespace App\Models;

use CodeIgniter\Model;

class EarningsModel extends BaseModel
{
    protected $builder;
    protected $builderPayouts;
    protected $builderUsersPayoutAccounts;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('earnings');
        $this->builderPayouts = $this->db->table('payouts');
        $this->builderUsersPayoutAccounts = $this->db->table('users_payout_accounts');
    }

    //get earnings count
    public function getEarningsCount($userId)
    {
        $this->filterEarnings();
        return $this->builder->where('user_id', clrNum($userId))->countAllResults();
    }

    //get paginated earnings
    public function getEarningsPaginated($userId, $perPage, $offset)
    {
        $this->filterEarnings();
        return $this->builder->where('user_id', clrNum($userId))->orderBy('earnings.created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter earnings
    public function filterEarnings()
    {
        $q = inputGet('q');
        if (!empty($q)) {
            $this->builder->like('earnings.order_number', $q);
        }
    }

    //add seller earnings
    public function addSellerEarnings($orderProduct)
    {
        if (!empty($orderProduct)) {
            $order = getOrder($orderProduct->order_id);
            if (!empty($order)) {
                //check if earning already added
                $row = $this->builder->where('order_number', $order->order_number)->where('order_product_id', $orderProduct->id)->where('user_id', $orderProduct->seller_id)->get()->getRow();
                if ($row < 1) {
                    $saleAmount = getPrice($orderProduct->product_total_price, 'decimal');
                    $vat = 0;
                    if (!empty($orderProduct->product_vat)) {
                        $vat = getPrice($orderProduct->product_vat, 'decimal');
                    }
                    $saleAmount = $saleAmount - $vat;
                    //calculate earned amount
                    $earnedAmount = $saleAmount;
                    $couponDiscount = 0;
                    $productIds = getCouponProductsArray($order);
                    if (!empty($productIds) && in_array($orderProduct->product_id, $productIds)) {
                        if ($order->coupon_discount_rate > 0 && $order->coupon_seller_id == $orderProduct->seller_id) {
                            $couponDiscount = ($saleAmount * $order->coupon_discount_rate) / 100;
                            $earnedAmount = $earnedAmount - $couponDiscount;
                        }
                    }
                    $commission = ($saleAmount * $orderProduct->commission_rate) / 100;
                    $earnedAmount = $earnedAmount - $commission;
                    $shippingCost = $this->getSingleProductShippingCost($orderProduct->order_id, $orderProduct->seller_id);
                    if (!empty($shippingCost)) {
                        $earnedAmount = $earnedAmount + $shippingCost;
                    }
                    $earnedAmount = $earnedAmount + $vat;
                    //add earning
                    $data = [
                        'order_number' => $order->order_number,
                        'order_product_id' => $orderProduct->id,
                        'user_id' => $orderProduct->seller_id,
                        'sale_amount' => getPrice($saleAmount, 'database'),
                        'vat_rate' => $orderProduct->product_vat_rate,
                        'vat_amount' => $orderProduct->product_vat,
                        'commission_rate' => $orderProduct->commission_rate,
                        'commission' => getPrice($commission, 'database'),
                        'coupon_discount' => getPrice($couponDiscount, 'database'),
                        'shipping_cost' => getPrice($shippingCost, 'database'),
                        'earned_amount' => getPrice($earnedAmount, 'database'),
                        'currency' => $orderProduct->product_currency,
                        'exchange_rate' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $totalEarned = $data['earned_amount'];
                    $productCurrency = getCurrencyByCode($orderProduct->product_currency);
                    if (!empty($productCurrency) && $this->paymentSettings->currency_converter == 1 && $productCurrency->exchange_rate > 0) {
                        $data['exchange_rate'] = $productCurrency->exchange_rate;
                        $totalEarned = getPrice($totalEarned, 'decimal');
                        $totalEarned = $totalEarned / $data['exchange_rate'];
                        $totalEarned = number_format($totalEarned, 2, '.', '');
                        $totalEarned = getPrice($totalEarned, 'database');
                    }
                    $this->builder->insert($data);
                    //update seller balance and number of sales
                    $user = getUser($orderProduct->seller_id);
                    if (!empty($user)) {
                        $newBalance = $user->balance;
                        if ($order->payment_method != 'Cash On Delivery') {
                            $newBalance = $user->balance + $totalEarned;
                        }
                        $sales = $user->number_of_sales;
                        $sales = $sales + 1;
                        $data = [
                            'balance' => $newBalance,
                            'number_of_sales' => $sales
                        ];
                        $this->db->table('users')->where('id', $user->id)->update($data);
                    }
                }
            }
        }
    }

    //refund product
    public function refundProduct($orderProduct)
    {
        if (!empty($orderProduct)) {
            $order = getOrder($orderProduct->order_id);
            $earning = $this->getEarningByOrderProductId($orderProduct->id, $order->order_number);
            if (!empty($order) && !empty($earning) && $order->payment_method != 'Cash On Delivery') {
                //edit vendor balance
                $user = getUser($orderProduct->seller_id);
                if (!empty($user)) {
                    $this->db->table('users')->where('id', $user->id)->update(['balance' => $user->balance - $earning->earned_amount]);
                }
                //edit order product
                $this->db->table('order_products')->where('id', $orderProduct->id)->update(['order_status' => 'refund_approved', 'updated_at' => date('Y-m-d H:i:s')]);
                //edit refund request
                $this->db->table('refund_requests')->where('order_product_id', $orderProduct->id)->update(['is_completed' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                //update earning
                $this->builder->where('id', $earning->id)->update(['is_refunded' => 1]);
                //update order date
                $this->db->table('orders')->where('id', $orderProduct->order_id)->update(['updated_at' => date('Y-m-d H:i:s')]);
            } else {
                //edit order product
                $this->db->table('order_products')->where('id', $orderProduct->id)->update(['order_status' => 'refund_approved', 'updated_at' => date('Y-m-d H:i:s')]);
                //edit refund request
                $this->db->table('refund_requests')->where('order_product_id', $orderProduct->id)->update(['is_completed' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                //update order date
                $this->db->table('orders')->where('id', $orderProduct->order_id)->update(['updated_at' => date('Y-m-d H:i:s')]);
            }
            //delete if digital product
            if ($orderProduct->product_type == 'digital') {
                $digitalSale = $this->db->table('digital_sales')->where('order_id', $orderProduct->order_id)->where('product_id', $orderProduct->product_id)->where('buyer_id', $orderProduct->buyer_id)->get()->getRow();
                if (!empty($digitalSale)) {
                    $this->db->table('digital_sales')->where('id', $digitalSale->id)->delete();
                }
            }
        }
    }

    //get single product shipping cost
    public function getSingleProductShippingCost($orderId, $sellerId)
    {
        $numProducts = 0;
        $sellerShippingCost = 0;
        $orderModel = new OrderModel();
        $orderProducts = $orderModel->getOrderProducts($orderId);
        if (!empty($orderProducts)) {
            foreach ($orderProducts as $product) {
                if ($product->seller_id == $sellerId) {
                    $numProducts += 1;
                    $sellerShippingCost = $product->seller_shipping_cost;
                }
            }
        }
        if (!empty($numProducts)) {
            $cost = ($sellerShippingCost / 100) / $numProducts;
            if (!empty($cost)) {
                return number_format($cost, 2, '.', '');
            }
        }
        return 0;
    }

    //get earning by order product
    public function getEarningByOrderProductId($orderProductId, $orderNumber)
    {
        return $this->builder->where('order_number', $orderNumber)->where('order_product_id', clrNum($orderProductId))->get()->getRow();
    }

    //get user payout account
    public function getUserPayoutAccount($userId)
    {
        $row = $this->builderUsersPayoutAccounts->where('user_id', clrNum($userId))->get()->getRow();
        if (!empty($row)) {
            return $row;
        }
        $data = [
            'user_id' => clrNum($userId),
            'payout_paypal_email' => '',
            'iban_full_name' => '',
            'iban_country_id' => '',
            'iban_bank_name' => '',
            'iban_number' => '',
            'swift_full_name' => '',
            'swift_address' => '',
            'swift_state' => '',
            'swift_city' => '',
            'swift_postcode' => '',
            'swift_country_id' => '',
            'swift_bank_account_holder_name' => '',
            'swift_iban' => '',
            'swift_code' => '',
            'swift_bank_name' => '',
            'swift_bank_branch_city' => '',
            'swift_bank_branch_country_id' => ''
        ];
        $this->builderUsersPayoutAccounts->insert($data);
        return $this->builderUsersPayoutAccounts->where('user_id', clrNum($userId))->get()->getRow();
    }

    //set paypal payout account
    public function setPayoutAccount($userId, $submit)
    {
        if ($submit == 'paypal') {
            $data = ['payout_paypal_email' => inputPost('payout_paypal_email')];
        } elseif ($submit == 'bitcoin') {
            $data = ['payout_bitcoin_address' => inputPost('payout_bitcoin_address')];
        } elseif ($submit == 'bitcoin') {
            $data = ['payout_bitcoin_address' => inputPost('payout_bitcoin_address')];
        } elseif ($submit == 'iban') {
            $data = [
                'iban_full_name' => inputPost('iban_full_name'),
                'iban_country_id' => inputPost('iban_country_id'),
                'iban_bank_name' => inputPost('iban_bank_name'),
                'iban_number' => inputPost('iban_number')
            ];
        } elseif ($submit == 'swift') {
            $data = [
                'swift_full_name' => inputPost('swift_full_name'),
                'swift_address' => inputPost('swift_address'),
                'swift_state' => inputPost('swift_state'),
                'swift_city' => inputPost('swift_city'),
                'swift_postcode' => inputPost('swift_postcode'),
                'swift_country_id' => inputPost('swift_country_id'),
                'swift_bank_account_holder_name' => inputPost('swift_bank_account_holder_name'),
                'swift_iban' => inputPost('swift_iban'),
                'swift_code' => inputPost('swift_code'),
                'swift_bank_name' => inputPost('swift_bank_name'),
                'swift_bank_branch_city' => inputPost('swift_bank_branch_city'),
                'swift_bank_branch_country_id' => inputPost('swift_bank_branch_country_id')
            ];
        }
        if (!empty($data)) {
            return $this->builderUsersPayoutAccounts->where('user_id', clrNum($userId))->update($data);
        }
        return false;
    }

    //get payouts count
    public function getPayoutsCount($userId)
    {
        return $this->builderPayouts->where('user_id', clrNum($userId))->countAllResults();
    }

    //get paginated payouts
    public function getPaginatedPayouts($userId, $perPage, $offset)
    {
        return $this->builderPayouts->where('user_id', clrNum($userId))->orderBy('payouts.created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get active payouts
    public function getActivePayouts($userId)
    {
        return $this->builderPayouts->where('user_id', clrNum($userId))->where('status', 0)->orderBy('payouts.created_at DESC')->get()->getResult();
    }

    //withdraw money
    public function withdrawMoney($data)
    {
        return $this->builderPayouts->insert($data);
    }
}
