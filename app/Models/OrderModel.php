<?php namespace App\Models;

use CodeIgniter\Model;

/*
 * STATUS
 * processing  : 0
 * completed   : 1
 * cancelled   : 2
 */

class OrderModel extends BaseModel
{
    protected $builder;
    protected $builderOrderProducts;
    protected $builderRefundRequests;
    protected $builderDigitalSales;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('orders');
        $this->builderOrderProducts = $this->db->table('order_products');
        $this->builderRefundRequests = $this->db->table('refund_requests');
        $this->builderDigitalSales = $this->db->table('digital_sales');
    }

    //add order
    public function addOrder($dataTransaction, $isOfflinePayment, $offlinePaymentMethod = '')
    {
        $cartModel = new CartModel();
        $orderStatus = 'payment_received';
        $paymentStatus = 'payment_received';
        $paymentMethod = !empty($dataTransaction) ? $dataTransaction['payment_method'] : '';
        if ($isOfflinePayment) {
            $orderStatus = 'awaiting_payment';
            $paymentStatus = 'awaiting_payment';
            $paymentMethod = $offlinePaymentMethod;
            if ($offlinePaymentMethod == 'Cash On Delivery') {
                $orderStatus = 'order_processing';
            }
        }
        $cartItems = helperGetSession('mds_shopping_cart_final');
        $cartTotal = helperGetSession('mds_shopping_cart_total_final');
        if (!empty($cartTotal)) {
            $data = [
                'order_number' => uniqid(),
                'buyer_id' => 0,
                'buyer_type' => 'guest',
                'price_subtotal' => getPrice($cartTotal->subtotal, 'database'),
                'price_vat' => getPrice($cartTotal->vat, 'database'),
                'price_shipping' => getPrice($cartTotal->shipping_cost, 'database'),
                'price_total' => getPrice($cartTotal->total, 'database'),
                'price_currency' => $cartTotal->currency,
                'coupon_code' => '',
                'coupon_products' => '',
                'coupon_discount_rate' => $cartTotal->coupon_discount_rate,
                'coupon_discount' => getPrice($cartTotal->coupon_discount, 'database'),
                'coupon_seller_id' => $cartTotal->coupon_seller_id,
                'status' => 0,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ];
            if ($data['coupon_discount'] > 0) {
                $data['coupon_code'] = getCartDiscountCoupon();
                if (!empty($cartTotal->coupon_discount_products)) {
                    $data['coupon_products'] = $cartTotal->coupon_discount_products;
                }
            }
            if (!$isOfflinePayment) {
                //if cart does not have physical product
                if ($cartModel->checkCartHasPhysicalProduct() != true) {
                    $data['status'] = 1;
                }
            }
            if (authCheck()) {
                $data['buyer_type'] = 'registered';
                $data['buyer_id'] = user()->id;
            }
            //add order shipping
            $cartShipping = helperGetSession('mds_cart_shipping');
            if (!empty($cartShipping)) {
                $data['shipping'] = serialize($cartShipping);
            }
            if ($this->builder->insert($data)) {
                $orderId = $this->db->insertID();
                //update order number
                $this->updateOrderNumber($orderId);
                //add order products
                $this->addOrderProducts($orderId, $orderStatus);
                if (!$isOfflinePayment) {
                    //add digital sales
                    $this->addDigitalSales($orderId);
                    //add seller earnings
                    $this->addDigitalSalesSellerEarnings($orderId);
                    //add payment transaction
                    $this->addPaymentTransaction($dataTransaction, $orderId);
                }
                //set bidding quotes as completed
                $biddingModel = new BiddingModel();
                $biddingModel->setBiddingQuotesAsCompletedAfterPurchase($cartItems);
                //add used coupon
                if ($data['coupon_discount'] > 0) {
                    $couponModel = new CouponModel();
                    $couponModel->addUsedCoupon($orderId, $data['coupon_code']);
                }
                //add invoice
                $this->addInvoice($orderId);
                //add to email queue
                $this->addOrderEmail($orderId);
                //clear cart
                $cartModel->clearCart();

                return $orderId;
            }
            return false;
        }
        return false;
    }

    //update order number
    public function updateOrderNumber($orderId)
    {
        $data = [
            'order_number' => clrNum($orderId) + 10000
        ];
        $this->builder->where('id', $orderId)->update($data);
    }

    //add order products
    public function addOrderProducts($orderId, $orderStatus)
    {
        $orderId = clrNum($orderId);
        $cartItems = helperGetSession('mds_shopping_cart_final');
        $sellerShippingCosts = array();
        if (!empty(helperGetSession('mds_seller_shipping_costs'))) {
            $sellerShippingCosts = helperGetSession('mds_seller_shipping_costs');
        }
        if (!empty($cartItems)) {
            foreach ($cartItems as $cartItem) {
                $product = getActiveProduct($cartItem->product_id);
                $variationOptionIds = @serialize($cartItem->options_array);
                if (!empty($product)) {
                    $shippingMethod = '';
                    $sellerShippingCost = 0;
                    if (!empty($sellerShippingCosts[$product->user_id])) {
                        if (!empty($sellerShippingCosts[$product->user_id]->shipping_method_id)) {
                            $method = $this->db->table('shipping_zone_methods')->where('id', clrNum($sellerShippingCosts[$product->user_id]->shipping_method_id))->get()->getRow();
                            if (!empty($method)) {
                                $shippingMethod = @parseSerializedNameArray($method->name_array, selectedLangId());
                            }
                        }
                        if (!empty($sellerShippingCosts[$product->user_id]->cost)) {
                            $sellerShippingCost = getPrice($sellerShippingCosts[$product->user_id]->cost, 'database');
                        }
                    }
                    if ($this->paymentSettings->currency_converter == 1 && !empty($sellerShippingCost)) {
                        $sellerShippingCost = convertCurrencyByExchangeRate($sellerShippingCost, $this->selectedCurrency->exchange_rate);
                    }
                    $data = [
                        'order_id' => $orderId,
                        'seller_id' => $product->user_id,
                        'buyer_id' => 0,
                        'buyer_type' => 'guest',
                        'product_id' => $product->id,
                        'product_type' => $product->product_type,
                        'listing_type' => $product->listing_type,
                        'product_title' => $cartItem->product_title,
                        'product_slug' => $product->slug,
                        'product_unit_price' => getPrice($cartItem->unit_price, 'database'),
                        'product_quantity' => $cartItem->quantity,
                        'product_currency' => $cartItem->currency,
                        'product_vat_rate' => $product->vat_rate,
                        'product_vat' => getPrice($cartItem->product_vat, 'database'),
                        'product_total_price' => getPrice($cartItem->total_price, 'database'),
                        'variation_option_ids' => $variationOptionIds,
                        'commission_rate' => $this->generalSettings->commission_rate,
                        'order_status' => $orderStatus,
                        'is_approved' => 0,
                        'shipping_tracking_number' => '',
                        'shipping_tracking_url' => '',
                        'shipping_method' => $shippingMethod,
                        'seller_shipping_cost' => $sellerShippingCost,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    if (authCheck()) {
                        $data['buyer_id'] = user()->id;
                        $data['buyer_type'] = 'registered';
                    }
                    //approve if digital product
                    if ($product->product_type == 'digital') {
                        $data['is_approved'] = 1;
                        if ($orderStatus == 'payment_received') {
                            $data['order_status'] = 'completed';
                        } else {
                            $data['order_status'] = $orderStatus;
                        }
                    }
                    $data['product_total_price'] = getPrice($cartItem->total_price, 'database') + getPrice($cartItem->product_vat, 'database');

                    //update product if single sale
                    if ($this->builderOrderProducts->insert($data)) {
                        if ($product->product_type == 'digital' && $product->multiple_sale != 1) {
                            $this->db->table('products')->where('id', $product->id)->update(['is_sold' => 1]);
                        }
                    }
                }
            }
        }
    }

    //add digital sales
    public function addDigitalSales($orderId)
    {
        $cartItems = helperGetSession('mds_shopping_cart_final');
        $order = $this->getOrder($orderId);
        if (!empty($cartItems) && authCheck() && !empty($order)) {
            foreach ($cartItems as $cartItem) {
                $product = getActiveProduct($cartItem->product_id);
                if (!empty($product) && $product->product_type == 'digital') {
                    $dataDigital = [
                        'order_id' => $orderId,
                        'product_id' => $product->id,
                        'product_title' => getProductTitle($product),
                        'seller_id' => $product->user_id,
                        'buyer_id' => $order->buyer_id,
                        'license_key' => '',
                        'purchase_code' => generatePurchaseCode(),
                        'currency' => $product->currency,
                        'price' => $product->price,
                        'purchase_date' => date('Y-m-d H:i:s')
                    ];
                    $productModel = new ProductModel();
                    $licenseKey = $productModel->getUnusedLicenseKey($product->id);
                    if (!empty($licenseKey)) {
                        $dataDigital['license_key'] = $licenseKey->license_key;
                    }
                    $this->builderDigitalSales->insert($dataDigital);
                    //set license key as used
                    if (!empty($licenseKey)) {
                        $productModel->setLicenseKeyUsed($licenseKey->id);
                    }
                    //check remaining license keys
                    if ($product->listing_type == 'license_key') {
                        if (empty($productModel->getUnusedLicenseKey($product->id))) {
                            $this->db->table('products')->where('id', $product->id)->update(['is_sold' => 1]);
                        }
                    }
                }
            }
        }
    }

    //add digital sale
    public function addDigitalSale($productId, $orderId)
    {
        $product = getActiveProduct($productId);
        $order = $this->getOrder($orderId);
        if (!empty($product) && $product->product_type == 'digital' && !empty($order)) {
            $dataDigital = [
                'order_id' => $orderId,
                'product_id' => $product->id,
                'product_title' => getProductTitle($product),
                'seller_id' => $product->user_id,
                'buyer_id' => $order->buyer_id,
                'license_key' => '',
                'purchase_code' => generatePurchaseCode(),
                'currency' => $product->currency,
                'price' => $product->price,
                'purchase_date' => date('Y-m-d H:i:s')
            ];
            $productModel = new ProductModel();
            $licenseKey = $productModel->getUnusedLicenseKey($product->id);
            if (!empty($licenseKey)) {
                $dataDigital['license_key'] = $licenseKey->license_key;
            }
            $this->builderDigitalSales->insert($dataDigital);
            //set license key as used
            if (!empty($licenseKey)) {
                $productModel->setLicenseKeyUsed($licenseKey->id);
            }
        }
    }

    //add digital sales seller earnings
    public function addDigitalSalesSellerEarnings($orderId)
    {
        $earningsModel = new EarningsModel();
        $orderProducts = $this->getOrderProducts($orderId);
        if (!empty($orderProducts)) {
            foreach ($orderProducts as $orderProduct) {
                if ($orderProduct->product_type == 'digital') {
                    $earningsModel->addSellerEarnings($orderProduct);
                }
            }
        }
    }

    //add payment transaction
    public function addPaymentTransaction($dataTransaction, $orderId)
    {
        $data = [
            'payment_method' => $dataTransaction['payment_method'],
            'payment_id' => $dataTransaction['payment_id'],
            'order_id' => $orderId,
            'user_id' => 0,
            'user_type' => 'guest',
            'currency' => $dataTransaction['currency'],
            'payment_amount' => $dataTransaction['payment_amount'],
            'payment_status' => $dataTransaction['payment_status'],
            'ip_address' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        if (authCheck()) {
            $data['user_id'] = user()->id;
            $data['user_type'] = 'registered';
        }
        $ip = getIPAddress();
        if (!empty($ip)) {
            $data['ip_address'] = $ip;
        }
        $this->db->table('transactions')->insert($data);
    }

    //update order payment as received
    public function updateOrderPaymentReceived($order)
    {
        if (!empty($order)) {
            //update product payment status
            $dataOrder = [
                'payment_status' => 'payment_received',
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($this->builder->where('id', $order->id)->update($dataOrder)) {
                //update order products payment status
                $orderProducts = $this->getOrderProducts($order->id);
                if (!empty($orderProducts)) {
                    foreach ($orderProducts as $orderProduct) {
                        $data = [
                            'order_status' => 'payment_received',
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                        $this->builderOrderProducts->where('id', $orderProduct->id)->update($data);
                    }
                }
                //add invoice
                $this->addInvoice($order->id);
            }
        }
    }

    //get orders count
    public function getOrdersCount($userId)
    {
        return $this->builder->where('buyer_id', clrNum($userId))->countAllResults();
    }

    //get paginated orders
    public function getOrdersPaginated($userId, $perPage, $offset)
    {
        return $this->builder->where('buyer_id', clrNum($userId))->orderBy('id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get orders by buyer id
    public function getOrdersByBuyerId($userId)
    {
        return $this->builder->where('buyer_id', clrNum($userId))->orderBy('orders.created_at DESC')->get()->getResult();
    }

    //get order products
    public function getOrderProducts($orderId)
    {
        return $this->builderOrderProducts->where('order_id', clrNum($orderId))->get()->getResult();
    }

    //get seller order products
    public function getSellerOrderProducts($orderId, $sellerId)
    {
        return $this->builderOrderProducts->where('order_id', clrNum($orderId))->where('seller_id', clrNum($sellerId))->get()->getResult();
    }

    //get order product
    public function getOrderProduct($id)
    {
        return $this->builderOrderProducts->where('id', clrNum($id))->get()->getRow();
    }

    //get order
    public function getOrder($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get order by order number
    public function getOrderByOrderNumber($orderNumber)
    {
        return $this->builder->where('order_number', clrNum($orderNumber))->get()->getRow();
    }

    //update order product status
    public function updateOrderProductStatus($orderProductId)
    {
        $orderProduct = $this->getOrderProduct($orderProductId);
        if (!empty($orderProduct)) {
            if ($orderProduct->seller_id == user()->id) {
                $data = [
                    'order_status' => inputPost('order_status'),
                    'is_approved' => 0,
                    'shipping_tracking_number' => inputPost('shipping_tracking_number'),
                    'shipping_tracking_url' => inputPost('shipping_tracking_url'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                if ($orderProduct->product_type == 'digital' && $data['order_status'] == 'payment_received') {
                    $data['order_status'] = 'completed';
                }
                if ($data['order_status'] == 'shipped') {
                    //send email
                    if ($this->generalSettings->send_email_order_shipped == 1) {
                        $buyer = getUser($orderProduct->buyer_id);
                        if (!empty($buyer)) {
                            $emailData = [
                                'email_type' => 'order_shipped',
                                'email_address' => $buyer->email,
                                'email_subject' => trans("your_order_shipped"),
                                'email_data' => serialize(['orderProductId' => $orderProduct->id]),
                                'template_path' => 'email/order_shipped'
                            ];
                            addToEmailQueue($emailData);
                        }
                    }
                }
                return $this->builderOrderProducts->where('id', $orderProduct->id)->update($data);
            }
        }
        return false;
    }

    //add bank transfer payment report
    public function addBankTransferPaymentReport()
    {
        $data = [
            'order_number' => inputPost('order_number'),
            'payment_note' => inputPost('payment_note'),
            'receipt_path' => '',
            'user_id' => 0,
            'user_type' => 'guest',
            'status' => "pending",
            'ip_address' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        if (authCheck()) {
            $data['user_id'] = user()->id;
            $data['user_type'] = 'registered';
        }
        $ip = getIPAddress();
        if (!empty($ip)) {
            $data['ip_address'] = $ip;
        }
        $uploadModel = new UploadModel();
        $file = $uploadModel->uploadReceipt('file');
        if (!empty($file) && !empty($file['path'])) {
            $data['receipt_path'] = $file['path'];
        }
        return $this->db->table('bank_transfers')->insert($data);
    }

    //get sales count
    public function getSalesCount($status, $userId)
    {
        $this->filterSales($status);
        return $this->builder->join('order_products', 'order_products.order_id = orders.id')->select('orders.id')->groupBy('orders.id')
            ->where('order_products.seller_id', clrNum($userId))->where('order_products.order_status !=', 'refund_approved')->countAllResults();
    }

    //get paginated sales
    public function getSalesPaginated($status, $userId, $perPage, $offset)
    {
        $this->filterSales($status);
        return $this->builder->join('order_products', 'order_products.order_id = orders.id')->select('orders.*')->groupBy('orders.id')->where('order_products.seller_id', clrNum($userId))
            ->where('order_products.order_status !=', 'refund_approved')->orderBy('orders.id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter sales
    public function filterSales($status)
    {
        $paymentStatus = cleanStr(inputGet('payment_status'));
        $q = cleanStr(inputGet('q'));
        if (!empty($paymentStatus) && ($paymentStatus == 'payment_received' || $paymentStatus == 'awaiting_payment')) {
            $this->builder->where('orders.payment_status', $paymentStatus);
        }
        if (!empty($q)) {
            $this->builder->where('orders.order_number', $q);
        }
        if ($status == 'active') {
            $this->builder->where('order_products.order_status !=', 'completed')->where('order_products.order_status !=', 'cancelled');
        } elseif ($status == 'completed') {
            $this->builder->where('order_products.order_status =', 'completed');
        } elseif ($status == 'cancelled') {
            $this->builder->where('order_products.order_status =', 'cancelled');
        }
    }

    //get limited sales by seller
    public function getSalesBySellerLimited($userId, $limit)
    {
        return $this->builder->join('order_products', 'order_products.order_id = orders.id')->select('orders.*')->groupBy('orders.id')
            ->where('order_products.seller_id', clrNum($userId))->orderBy('orders.created_at DESC')->limit($limit)->get()->getResult();
    }

    //check order seller
    public function checkOrderSeller($orderId)
    {
        $orderProducts = $this->getOrderProducts($orderId);
        $result = false;
        if (!empty($orderProducts)) {
            foreach ($orderProducts as $product) {
                if ($product->seller_id == user()->id) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    //get seller total price
    public function getSellerFinalPrice($orderId)
    {
        $order = $this->getOrder($orderId);
        if (!empty($order)) {
            $orderProducts = $this->getOrderProducts($orderId);
            $total = 0;
            $sellerShipping = 0;
            if (!empty($orderProducts)) {
                foreach ($orderProducts as $orderProduct) {
                    if ($orderProduct->seller_id == user()->id) {
                        $total += $orderProduct->product_total_price;
                        $sellerShipping = $orderProduct->seller_shipping_cost;
                    }
                }
            }
            $total = $total + $sellerShipping;
            if (user()->id == $order->coupon_seller_id && !empty($order->coupon_discount)) {
                $total = $total - $order->coupon_discount;
            }
            return $total;
        }
    }

    //approve order product
    public function approveOrderProduct($orderProductId)
    {
        $orderProduct = $this->getOrderProduct($orderProductId);
        if (!empty($orderProduct)) {
            if (user()->id == $orderProduct->buyer_id) {
                $data = [
                    'is_approved' => 1,
                    'order_status' => 'completed',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                if ($this->builderOrderProducts->where('id', $orderProduct->id)->update($data)) {
                    $this->builder->where('id', $orderProduct->order_id)->update(['payment_status' => 'payment_received']);
                }
                return true;
            }
        }
        return false;
    }

    //decrease product stock after sale
    public function decreaseProductStockAfterSale($orderId)
    {
        $variationModel = new VariationModel();
        $orderProducts = $this->getOrderProducts($orderId);
        if (!empty($orderProducts)) {
            foreach ($orderProducts as $orderProduct) {
                $product = getProduct($orderProduct->product_id);
                if (!empty($product) && $product->product_type != 'digital') {
                    $optionIds = unserializeData($orderProduct->variation_option_ids);
                    if (!empty($optionIds)) {
                        foreach ($optionIds as $optionId) {
                            $option = $variationModel->getVariationOption($optionId);
                            if (!empty($option)) {
                                if ($option->is_default == 1) {
                                    $stock = $product->stock - $orderProduct->product_quantity;
                                    if ($stock < 0) {
                                        $stock = 0;
                                    }
                                    $this->db->table('products')->where('id', $product->id)->update(['stock' => $stock]);
                                } else {
                                    $stock = $option->stock - $orderProduct->product_quantity;
                                    if ($stock < 0) {
                                        $stock = 0;
                                    }
                                    $this->db->table('variation_options')->where('id', $option->id)->update(['stock' => $stock]);
                                }
                            }
                        }
                    } else {
                        $stock = $product->stock - $orderProduct->product_quantity;
                        if ($stock < 0) {
                            $stock = 0;
                        }
                        $this->db->table('products')->where('id', $product->id)->update(['stock' => $stock]);
                    }
                }
            }
        }
    }

    //check if user bought product
    public function checkUserBoughtProduct($userId, $productId)
    {
        if (!empty($this->builderOrderProducts->where('buyer_id', clrNum($userId))->where('product_id', clrNum($productId))->get()->getRow())) {
            return true;
        }
        return false;
    }

    //add invoice
    public function addInvoice($orderId)
    {
        $order = $this->getOrder($orderId);
        if (!empty($order)) {
            $orderShipping = unserializeData($order->shipping);
            $invoice = $this->getInvoiceByOrderNumber($order->order_number);
            if (empty($invoice)) {
                $invoiceItems = array();
                $orderProducts = $this->getOrderProducts($orderId);
                if (!empty($orderProducts)) {
                    foreach ($orderProducts as $orderProduct) {
                        $seller = getUser($orderProduct->seller_id);
                        $item = [
                            'id' => $orderProduct->id,
                            'seller' => !empty($seller) ? getUsername($seller) : ''
                        ];
                        array_push($invoiceItems, $item);
                    }
                }
                $client = getUser($order->buyer_id);
                if (!empty($client)) {
                    $country = getCountry($client->country_id);
                    $state = getState($client->state_id);
                    $city = getCity($client->city_id);
                    $data = [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'client_username' => getUsername($client),
                        'client_first_name' => $client->first_name,
                        'client_last_name' => $client->last_name,
                        'client_email' => $client->email,
                        'client_phone_number' => $client->phone_number,
                        'client_address' => $client->address,
                        'client_country' => !empty($country) ? $country->name : '',
                        'client_state' => !empty($state) ? $state->name : '',
                        'client_city' => !empty($city) ? $city->name : '',
                        'invoice_items' => @serialize($invoiceItems),
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    if (!empty($orderShipping)) {
                        $data['client_first_name'] = $orderShipping->bFirstName;
                        $data['client_last_name'] = $orderShipping->bLastName;
                        $data['client_email'] = $orderShipping->bEmail;
                        $data['client_phone_number'] = $orderShipping->bPhoneNumber;
                        $data['client_address'] = $orderShipping->bAddress;
                        $data['client_country'] = $orderShipping->bCountry;
                        $data['client_state'] = $orderShipping->bState;
                        $data['client_city'] = $orderShipping->bCity;
                    }
                    return $this->db->table('invoices')->insert($data);
                } else {
                    if (!empty($orderShipping)) {
                        $data['order_id'] = $order->id;
                        $data['order_number'] = $order->order_number;
                        $data['client_username'] = 'guest';
                        $data['client_first_name'] = $orderShipping->bFirstName;
                        $data['client_last_name'] = $orderShipping->bLastName;
                        $data['client_email'] = $orderShipping->bEmail;
                        $data['client_phone_number'] = $orderShipping->bPhoneNumber;
                        $data['client_address'] = $orderShipping->bAddress;
                        $data['client_country'] = $orderShipping->bCountry;
                        $data['client_state'] = $orderShipping->bState;
                        $data['client_city'] = $orderShipping->bCity;
                        $data['invoice_items'] = @serialize($invoiceItems);
                        $data['created_at'] = date('Y-m-d H:i:s');
                        return $this->db->table('invoices')->insert($data);
                    }
                }
            }
        }
        return false;
    }

    //get invoice
    public function getInvoice($id)
    {
        return $this->db->table('invoices')->where('id', clrNum($id))->get()->getRow();
    }

    //get invoice by order number
    public function getInvoiceByOrderNumber($orderNumber)
    {
        return $this->db->table('invoices')->where('order_number', clrNum($orderNumber))->get()->getRow();
    }

    /*
     * --------------------------------------------------------------------
     * Refund
     * --------------------------------------------------------------------
     */

    //add refund request
    public function addRefundRequest($orderProduct)
    {
        if (!empty($orderProduct)) {
            $order = $this->getOrder($orderProduct->order_id);
            if (!empty($order) && $order->status != 2) {
                if ($order->buyer_id == user()->id) {
                    $data = [
                        'buyer_id' => $orderProduct->buyer_id,
                        'seller_id' => $orderProduct->seller_id,
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'order_product_id' => $orderProduct->id,
                        'status' => 0,
                        'is_completed' => 0,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    if ($this->builderRefundRequests->insert($data)) {
                        $id = $this->db->insertID();
                        $this->addRefundMessage($id, true);
                    }
                    return $id;
                }
            }
        }
        return false;
    }

    //add refund request message
    public function addRefundMessage($requestId, $isBuyer)
    {
        $data = [
            'request_id' => $requestId,
            'user_id' => user()->id,
            'is_buyer' => $isBuyer,
            'message' => inputPost('message'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        $data['message'] = str_replace('\n', '<br/>', $data['message'] ?? '');
        if ($this->db->table('refund_requests_messages')->insert($data)) {
            $this->builderRefundRequests->where('id', clrNum($requestId))->update(['updated_at' => date('Y-m-d H:i:s')]);
        }
    }

    //get refund requests
    public function getRefundRequest($id)
    {
        return $this->builderRefundRequests->where('id', clrNum($id))->get()->getRow();
    }

    //get refund request count
    public function getRefundRequestCount($userId, $type)
    {
        if ($type == 'buyer') {
            $this->builderRefundRequests->where('buyer_id', clrNum($userId));
        } elseif ($type == 'seller') {
            $this->builderRefundRequests->where('seller_id', clrNum($userId));
        }
        return $this->builderRefundRequests->countAllResults();
    }

    //get paginated orders
    public function getRefundRequestsPaginated($userId, $type, $perPage, $offset)
    {
        if ($type == 'buyer') {
            $this->builderRefundRequests->where('buyer_id', clrNum($userId));
        } elseif ($type == 'seller') {
            $this->builderRefundRequests->where('seller_id', clrNum($userId));
        }
        return $this->builderRefundRequests->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get buyer active refund request ids
    public function getBuyerActiveRefundRequestIds($userId)
    {
        $idsArray = array();
        $rows = $this->builderRefundRequests->where('buyer_id', clrNum($userId))->where('status !=', 2)->get()->getResult();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                array_push($idsArray, $row->order_product_id);
            }
        }
        return $idsArray;
    }

    //get seller active refund request count
    public function getSellerActiveRefundRequestCount($userId)
    {
        return $this->builderRefundRequests->where('seller_id', clrNum($userId))->where('status = 0')->countAllResults();
    }

    //get refund messages
    public function getRefundMessages($id)
    {
        return $this->db->table('refund_requests_messages')->where('request_id', clrNum($id))->orderBy('id')->get()->getResult();
    }

    //approve or decline refund request
    public function approveDeclineRefund()
    {
        $id = inputPost('id');
        $request = $this->getRefundRequest($id);
        if (!empty($request)) {
            if ($request->seller_id == user()->id) {
                $submit = inputPost('submit');
                if ($submit == 1) {
                    $data = [
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $this->builderRefundRequests->where('id', $request->id)->update($data);
                } else {
                    $data = [
                        'status' => 2,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $this->builderRefundRequests->where('id', $request->id)->update($data);
                }
            }
            //send email
            $user = getUser($request->buyer_id);
            if (!empty($this->generalSettings->mail_username) && !empty($user)) {
                $emailData = [
                    'email_type' => 'refund',
                    'email_address' => $user->email,
                    'email_subject' => trans("refund_request"),
                    'template_path' => 'email/main',
                    'email_data' => serialize([
                        'content' => trans("msg_refund_request_update_email"),
                        'url' => generateUrl('refund_requests') . '/' . $request->id,
                        'buttonText' => trans("see_details")
                    ])
                ];
                addToEmailQueue($emailData);
            }
            return true;
        }
        return false;
    }

    //cancel order
    public function cancelOrder($orderId)
    {
        $order = $this->getOrder($orderId);
        if (!empty($order)) {
            $updateOrder = false;
            if (isAdmin()) {
                $updateOrder = true;
            } else {
                if ($order->buyer_id == user()->id) {
                    if ($order->payment_method != 'Cash On Delivery' || ($order->payment_method == 'Cash On Delivery' && dateDifferenceInHours(date('Y-m-d H:i:s'), $order->created_at) <= 24)) {
                        $updateOrder = true;
                    }
                }
            }
            if ($updateOrder == true) {
                $data = [
                    'order_status' => 'cancelled',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                if ($this->builderOrderProducts->where('order_id', $orderId)->update($data)) {
                    return $this->builder->where('id', $orderId)->update(['status' => 2, 'updated_at' => date('Y-m-d H:i:s')]);
                }
            }
        }
        return false;
    }

    //check if valid transaction by payment Id
    public function isValidTransaction($paymentType, $paymentId, $paymentMethod)
    {
        if ($paymentType == 'sale') {
            if (empty($this->db->table('transactions')->where('payment_method', cleanStr($paymentMethod))->where('payment_id', cleanStr($paymentId))->get()->getRow())) {
                return true;
            }
        } elseif ($paymentType == 'membership') {
            if (empty($this->db->table('membership_transactions')->where('payment_method', cleanStr($paymentMethod))->where('payment_id', cleanStr($paymentId))->get()->getRow())) {
                return true;
            }
        } elseif ($paymentType == 'promote') {
            if (empty($this->db->table('promoted_transactions')->where('payment_method', cleanStr($paymentMethod))->where('payment_id', cleanStr($paymentId))->get()->getRow())) {
                return true;
            }
        }
        return false;
    }

    //build order email
    public function addOrderEmail($orderId)
    {
        if ($this->generalSettings->send_email_buyer_purchase == 1) {
            $order = $this->getOrder($orderId);
            if (!empty($order)) {
                $orderProducts = $this->getOrderProducts($order->id);
                $shipping = unserializeData($order->shipping);
                if (!empty($order)) {
                    //send to buyer
                    $to = '';
                    if (!empty($shipping)) {
                        $to = $shipping->sEmail;
                    }
                    if ($order->buyer_type == 'registered') {
                        $user = getUser($order->buyer_id);
                        if (!empty($user)) {
                            $to = $user->email;
                        }
                    }
                    $emailData = [
                        'email_type' => 'new_order',
                        'email_address' => $to,
                        'email_subject' => trans("email_text_thank_for_order"),
                        'email_data' => serialize(['orderId' => $order->id]),
                        'template_path' => 'email/new_order'
                    ];
                    addToEmailQueue($emailData);
                    //send to sellers
                    if (!empty($orderProducts)) {
                        foreach ($orderProducts as $orderProduct) {
                            $seller = getUser($orderProduct->seller_id);
                            if (!empty($seller)) {
                                $emailData = [
                                    'email_type' => 'new_order_seller',
                                    'email_address' => $seller->email,
                                    'email_subject' => trans("you_have_new_order"),
                                    'email_data' => serialize(['orderId' => $order->id, 'sellerId' => $seller->id]),
                                    'template_path' => 'email/new_order_seller'
                                ];
                                addToEmailQueue($emailData);
                            }
                        }
                    }
                }
            }
        }
    }
}