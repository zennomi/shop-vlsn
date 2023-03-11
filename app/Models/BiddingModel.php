<?php namespace App\Models;

use CodeIgniter\Model;

class BiddingModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('quote_requests');
    }

    //request quote
    public function requestQuote($product)
    {
        $quantity = inputPost('product_quantity');
        if (empty($quantity)) {
            $quantity = 1;
        }
        $cartModel = new CartModel();
        $appendedVariations = $cartModel->getSelectedVariations($product->id)->str;
        $data = [
            'product_id' => $product->id,
            'product_title' => getProductTitle($product) . ' ' . $appendedVariations,
            'product_quantity' => $quantity,
            'seller_id' => $product->user_id,
            'buyer_id' => user()->id,
            'status' => 'new_quote_request',
            'price_offered' => 0,
            'price_currency' => '',
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        if ($this->builder->insert($data)) {
            return $this->db->insertID();
        }
        return false;
    }

    //submit quote
    public function submitQuote($quoteRequest)
    {
        if (!empty($quoteRequest) && user()->id == $quoteRequest->seller_id) {
            $data = [
                'price_offered' => inputPost('price'),
                'price_currency' => inputPost('currency'),
                'status' => 'pending_quote',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $data['price_offered'] = getPrice($data['price_offered'], 'database');
            if (empty($data['price_offered'])) {
                $data['price_offered'] = 0;
            }
            return $this->builder->where('id', $quoteRequest->id)->update($data);
        }
        return false;
    }

    //accept quote
    public function acceptQuote($quoteRequest)
    {
        if (!empty($quoteRequest) && user()->id == $quoteRequest->buyer_id) {
            $data = [
                'status' => 'pending_payment',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            return $this->builder->where('id', $quoteRequest->id)->update($data);
        }
        return false;
    }

    //reject quote
    public function rejectQuote($quoteRequest)
    {
        if (!empty($quoteRequest) && user()->id == $quoteRequest->buyer_id) {
            $data = [
                'status' => 'rejected_quote',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            return $this->builder->where('id', $quoteRequest->id)->update($data);
        }
        return false;
    }

    //get quote request
    public function getQuoteRequest($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get quote requests count
    public function getQuoteRequestsCount($userId)
    {
        $this->builder->join('products', 'quote_requests.product_id = products.id');
        if ($this->generalSettings->membership_plans_system == 1) {
            $this->builder->join('users', 'quote_requests.seller_id = users.id AND users.banned = 0 AND users.is_membership_plan_expired = 0');
        }
        return $this->builder->where('products.status', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)
            ->where("((products.product_type = 'physical' AND products.stock > 0) OR products.product_type = 'digital')")->where('quote_requests.buyer_id', clrNum($userId))
            ->where('quote_requests.is_buyer_deleted', 0)->countAllResults();
    }

    //get paginated quote requests
    public function getQuoteRequestsPaginated($userId, $perPage, $offset)
    {
        $this->builder->select('quote_requests.*')->join('products', 'quote_requests.product_id = products.id');
        if ($this->generalSettings->membership_plans_system == 1) {
            $this->builder->join('users', 'quote_requests.seller_id = users.id AND users.banned = 0 AND users.is_membership_plan_expired = 0');
        }
        return $this->builder->where('products.status', 1)->where('products.is_draft', 0)->where('products.is_deleted', 0)
            ->where("((products.product_type = 'physical' AND products.stock > 0) OR products.product_type = 'digital')")->where('quote_requests.buyer_id', clrNum($userId))
            ->where('quote_requests.is_buyer_deleted', 0)->orderBy('updated_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get vendor quote requests count
    public function getVendorQuoteRequestsCount($userId)
    {
        $this->filterQuoteRequests();
        return $this->builder->join('products', 'quote_requests.product_id = products.id')->where('products.status', 1)->where('products.is_draft', 0)
            ->where('products.is_deleted', 0)->where("((products.product_type = 'physical' AND products.stock > 0) OR products.product_type = 'digital')")
            ->where('quote_requests.seller_id', clrNum($userId))->where('quote_requests.is_seller_deleted', 0)->countAllResults();
    }

    //get vendor paginated quote requests
    public function getVendorQuoteRequestsPaginated($userId, $perPage, $offset)
    {
        $this->filterQuoteRequests();
        return $this->builder->select('quote_requests.*')->join('products', 'quote_requests.product_id = products.id')->where('products.status', 1)->where('products.is_draft', 0)
            ->where('products.is_deleted', 0)->where("((products.product_type = 'physical' AND products.stock > 0) OR products.product_type = 'digital')")
            ->where('quote_requests.seller_id', clrNum($userId))->where('quote_requests.is_seller_deleted', 0)->orderBy('updated_at DESC')
            ->limit($perPage, $offset)->get()->getResult();
    }

    //get new quote requests count
    public function getNewQuoteRequestsCount($userId)
    {
        return $this->builder->where('seller_id', clrNum($userId))->where('is_seller_deleted', 0)->where('status', 'new_quote_request')->countAllResults();
    }

    //check active quote request
    public function checkActiveQuoteRequest($productId, $buyerId)
    {
        $row = $this->builder->where('product_id', clrNum($productId))->where('buyer_id', clrNum($buyerId))->where('status', 'new_quote_request')->get()->getRow();
        if(!empty($row)){
            return false;
        }
        return true;
    }

    //delete quote request
    public function deleteQuoteRequest($id)
    {
        $quoteRequest = $this->getQuoteRequest($id);
        if (!empty($quoteRequest)) {
            if (user()->id == $quoteRequest->seller_id || user()->id == $quoteRequest->buyer_id) {
                if (user()->id == $quoteRequest->buyer_id) {
                    $data = [
                        'is_buyer_deleted' => 1,
                        'status' => 'closed',
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    if ($quoteRequest->status == 'completed') {
                        $data['status'] = 'completed';
                    }
                    return $this->builder->where('id', $quoteRequest->id)->update($data);
                } elseif (user()->id == $quoteRequest->seller_id) {
                    $data = [
                        'is_seller_deleted' => 1,
                        'status' => 'closed',
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    if ($quoteRequest->status == 'completed') {
                        $data['status'] = 'completed';
                    }
                    return $this->builder->where('id', $id)->update($data);
                }
            }
        }
        return false;
    }

    //delete quote if both deleted
    public function deleteQuoteRequestIfBothDeleted($id)
    {
        $quoteRequest = $this->getQuoteRequest($id);
        if (!empty($quoteRequest)) {
            if (user()->id == $quoteRequest->seller_id || user()->id == $quoteRequest->buyer_id) {
                if ($quoteRequest->is_buyer_deleted == 1 && $quoteRequest->is_seller_deleted == 1) {
                    return $this->builder->where('id', $id)->delete();
                }
            }
        }
        return false;
    }

    //set bidding quotes as completed after purchase
    public function setBiddingQuotesAsCompletedAfterPurchase($cartItems)
    {
        if (!empty($cartItems)) {
            foreach ($cartItems as $cartItem) {
                if ($cartItem->purchase_type == 'bidding') {
                    $data = [
                        'status' => 'completed',
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $this->builder->where('id', $cartItem->quote_request_id)->update($data);
                }
            }
        }
    }

    //get admin quote requests count
    public function getQuoteRequestCountAdmin()
    {
        $this->filterQuoteRequests();
        return $this->builder->countAllResults();
    }

    //get admin quote requests
    public function getQuoteRequestsPaginatedAdmin($perPage, $offset)
    {
        $this->filterQuoteRequests();
        return $this->builder->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter quote requests
    public function filterQuoteRequests()
    {
        $status = inputGet('status');
        $q = inputGet('q');
        if ($status == 'new_quote_request' || $status == 'pending_quote' || $status == 'pending_payment' || $status == 'rejected_quote' || $status == 'closed' || $status == 'completed') {
            $this->builder->where('quote_requests.status', $status);
        }
        if (!empty($q)) {
            $this->builder->groupStart()->like('quote_requests.product_title', $q)->orLike('quote_requests.id', $q)->groupEnd();
        }
    }

    //delete admin quote request
    public function deleteQuoteRequestAdmin($id)
    {
        if (isAdmin()) {
            $quoteRequest = $this->getQuoteRequest($id);
            if (!empty($quoteRequest)) {
                return $this->builder->where('id', $id)->delete();
            }
        }
        return false;
    }

}
