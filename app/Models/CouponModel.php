<?php namespace App\Models;

use CodeIgniter\Model;

class CouponModel extends BaseModel
{
    protected $builder;
    protected $builderCouponProducts;
    protected $builderUsed;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('coupons');
        $this->builderCouponProducts = $this->db->table('coupon_products');
        $this->builderUsed = $this->db->table('coupons_used');
    }

    //input values
    public function inputValues()
    {
        $data = [
            'coupon_code' => removeSpecialCharacters(inputPost('coupon_code')),
            'discount_rate' => inputPost('discount_rate'),
            'coupon_count' => inputPost('coupon_count'),
            'minimum_order_amount' => inputPost('minimum_order_amount'),
            'currency' => $this->defaultCurrency->code,
            'usage_type' => inputPost('usage_type'),
            'category_ids' => '',
            'expiry_date' => inputPost('expiry_date')
        ];
        if ($data['discount_rate'] > 99) {
            $data['discount_rate'] = 99;
        }
        if ($data['discount_rate'] < 1) {
            $data['discount_rate'] = 1;
        }
        if ($data['usage_type'] != 'single' && $data['usage_type'] != 'multiple') {
            $data['usage_type'] = 'single';
        }
        if ($data['coupon_count'] <= 0) {
            $data['discount_rate'] = 0;
        }
        //selected category ids
        $array = array();
        $categoryIds = inputPost('category_id');
        if (!empty($categoryIds)) {
            foreach ($categoryIds as $id) {
                array_push($array, $id);
            }
            $data['category_ids'] = implode(',', $array);
        }
        return $data;
    }

    //add coupon
    public function addCoupon()
    {
        $data = $this->inputValues();
        $data['minimum_order_amount'] = getPrice($data['minimum_order_amount'], 'database');
        if (empty($data['minimum_order_amount'])) {
            $data['minimum_order_amount'] = 0;
        }
        $data['seller_id'] = user()->id;
        $data['created_at'] = date('Y-m-d H:i:s');
        if ($this->builder->insert($data)) {
            $couponId = $this->db->insertID();
            //coupon products
            $productIds = $this->getSelectedProductsArray();
            foreach ($productIds as $item) {
                if (empty($this->builderCouponProducts->where('coupon_id', clrNum($couponId))->where('product_id', clrNum($item))->get()->getRow())) {
                    $this->builderCouponProducts->insert(['coupon_id' => clrNum($couponId), 'product_id' => clrNum($item)]);
                }
            }
        }
        return true;
    }

    //edit coupon
    public function editCoupon($id)
    {
        $data = $this->inputValues();
        $data['minimum_order_amount'] = getPrice($data['minimum_order_amount'], 'database');
        if (empty($data['minimum_order_amount'])) {
            $data['minimum_order_amount'] = 0;
        }
        if ($this->builder->where('id', clrNum($id))->update($data)) {
            //coupon products
            $productIds = $this->getSelectedProductsArray();
            $couponProducts = $this->getCouponProducts($id);
            if (!empty($couponProducts)) {
                foreach ($couponProducts as $item) {
                    if (!in_array($item->product_id, $productIds)) {
                        $this->builderCouponProducts->where('coupon_id', clrNum($id))->where('product_id', clrNum($item->product_id))->delete();
                    }
                }
            }
            if (!empty($productIds)) {
                foreach ($productIds as $productId) {
                    if (empty($this->builderCouponProducts->where('coupon_id', clrNum($id))->where('product_id', clrNum($productId))->get()->getRow())) {
                        $this->builderCouponProducts->insert(['coupon_id' => clrNum($id), 'product_id' => clrNum($productId)]);
                    }
                }
            }
        }
        return true;
    }

    //add used coupon
    public function addUsedCoupon($orderId, $couponCode)
    {
        $userId = 0;
        if (authCheck()) {
            $userId = user()->id;
        }
        $data = [
            'order_id' => $orderId,
            'user_id' => $userId,
            'coupon_code' => $couponCode,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->builderUsed->insert($data);
    }

    //get selected products array
    public function getSelectedProductsArray()
    {
        $array = array();
        $productIds = inputPost('product_id');
        if (!empty($productIds)) {
            foreach ($productIds as $key => $value) {
                array_push($array, $value);
            }
        }
        return $array;
    }

    //get coupon
    public function getCoupon($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get coupon by code
    public function getCouponByCode($code)
    {
        return $this->builder->where('coupon_code', removeSpecialCharacters($code))->get()->getRow();
    }

    //get coupon by code cart
    public function getCouponByCodeCart($code)
    {
        return $this->builder->select('coupons.seller_id, coupons.coupon_code, coupons.discount_rate, coupons.coupon_count, coupons.minimum_order_amount, coupons.currency, coupons.usage_type, coupons.expiry_date,
        (SELECT GROUP_CONCAT(coupon_products.product_id) FROM coupon_products WHERE coupon_products.coupon_id = coupons.id) AS product_ids, 
        (SELECT COUNT(coupons_used.id) FROM coupons_used WHERE coupons_used.coupon_code = coupons.coupon_code) AS used_coupon_count')->where('coupon_code', removeSpecialCharacters($code))->get()->getRow();
    }

    //get coupons count
    public function getCouponsCount($userId)
    {
        return $this->builder->where('seller_id', clrNum($userId))->countAllResults();
    }

    //get coupons paginated
    public function getCouponsPaginated($userId, $perPage, $offset)
    {
        return $this->builder->where('seller_id', clrNum($userId))->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get used coupons count
    public function getUsedCouponsCount($couponCode)
    {
        return $this->builderUsed->where('coupon_code', removeSpecialCharacters($couponCode))->countAllResults();
    }

    //check coupon used
    public function isCouponUsed($userId, $couponCode)
    {
        if ($this->builderUsed->where('coupon_code', removeSpecialCharacters($couponCode))->where('user_id', clrNum($userId))->countAllResults() > 0) {
            return true;
        }
        return false;
    }

    //get coupon products by category
    public function getCouponProductsByCategory($userId, $categoryId)
    {
        return $this->db->table('products')->select('products.*, product_details.title')->join('product_details', 'product_details.product_id = products.id')
            ->where('product_details.lang_id', selectedLangId())->where('products.user_id', clrNum($userId))->where('products.category_id', clrNum($categoryId))
            ->where('products.listing_type', 'sell_on_site')->where('products.status', 1)->where('products.visibility', 1)->where('products.is_draft', 0)
            ->where('products.is_deleted', 0)->orderBy('products.created_at DESC')->get()->getResult();
    }

    //get coupon products
    public function getCouponProducts($couponId)
    {
        return $this->builderCouponProducts->where('coupon_id', clrNum($couponId))->get()->getResult();
    }

    //delete coupon
    public function deleteCoupon($coupon)
    {
        if (!empty($coupon)) {
            if ($this->builder->where('id', $coupon->id)->delete()) {
                $this->builderCouponProducts->where('coupon_id', $coupon->id)->delete();
                $this->builderUsed->where('coupon_code', $coupon->coupon_code)->delete();
                return true;
            }
        }
        return false;
    }
}
