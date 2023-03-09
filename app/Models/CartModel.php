<?php namespace App\Models;

use CodeIgniter\Model;

class CartModel extends BaseModel
{
    protected $cartProductIds;
    protected $sessionCartItems;

    public function __construct()
    {
        parent::__construct();
        $this->cartProductIds = array();
        $this->sessionCartItems = $this->getSessCartItems();
    }

    //add to cart
    public function addToCart($product)
    {
        $cart = $this->sessionCartItems;
        $quantity = inputPost('product_quantity');
        if ($quantity < 1) {
            $quantity = 1;
        }
        if ($product->product_type == 'digital') {
            $quantity = 1;
        }
        $selectedVariations = $this->getSelectedVariations($product->id);
        $appendedVariations = $selectedVariations->str;
        $optionsArray = $selectedVariations->options_array;
        $productId = $product->id;
        $productTitle = getProductTitle($product) . ' ' . $appendedVariations;
        //check if item exists
        $cart = $this->sessionCartItems;
        $updateQuantity = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                if ($item->product_id == $productId && $item->product_title == $productTitle) {
                    if ($product->listing_type != 'license_key' && $product->product_type != 'digital') {
                        $item->quantity += 1;
                    }
                    $updateQuantity = 1;
                }
            }
        }
        if ($updateQuantity == 1) {
            helperSetSession('mds_shopping_cart', $cart);
        } else {
            $item = new \stdClass();
            $item->cart_item_id = generateToken();
            $item->product_id = $product->id;
            $item->product_type = $product->product_type;
            $item->product_title = getProductTitle($product) . ' ' . $appendedVariations;
            $item->options_array = $optionsArray;
            $item->quantity = $quantity;
            $item->unit_price = null;
            $item->total_price = null;
            $item->discount_rate = 0;
            $item->currency = $this->selectedCurrency->code;
            $item->product_vat = 0;
            $item->is_stock_available = null;
            $item->purchase_type = 'product';
            $item->quote_request_id = 0;
            array_push($cart, $item);
            helperSetSession('mds_shopping_cart', $cart);
        }
    }

    //add to cart quote
    public function addToCartQuote($quoteRequestId)
    {
        $biddingModel = new BiddingModel();
        $quoteRequest = $biddingModel->getQuoteRequest($quoteRequestId);
        if (!empty($quoteRequest)) {
            $product = getActiveProduct($quoteRequest->product_id);
            if (!empty($product)) {
                $cart = $this->sessionCartItems;
                $item = new \stdClass();
                $item->cart_item_id = generateToken();
                $item->product_id = $product->id;
                $item->product_type = $product->product_type;
                $item->product_title = $quoteRequest->product_title;
                $item->options_array = array();
                $item->quantity = $quoteRequest->product_quantity;
                $item->unit_price = null;
                $item->total_price = null;
                $item->currency = $this->selectedCurrency->code;
                $item->product_vat = 0;
                $item->is_stock_available = 1;
                $item->purchase_type = 'bidding';
                $item->quote_request_id = $quoteRequest->id;
                array_push($cart, $item);
                helperSetSession('mds_shopping_cart', $cart);
                return true;
            }
        }
        return false;
    }

    //remove from cart
    public function removeFromCart($cartItemId)
    {
        $cart = $this->sessionCartItems;
        if (!empty($cart)) {
            $newCart = array();
            foreach ($cart as $item) {
                if ($item->cart_item_id != $cartItemId) {
                    array_push($newCart, $item);
                }
            }
            helperSetSession('mds_shopping_cart', $newCart);
        }
    }

    //get selected variations
    public function getSelectedVariations($productId)
    {
        $variationModel = new VariationModel();
        $object = new \stdClass();
        $object->str = '';
        $object->options_array = array();
        $variations = $variationModel->getProductVariations($productId);
        $str = '';
        if (!empty($variations)) {
            foreach ($variations as $variation) {
                $appendText = '';
                if (!empty($variation) && $variation->is_visible == 1) {
                    $variationVal = inputPost('variation' . $variation->id);
                    if (!empty($variationVal)) {
                        if ($variation->variation_type == 'text' || $variation->variation_type == 'number') {
                            $appendText = $variationVal;
                        } else {
                            //check multiselect
                            if (is_array($variationVal)) {
                                $i = 0;
                                foreach ($variationVal as $item) {
                                    $option = $variationModel->getVariationOption($item);
                                    if (!empty($option)) {
                                        if ($i == 0) {
                                            $appendText .= getVariationOptionName($option->option_names, selectedLangId());
                                        } else {
                                            $appendText .= ' - ' . getVariationOptionName($option->option_names, selectedLangId());
                                        }
                                        $i++;
                                        array_push($object->options_array, $option->id);
                                    }
                                }
                            } else {
                                $option = $variationModel->getVariationOption($variationVal);
                                if (!empty($option)) {
                                    $appendText .= getVariationOptionName($option->option_names, selectedLangId());
                                    array_push($object->options_array, $option->id);
                                }
                            }
                        }
                        if (empty($str)) {
                            $str .= '(' . getVariationLabel($variation->label_names, selectedLangId()) . ': ' . $appendText;
                        } else {
                            $str .= ', ' . getVariationLabel($variation->label_names, selectedLangId()) . ': ' . $appendText;
                        }
                    }
                }
            }
            if (!empty($str)) {
                $str = $str . ')';
            }
        }
        $object->str = $str;
        return $object;
    }

    //get product price and stock
    public function getProductPriceAndStock($product, $cartProductTitle, $optionsArray)
    {
        $object = new \stdClass();
        $object->price = 0;
        $object->discount_rate = 0;
        $object->price_calculated = 0;
        $object->is_stock_available = 0;
        if (!empty($product)) {
            //quantity in cart
            $quantityInCart = 0;
            if (!empty(helperGetSession('mds_shopping_cart'))) {
                foreach (helperGetSession('mds_shopping_cart') as $item) {
                    if (($item->product_id == $product->id && $item->product_title == $cartProductTitle) || ($item->product_id == $product->id && empty($item->options_array))) {
                        $quantityInCart += $item->quantity;
                    }
                }
            }
            $stock = $product->stock;
            $price = getPrice($product->price, 'decimal');
            $discountRate = $product->discount_rate;
            if (!empty($optionsArray)) {
                $variationModel = new VariationModel();
                foreach ($optionsArray as $optionId) {
                    $option = $variationModel->getVariationOption($optionId);
                    if (!empty($option)) {
                        $variation = $variationModel->getVariation($option->variation_id);
                        if ($variation->use_different_price == 1) {
                            if (!empty($option->price)) {
                                $price = getPrice($option->price, 'decimal');
                            }
                            if (!empty($option->discount_rate)) {
                                $discountRate = $option->discount_rate;
                            }
                        }
                        if ($option->is_default != 1) {
                            $stock = $option->stock;
                        }
                    }
                }
            }
            if (empty($price)) {
                $object->price = $price;
                $discountRate = $product->discount_rate;
            }
            $price = $price - (($price * $discountRate) / 100);
            if (!empty($price)) {
                $object->price_calculated = number_format($price, 2, '.', '');
            }
            if ($stock >= $quantityInCart) {
                $object->is_stock_available = 1;
            }
            if ($product->product_type == 'digital') {
                $object->is_stock_available = 1;
            }
        }
        return $object;
    }

    //update cart product quantity
    public function updateCartProductQuantity($productId, $cartItemId, $quantity)
    {
        if ($quantity < 1) {
            $quantity = 1;
        }
        $cart = $this->sessionCartItems;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                if ($item->cart_item_id == $cartItemId) {
                    $item->quantity = $quantity;
                }
            }
        }
        helperSetSession('mds_shopping_cart', $cart);
    }

    //get cart items session
    public function getSessCartItems()
    {
        $cart = array();
        $newCart = array();
        $this->cartProductIds = array();
        if (!empty(helperGetSession('mds_shopping_cart'))) {
            $cart = helperGetSession('mds_shopping_cart');
        }
        if (!empty($cart)) {
            foreach ($cart as $cartItem) {
                $product = getActiveProduct($cartItem->product_id);
                if (!empty($product)) {
                    //if purchase type is bidding
                    if ($cartItem->purchase_type == 'bidding') {
                        $biddingModel = new BiddingModel();
                        $quoteRequest = $biddingModel->getQuoteRequest($cartItem->quote_request_id);
                        if (!empty($quoteRequest) && $quoteRequest->status == 'pending_payment') {
                            $priceOffered = getPrice($quoteRequest->price_offered, 'decimal');
                            //convert currency
                            $baseCurrency = $this->selectedCurrency;
                            if ($this->paymentSettings->currency_converter == 1) {
                                $baseCurrency = $this->selectedCurrency;
                                if (!empty($baseCurrency)) {
                                    $priceOffered = convertCurrencyByExchangeRate($priceOffered, $baseCurrency->exchange_rate);
                                }
                            }
                            $item = new \stdClass();
                            $item->cart_item_id = $cartItem->cart_item_id;
                            $item->product_id = $product->id;
                            $item->product_type = $cartItem->product_type;
                            $item->product_title = $cartItem->product_title;
                            $item->product_image = getProductItemImage($product);
                            $item->options_array = $cartItem->options_array;
                            $item->quantity = $cartItem->quantity;
                            $item->unit_price = $priceOffered / $quoteRequest->product_quantity;
                            $item->total_price = $priceOffered;
                            $item->discount_rate = 0;
                            $item->currency = $baseCurrency->code;
                            $item->product_vat = 0;
                            $item->purchase_type = $cartItem->purchase_type;
                            $item->quote_request_id = $cartItem->quote_request_id;
                            $item->seller_id = $product->user_id;
                            $item->shipping_class_id = $product->shipping_class_id;
                            $item->is_stock_available = 1;
                            array_push($newCart, $item);
                        }
                    } else {
                        $object = $this->getProductPriceAndStock($product, $cartItem->product_title, $cartItem->options_array);
                        //convert currency
                        $baseCurrency = $this->selectedCurrency;
                        if ($this->paymentSettings->currency_converter == 1) {
                            $baseCurrency = $this->selectedCurrency;
                            if (!empty($baseCurrency)) {
                                $object->price_calculated = convertCurrencyByExchangeRate($object->price_calculated, $baseCurrency->exchange_rate);
                            }
                        }
                        $item = new \stdClass();
                        $item->cart_item_id = $cartItem->cart_item_id;
                        $item->product_id = $product->id;
                        $item->product_type = $cartItem->product_type;
                        $item->product_title = $cartItem->product_title;
                        $item->product_image = getProductItemImage($product);
                        $item->options_array = $cartItem->options_array;
                        $item->quantity = $cartItem->quantity;
                        $item->unit_price = $object->price_calculated;
                        $item->total_price = $object->price_calculated * $cartItem->quantity;
                        $item->discount_rate = $object->discount_rate;
                        $item->currency = $product->currency;
                        $item->product_vat = $this->calculateTotalVat($object->price_calculated, $product->vat_rate, $cartItem->quantity);
                        $item->purchase_type = $cartItem->purchase_type;
                        $item->quote_request_id = $cartItem->quote_request_id;
                        $item->seller_id = $product->user_id;
                        $item->shipping_class_id = $product->shipping_class_id;
                        $item->is_stock_available = $object->is_stock_available;
                        array_push($newCart, $item);
                    }
                }
            }
        }
        //convert currency
        if ($this->paymentSettings->currency_converter == 1 && !empty($baseCurrency)) {
            if (!empty($newCart)) {
                foreach ($newCart as $item) {
                    $item->currency = $baseCurrency->code;
                }
            }
        }

        helperSetSession('mds_shopping_cart', $newCart);
        return $newCart;
    }

    //calculate cart total
    public function calculateCartTotal($cartItems, $currencyCode = null, $setSession = true)
    {
        if (empty($currencyCode)) {
            $currencyCode = $this->selectedCurrency->code;
        }
        $cartTotal = new \stdClass();
        $cartTotal->subtotal = 0;
        $cartTotal->vat = 0;
        $cartTotal->shipping_cost = 0;
        $cartTotal->total_before_shipping = 0;
        $cartTotal->total = 0;
        $cartTotal->is_stock_available = 1;
        $cartTotal->currency = $currencyCode;
        if (!empty($cartItems)) {
            foreach ($cartItems as $item) {
                if ($item->purchase_type == 'bidding') {
                    $cartTotal->subtotal += $item->total_price;
                } else {
                    $cartTotal->subtotal += $item->total_price;
                    $cartTotal->vat += $item->product_vat;
                }
                if ($item->is_stock_available != 1) {
                    $cartTotal->is_stock_available = 0;
                }
            }
        }
        //set shipping cost
        if (!empty(helperGetSession('mds_cart_shipping')) && !empty(helperGetSession('mds_cart_shipping')->totalCost)) {
            $shippingCost = helperGetSession('mds_cart_shipping')->totalCost;
            $currency = getCurrencyByCode($currencyCode);
            if (!empty($currency)) {
                $shippingCost = convertCurrencyByExchangeRate($shippingCost, $currency->exchange_rate);
            }
            $cartTotal->shipping_cost = $shippingCost;
        }
        $cartTotal->total_before_shipping = $cartTotal->subtotal + $cartTotal->vat;
        $cartTotal->total = $cartTotal->subtotal + $cartTotal->vat + $cartTotal->shipping_cost;
        //discount coupon
        $arrayDiscount = $this->calculateCouponDiscount($cartItems);
        $cartTotal->coupon_discount_products = $arrayDiscount['product_ids'];
        if (!empty($cartTotal->coupon_discount_products)) {
            $cartTotal->coupon_discount_products = trim($cartTotal->coupon_discount_products, ',');
        }
        $cartTotal->coupon_discount_rate = $arrayDiscount['discount_rate'];
        $cartTotal->coupon_discount = $arrayDiscount['total_discount'];
        $cartTotal->coupon_seller_id = $arrayDiscount['seller_id'];
        $cartTotal->total_before_shipping = $cartTotal->total_before_shipping - $cartTotal->coupon_discount;
        $cartTotal->total = $cartTotal->total - $cartTotal->coupon_discount;
        if ($setSession == true) {
            helperSetSession('mds_shopping_cart_total', $cartTotal);
        } else {
            return $cartTotal;
        }
    }

    //calculate total vat
    public function calculateTotalVat($price, $vatRate, $quantity)
    {
        $vat = 0;
        if (!empty($price)) {
            $vat = (($price * $vatRate) / 100) * $quantity;
            if (filter_var($vat, FILTER_VALIDATE_INT) === false) {
                $vat = number_format($vat, 2, '.', '');
            }
        }
        return $vat;
    }

    //check cart has physical products
    public function checkCartHasPhysicalProduct()
    {
        $cartItems = $this->sessionCartItems;
        if (!empty($cartItems)) {
            foreach ($cartItems as $cartItem) {
                if ($cartItem->product_type == 'physical') {
                    return true;
                }
            }
        }
        return false;
    }

    //check cart has digital products
    public function checkCartHasDigitalProduct()
    {
        $cartItems = $this->sessionCartItems;
        if (!empty($cartItems)) {
            foreach ($cartItems as $cartItem) {
                if ($cartItem->product_type == 'digital') {
                    return true;
                }
            }
        }
        return false;
    }

    //validate cart
    public function validateCart()
    {
        $cartTotal = $this->getSessCartTotal();
        if (!empty($cartTotal)) {
            if ($cartTotal->total <= 0 || $cartTotal->is_stock_available != 1) {
                redirectToUrl(generateUrl('cart'));
            }
        }
    }

    //get cart total session
    public function getSessCartTotal()
    {
        $cartTotal = new \stdClass();
        if (!empty(helperGetSession('mds_shopping_cart_total'))) {
            $cartTotal = helperGetSession('mds_shopping_cart_total');
        }
        return $cartTotal;
    }

    //set cart payment method option session
    public function setSessCartPaymentMethod()
    {
        $std = new \stdClass();
        $std->payment_option = inputPost('payment_option');
        $std->terms_conditions = inputPost('terms_conditions');
        helperSetSession('mds_cart_payment_method', $std);
    }

    //get cart payment method option session
    public function getSessCartPaymentMethod()
    {
        if (!empty(helperGetSession('mds_cart_payment_method'))) {
            return helperGetSession('mds_cart_payment_method');
        }
    }

    //unset cart items session
    public function unsetSessCartItems()
    {
        if (!empty(helperGetSession('mds_shopping_cart'))) {
            helperDeleteSession('mds_shopping_cart');
        }
    }

    //unset cart total
    public function unsetSessCartTotal()
    {
        if (!empty(helperGetSession('mds_shopping_cart_total'))) {
            helperDeleteSession('mds_shopping_cart_total');
        }
    }

    //unset cart payment method option session
    public function unsetSessCartPaymentMethod()
    {
        if (!empty(helperGetSession('mds_cart_payment_method'))) {
            helperDeleteSession('mds_cart_payment_method');
        }
    }

    //get cart total by currency
    public function getCartTotalByCurrency($currency)
    {
        $cart = array();
        $newCart = array();
        $this->cartProductIds = array();
        if (!empty(helperGetSession('mds_shopping_cart'))) {
            $cart = helperGetSession('mds_shopping_cart');
        }
        foreach ($cart as $cartItem) {
            $product = getActiveProduct($cartItem->product_id);
            if (!empty($product)) {
                //if purchase type is bidding
                if ($cartItem->purchase_type == 'bidding') {
                    $biddingModel = new BiddingModel();
                    $quoteRequest = $biddingModel->getQuoteRequest($cartItem->quote_request_id);
                    if (!empty($quoteRequest) && $quoteRequest->status == 'pending_payment') {
                        $priceOffered = getPrice($quoteRequest->price_offered, 'decimal');
                        //convert currency
                        if (!empty($currency)) {
                            $priceOffered = convertCurrencyByExchangeRate($priceOffered, $currency->exchange_rate);
                        }
                        $item = new \stdClass();
                        $item->purchase_type = $cartItem->purchase_type;
                        $item->quantity = $cartItem->quantity;
                        $item->unit_price = $priceOffered / $quoteRequest->product_quantity;
                        $item->total_price = $priceOffered;
                        $item->discount_rate = 0;
                        $item->product_vat = 0;
                        $item->is_stock_available = $cartItem->is_stock_available;
                        array_push($newCart, $item);
                    }
                } else {
                    $object = $this->getProductPriceAndStock($product, $cartItem->product_title, $cartItem->options_array);
                    //convert currency
                    if (!empty($currency)) {
                        $object->price_calculated = convertCurrencyByExchangeRate($object->price_calculated, $currency->exchange_rate);
                    }
                    $item = new \stdClass();
                    $item->purchase_type = $cartItem->purchase_type;
                    $item->quantity = $cartItem->quantity;
                    $item->unit_price = $object->price_calculated;
                    $item->total_price = $object->price_calculated * $cartItem->quantity;
                    $item->discount_rate = $object->discount_rate;
                    $item->product_vat = $this->calculateTotalVat($object->price_calculated, $product->vat_rate, $cartItem->quantity);
                    $item->is_stock_available = $cartItem->is_stock_available;
                    array_push($newCart, $item);
                }
            }
        }
        return $this->calculateCartTotal($newCart, $currency->code, false);
    }

    //convert currency by payment gateway
    public function convertCurrencyByPaymentGateway($total, $paymentType)
    {
        $data = new \stdClass();
        $data->total = $total;
        $data->currency = $this->selectedCurrency->code;
        $paymentMethod = $this->getSessCartPaymentMethod();
        if ($this->paymentSettings->currency_converter != 1) {
            return $data;
        }
        if (empty($paymentMethod)) {
            return $data;
        }
        if (empty($paymentMethod->payment_option) || $paymentMethod->payment_option == 'bank_transfer' || $paymentMethod->payment_option == 'cash_on_delivery') {
            return $data;
        }
        $paymentGateway = getPaymentGateway($paymentMethod->payment_option);
        if (!empty($paymentGateway)) {
            if (empty($paymentGateway->base_currency) || $paymentGateway->base_currency == "all") {
                $newCurrency = $this->selectedCurrency;
            } else {
                $newCurrency = getCurrencyByCode($paymentGateway->base_currency);
            }
            if ($paymentType == 'sale') {
                if ($paymentGateway->base_currency != $this->selectedCurrency->code && $paymentGateway->base_currency != 'all') {
                    if (!empty($newCurrency)) {
                        $newTotal = $this->getCartTotalByCurrency($newCurrency);
                        if (!empty($newTotal)) {
                            $data->total = $newTotal->total;
                            $data->currency = $newCurrency->code;
                        }
                    }
                }
            } elseif ($paymentType == 'membership') {
                $total = getPrice($total, 'decimal');
                $newTotal = convertCurrencyByExchangeRate($total, $newCurrency->exchange_rate);
                if (!empty($newTotal)) {
                    $data->total = $newTotal;
                    $data->currency = $newCurrency->code;
                }
            } elseif ($paymentType == 'promote') {
                $newTotal = convertCurrencyByExchangeRate($total, $newCurrency->exchange_rate);
                if (!empty($newTotal)) {
                    $data->total = $newTotal;
                    $data->currency = $newCurrency->code;
                }
            }
        }
        return $data;
    }

    //apply coupon
    public function applyCoupon($couponCode, $cartItems)
    {
        $couponModel = new CouponModel();
        $couponCode = removeSpecialCharacters($couponCode);
        if ($this->verifyCouponCode($couponCode, true)) {
            helperSetSession('mds_cart_coupon_code', $couponCode);
            return true;
        }
        return false;
    }

    //get coupon discount rate
    public function calculateCouponDiscount($cartItems)
    {
        $couponCode = '';
        $totalDiscount = 0;
        $discountRate = 0;
        $sellerId = 0;
        $productIds = '';
        if (!empty(helperGetSession('mds_cart_coupon_code'))) {
            $couponCode = helperGetSession('mds_cart_coupon_code');
        }
        if (!empty($couponCode)) {
            $coupon = $this->verifyCouponCode($couponCode, false);
            if (!empty($coupon)) {
                $sellerId = $coupon->seller_id;
                if (!empty($coupon) && !empty($coupon->product_ids)) {
                    $discountRate = $coupon->discount_rate;
                    $ids_array = explode(',', $coupon->product_ids);
                    if (!empty($ids_array) && is_array($ids_array) && countItems($ids_array) > 0) {
                        if (!empty($cartItems)) {
                            foreach ($cartItems as $cartItem) {
                                if (!empty($cartItem->product_id) && in_array($cartItem->product_id, $ids_array)) {
                                    $productIds .= $cartItem->product_id . ',';
                                    $discount = ($cartItem->total_price * $coupon->discount_rate) / 100;
                                    $discount = number_format($discount, 2, ".", "");
                                    $totalDiscount += $discount;
                                }
                            }
                        }
                    }
                }
            }
        }
        return ['discount_rate' => $discountRate, 'total_discount' => $totalDiscount, 'seller_id' => $sellerId, 'product_ids' => $productIds];
    }

    //verify coupon code
    public function verifyCouponCode($couponCode, $setMessage)
    {
        $couponModel = new CouponModel();
        $coupon = $couponModel->getCouponByCodeCart($couponCode);
        if (!empty($coupon)) {
            if (date('Y-m-d H:i:s') > $coupon->expiry_date) {
                $this->removeCoupon();
                if ($setMessage) {
                    $this->session->setFlashdata('error_coupon_code', trans("msg_invalid_coupon"));
                }
                return false;
            }
            if ($coupon->coupon_count <= $coupon->used_coupon_count) {
                $this->removeCoupon();
                if ($setMessage) {
                    $this->session->setFlashdata('error_coupon_code', trans("msg_coupon_limit"));
                }
                return false;
            }
            if ($coupon->coupon_count <= $coupon->used_coupon_count) {
                $this->removeCoupon();
                if ($setMessage) {
                    $this->session->setFlashdata('error_coupon_code', trans("msg_coupon_limit"));
                }
                return false;
            }
            if ($coupon->usage_type == 'single') {
                if (!authCheck()) {
                    $this->removeCoupon();
                    if ($setMessage) {
                        $this->session->setFlashdata('error_coupon_code', trans("msg_coupon_auth"));
                    }
                    return false;
                }
                if ($couponModel->isCouponUsed(user()->id, $couponCode) > 0) {
                    $this->removeCoupon();
                    if ($setMessage) {
                        $this->session->setFlashdata('error_coupon_code', trans("msg_coupon_used"));
                    }
                    return false;
                }
            }
            $cartTotal = $this->getSessCartTotal();
            $sellerCartTotal = 0;
            $cartItems = $this->sessionCartItems;
            if (!empty($cartItems)) {
                foreach ($cartItems as $cartItem) {
                    if ($cartItem->seller_id == $coupon->seller_id) {
                        $sellerCartTotal += $cartItem->total_price;
                    }
                }
            }
            $minAmount = getPrice($coupon->minimum_order_amount, 'decimal');
            $minAmount = priceDecimal($minAmount, $cartTotal->currency, true, false);
            if ($sellerCartTotal < $minAmount) {
                $this->removeCoupon();
                if ($setMessage) {
                    $this->session->setFlashdata('error_coupon_code', trans("msg_coupon_cart_total") . " " . priceCurrencyFormat($minAmount, $cartTotal->currency));
                }
                return false;
            }
            return $coupon;
        }
        $this->removeCoupon();
        if ($setMessage) {
            $this->session->setFlashdata('error_coupon_code', trans("msg_invalid_coupon"));
        }
        return false;
    }

    //set shipping address
    public function setShippingAddress($totalCost)
    {
        $isSame = inputPost('use_same_address_for_billing');
        $data = new \stdClass();
        $data->totalCost = $totalCost;
        $data->useSameAddressForBilling = $isSame;
        if (authCheck()) {
            $profileModel = new ProfileModel();
            $sAddressId = inputPost('shipping_address_id');
            $bAddressId = inputPost('billing_address_id');
            $sAddress = $profileModel->getShippingAddressById($sAddressId, user()->id);
            $bAddress = $profileModel->getShippingAddressById($bAddressId, user()->id);
            $data->isGuest = 0;
            if (!empty($sAddress)) {
                $country = getCountry($sAddress->country_id);
                $state = getState($sAddress->state_id);
                $data->sTitle = $sAddress->title;
                $data->sFirstName = $sAddress->first_name;
                $data->sLastName = $sAddress->last_name;
                $data->sEmail = $sAddress->email;
                $data->sPhoneNumber = $sAddress->phone_number;
                $data->sAddress = $sAddress->address;
                $data->sCountry = !empty($country) ? $country->name : '';
                $data->sState = !empty($state) ? $state->name : '';
                $data->sCity = $sAddress->city;
                $data->sZipCode = $sAddress->zip_code;
            }
            if (!empty($bAddress)) {
                $country = getCountry($bAddress->country_id);
                $state = getState($bAddress->state_id);
                $data->bTitle = $bAddress->title;
                $data->bFirstName = $bAddress->first_name;
                $data->bLastName = $bAddress->last_name;
                $data->bEmail = $bAddress->email;
                $data->bPhoneNumber = $bAddress->phone_number;
                $data->bAddress = $bAddress->address;
                $data->bCountry = !empty($country) ? $country->name : '';
                $data->bState = !empty($state) ? $state->name : '';
                $data->bCity = $bAddress->city;
                $data->bZipCode = $bAddress->zip_code;
            }
        } else {
            $sCountry = getCountry(inputPost('shipping_country_id'));
            $sState = getState(inputPost('shipping_state_id'));
            $bCountry = $sCountry;
            $bState = $sState;
            if (!$isSame) {
                $bCountry = getCountry(inputPost('billing_country_id'));
                $bState = getState(inputPost('billing_state_id'));
            }
            $data->isGuest = 1;
            $data->sTitle = 'Main';
            $data->sFirstName = inputPost('shipping_first_name');
            $data->sLastName = inputPost('shipping_last_name');
            $data->sEmail = inputPost('shipping_email');
            $data->sPhoneNumber = inputPost('shipping_phone_number');
            $data->sAddress = inputPost('shipping_address');
            $data->sCountry = !empty($sCountry) ? $sCountry->name : '';
            $data->sState = !empty($sState) ? $sState->name : '';
            $data->sCity = inputPost('shipping_city');
            $data->sZipCode = inputPost('shipping_zip_code');
            $data->bTitle = 'Main';
            $data->bFirstName = $isSame ? $data->sFirstName : inputPost('billing_first_name');
            $data->bLastName = $isSame ? $data->sLastName : inputPost('billing_last_name');
            $data->bEmail = $isSame ? $data->sEmail : inputPost('billing_email');
            $data->bPhoneNumber = $isSame ? $data->sPhoneNumber : inputPost('billing_phone_number');
            $data->bAddress = $isSame ? $data->sAddress : inputPost('billing_address');
            $data->bCountry = !empty($bCountry) ? $bCountry->name : '';
            $data->bState = !empty($bState) ? $bState->name : '';
            $data->bCity = $isSame ? $data->sCity : inputPost('billing_city');
            $data->bZipCode = $isSame ? $data->sZipCode : inputPost('billing_zip_code');
        }
        helperSetSession('mds_cart_shipping', $data);
    }

    //remove coupon
    public function removeCoupon()
    {
        if (!empty(helperGetSession('mds_cart_coupon_code'))) {
            helperDeleteSession('mds_cart_coupon_code');
        }
    }

    //clear cart
    public function clearCart()
    {
        $this->unsetSessCartItems();
        $this->unsetSessCartTotal();
        $this->unsetSessCartPaymentMethod();
        if (!empty(helperGetSession('mds_shopping_cart_final'))) {
            helperDeleteSession('mds_shopping_cart_final');
        }
        if (!empty(helperGetSession('mds_shopping_cart_total_final'))) {
            helperDeleteSession('mds_shopping_cart_total_final');
        }
        if (!empty(helperGetSession('mds_cart_shipping'))) {
            helperDeleteSession('mds_cart_shipping');
        }
        $this->removeCoupon();
    }
}
