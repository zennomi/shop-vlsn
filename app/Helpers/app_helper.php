<?php

use Config\Globals;

//get default language id
if (!function_exists('defaultLangId')) {
    function defaultLangId()
    {
        if (!empty(Globals::$defaultLang)) {
            return Globals::$defaultLang->id;
        }
        return 0;
    }
}

//get active language id
if (!function_exists('selectedLangId')) {
    function selectedLangId()
    {
        if (!empty(Globals::$activeLang)) {
            return Globals::$activeLang->id;
        }
        return 0;
    }
}

//check language exist
if (!function_exists('checkLanguageExist')) {
    function checkLanguageExist($langId)
    {
        if (!empty(Globals::$languages)) {
            foreach (Globals::$languages as $language) {
                if ($langId == $language->id) {
                    return true;
                }
            }
        }
        return false;
    }
}

//get user avatar
if (!function_exists('getUserAvatar')) {
    function getUserAvatar($user)
    {
        if (!empty($user)) {
            if (!empty($user->avatar) && file_exists(FCPATH . $user->avatar)) {
                return base_url($user->avatar);
            } elseif (!empty($user->avatar) && $user->user_type != 'registered') {
                return $user->avatar;
            }
        }
        return base_url('assets/img/user.png');
    }
}

//get user avatar by id
if (!function_exists('getUserAvatarById')) {
    function getUserAvatarById($userId)
    {
        $user = getUser($userId);
        if (!empty($user)) {
            if (!empty($user->avatar) && file_exists(FCPATH . $user->avatar)) {
                return base_url($user->avatar);
            } elseif (!empty($user->avatar) && $user->user_type != 'registered') {
                return $user->avatar;
            }
        }
        return base_url('assets/img/user.png');
    }
}

//get user avatar by image url
if (!function_exists('getUserAvatarByImageURL')) {
    function getUserAvatarByImageURL($imageURL, $userType)
    {
        if (!empty($imageURL)) {
            if ($userType != 'registered') {
                return $imageURL;
            } else {
                return base_url($imageURL);
            }
        }
        return base_url('assets/img/user.png');
    }
}

//get page by default name
if (!function_exists('getPageByDefaultName')) {
    function getPageByDefaultName($defaultName, $langId)
    {
        $model = new \App\Models\PageModel();
        return $model->getPageByDefaultName($defaultName, $langId);
    }
}

//get continents
if (!function_exists('getContinents')) {
    function getContinents()
    {
        return array('EU' => 'Europe', 'AS' => 'Asia', 'AF' => 'Africa', 'NA' => 'North America', 'SA' => 'South America', 'OC' => 'Oceania', 'AN' => 'Antarctica');
    }
}

//get continent name by key
if (!function_exists('getContinentNameByKey')) {
    function getContinentNameByKey($continentKey)
    {
        $continents = getContinents();
        if (!empty($continents)) {
            foreach ($continents as $key => $value) {
                if ($key == $continentKey) {
                    return $value;
                }
            }
        }
        return '';
    }
}

//get countries
if (!function_exists('getCountries')) {
    function getCountries()
    {
        $model = new \App\Models\LocationModel();
        return $model->getCountries();
    }
}

//get country
if (!function_exists('getCountry')) {
    function getCountry($id)
    {
        $model = new \App\Models\LocationModel();
        return $model->getCountry($id);
    }
}

//get state
if (!function_exists('getState')) {
    function getState($id)
    {
        $model = new \App\Models\LocationModel();
        return $model->getState($id);
    }
}

//get city
if (!function_exists('getCity')) {
    function getCity($id)
    {
        $model = new \App\Models\LocationModel();
        return $model->getCity($id);
    }
}

//get states by country
if (!function_exists('getStatesByCountry')) {
    function getStatesByCountry($countryId)
    {
        $model = new \App\Models\LocationModel();
        return $model->getStatesByCountry($countryId);
    }
}

//get role
if (!function_exists('getRoleById')) {
    function getRoleById($id)
    {
        $model = new \App\Models\MembershipModel();
        return $model->getRole($id);
    }
}

//get membership plan
if (!function_exists('getMembershipPlan')) {
    function getMembershipPlan($id)
    {
        $model = new \App\Models\MembershipModel();
        return $model->getPlan($id);
    }
}

//get membership plan title
if (!function_exists('getMembershipPlanTitle')) {
    function getMembershipPlanTitle($id)
    {
        $model = new \App\Models\MembershipModel();
        return $model->getMembershipPlanTitle($id);
    }
}

//get membership plan name
if (!function_exists('getMembershipPlanName')) {
    function getMembershipPlanName($titleArray, $langId)
    {
        if (!empty($titleArray)) {
            $array = unserializeData($titleArray);
            if (!empty($array)) {
                $main = '';
                foreach ($array as $item) {
                    if ($item['lang_id'] == $langId) {
                        return $item['title'];
                    }
                    if ($item['lang_id'] == Globals::$generalSettings->site_lang) {
                        $main = $item['title'];
                    }
                }
                return $main;
            }
        }
        return '';
    }
}

//get membership plan features
if (!function_exists('getMembershipPlanFeatures')) {
    function getMembershipPlanFeatures($featuresArray, $langId)
    {
        if (!empty($featuresArray)) {
            $array = unserializeData($featuresArray);
            if (!empty($array)) {
                $main = '';
                foreach ($array as $item) {
                    if ($item['lang_id'] == $langId) {
                        if (!empty($item['features'])) {
                            return $item['features'];
                        }
                    }
                    if ($item['lang_id'] == Globals::$defaultLang->id) {
                        if (!empty($item['features'])) {
                            $main = $item['features'];
                        }
                    }
                }
                return $main;
            }
        }
        return '';
    }
}

//get user payout account
if (!function_exists('getUserPayoutAccount')) {
    function getUserPayoutAccount($userId)
    {
        $model = new \App\Models\EarningsModel();
        return $model->getUserPayoutAccount($userId);
    }
}

//get location
if (!function_exists('getLocation')) {
    function getLocation($object)
    {
        $model = new \App\Models\LocationModel();
        $location = '';
        if (!empty($object)) {
            if (!empty($object->address)) {
                $location = $object->address;
            }
            if (!empty($object->zip_code)) {
                $location .= ' ' . $object->zip_code;
            }
            if (!empty($object->city_id)) {
                $city = $model->getCity($object->city_id);
                if (!empty($city)) {
                    if (!empty($object->address) || !empty($object->zip_code)) {
                        $location .= " ";
                    }
                    $location .= $city->name;
                }
            }
            if (!empty($object->state_id)) {
                $state = $model->getState($object->state_id);
                if (!empty($state)) {
                    if (!empty($object->address) || !empty($object->zip_code) || !empty($object->city_id)) {
                        $location .= ', ';
                    }
                    $location .= $state->name;
                }
            }
            if (!empty($object->country_id)) {
                $country = $model->getCountry($object->country_id);
                if (!empty($country)) {
                    if (!empty($object->state_id) || $object->city_id || !empty($object->address) || !empty($object->zip_code)) {
                        $location .= ', ';
                    }
                    $location .= $country->name;
                }
            }
        }
        return $location;
    }
}

//add to email queue
if (!function_exists('addToEmailQueue')) {
    function addToEmailQueue($data)
    {
        $model = new \App\Models\EmailModel();
        return $model->addToEmailQueue($data);
    }
}

//get order
if (!function_exists('getOrder')) {
    function getOrder($id)
    {
        $model = new \App\Models\OrderModel();
        return $model->getOrder($id);
    }
}

//get order by order number
if (!function_exists('getOrderByOrderNumber')) {
    function getOrderByOrderNumber($orderNumber)
    {
        $model = new \App\Models\OrderModel();
        return $model->getOrderByOrderNumber($orderNumber);
    }
}

//get order product
if (!function_exists('getOrderProduct')) {
    function getOrderProduct($id)
    {
        $model = new \App\Models\OrderModel();
        return $model->getOrderProduct($id);
    }
}

//get earning by order product
if (!function_exists('getEarningByOrderProductId')) {
    function getEarningByOrderProductId($orderProductId, $orderNumber)
    {
        $model = new \App\Models\EarningsModel();
        return $model->getEarningByOrderProductId($orderProductId, $orderNumber);
    }
}

//check if user bought product
if (!function_exists('checkUserBoughtProduct')) {
    function checkUserBoughtProduct($userId, $productId)
    {
        $model = new \App\Models\OrderModel();
        return $model->checkUserBoughtProduct($userId, $productId);
    }
}

//get currency by code
if (!function_exists('getCurrencyByCode')) {
    function getCurrencyByCode($currencyCode)
    {
        if (!empty(Globals::$currencies[$currencyCode])) {
            return Globals::$currencies[$currencyCode];
        }
    }
}

//get currency symbol
if (!function_exists('getCurrencySymbol')) {
    function getCurrencySymbol($currencyCode)
    {
        if (!empty(Globals::$currencies)) {
            if (isset(Globals::$currencies[$currencyCode])) {
                return Globals::$currencies[$currencyCode]->symbol;
            }
        }
        return '';
    }
}

//get shipping locations by zone
if (!function_exists('getShippingLocationsByZone')) {
    function getShippingLocationsByZone($zoneId)
    {
        $model = new \App\Models\ShippingModel();
        return $model->getShippingLocationsByZone($zoneId);
    }
}

//get shipping payment methods by zone
if (!function_exists('getShippingPaymentMethodsByZone')) {
    function getShippingPaymentMethodsByZone($zoneId)
    {
        $model = new \App\Models\ShippingModel();
        return $model->getShippingPaymentMethodsByZone($zoneId);
    }
}

//get shipping methods
if (!function_exists('getShippingMethods')) {
    function getShippingMethods()
    {
        return ['flat_rate', 'local_pickup', 'free_shipping'];
    }
}

//get shipping val
if (!function_exists('getShippingVal')) {
    function getShippingVal($var)
    {

    }
}

//get shipping class cost by method
if (!function_exists('getShippingClassCostByMethod')) {
    function getShippingClassCostByMethod($costArray, $classId)
    {
        if (!empty($costArray) && !empty($classId)) {
            $model = new \App\Models\ShippingModel();
            $shippingClass = $model->getShippingClass($classId);
            if (!empty($shippingClass) && $shippingClass->status == 1) {
                $costArray = unserializeData($costArray);
                if (!empty($costArray)) {
                    foreach ($costArray as $item) {
                        if ($item['class_id'] == $classId && !empty($item['cost'])) {
                            return esc($item['cost']);
                        }
                    }
                }
            }
        }
    }
}

//get used coupons count
if (!function_exists('getUsedCouponsCount')) {
    function getUsedCouponsCount($couponCode)
    {
        $model = new \App\Models\CouponModel();
        return $model->getUsedCouponsCount($couponCode);
    }
}

//get subcategories
if (!function_exists('getSubCategories')) {
    function getSubCategories($parentId)
    {
        $model = new \App\Models\CategoryModel();
        return $model->getSubCategoriesByParentId($parentId);
    }
}

//get coupon products by category
if (!function_exists('getCouponProductsByCategory')) {
    function getCouponProductsByCategory($userId, $categoryId)
    {
        $model = new \App\Models\CouponModel();
        return $model->getCouponProductsByCategory($userId, $categoryId);
    }
}

//get user plan
if (!function_exists('getUserPlanByUserId')) {
    function getUserPlanByUserId($userId)
    {
        $model = new \App\Models\MembershipModel();
        return $model->getUserPlanByUserId($userId);
    }
}

//calculate user rating
if (!function_exists('calculateUserRating')) {
    function calculateUserRating($userId)
    {
        $model = new \App\Models\CommonModel();
        return $model->calculateUserRating($userId);
    }
}

//get user drafts count
if (!function_exists('getUserDownloadsCount')) {
    function getUserDownloadsCount($userId)
    {
        $model = new \App\Models\ProductModel();
        return $model->getUserDownloadsCount($userId);
    }
}

//get followers count
if (!function_exists('getFollowersCount')) {
    function getFollowersCount($followingId)
    {
        $model = new \App\Models\ProfileModel();
        return $model->getFollowersCount($followingId);
    }
}

//get following users count
if (!function_exists('getFollowingUsersCount')) {
    function getFollowingUsersCount($followerId)
    {
        $model = new \App\Models\ProfileModel();
        return $model->getFollowingUsersCount($followerId);
    }
}

if (!function_exists('isUserOnline')) {
    function isUserOnline($timestamp)
    {
        $timeAgo = strtotime($timestamp);
        $currentTime = time();
        $timeDifference = $currentTime - $timeAgo;
        $seconds = $timeDifference;
        $minutes = round($seconds / 60);
        if ($minutes <= 2) {
            return true;
        } else {
            return false;
        }
    }
}

//check user follows
if (!function_exists('isUserFollows')) {
    function isUserFollows($followingId, $followerId)
    {
        $model = new \App\Models\ProfileModel();
        return $model->isUserFollows($followingId, $followerId);
    }
}

//get review
if (!function_exists('getReview')) {
    function getReview($productId, $userId)
    {
        $model = new \App\Models\CommonModel();
        return $model->getReview($productId, $userId);
    }
}

//get subcomments
if (!function_exists('getSubComments')) {
    function getSubComments($parentId)
    {
        $model = new \App\Models\CommonModel();
        return $model->getSubComments($parentId);
    }
}

//get digital sale by buyer id
if (!function_exists('getDigitalSaleByBuyerId')) {
    function getDigitalSaleByBuyerId($buyerId, $productId)
    {
        $model = new \App\Models\ProductModel();
        return $model->getDigitalSaleByBuyerId($buyerId, $productId);
    }
}

//get digital sale by order id
if (!function_exists('getDigitalSaleByOrderId')) {
    function getDigitalSaleByOrderId($buyerId, $productId, $orderId)
    {
        $model = new \App\Models\ProductModel();
        return $model->getDigitalSaleByOrderId($buyerId, $productId, $orderId);
    }
}

//get order products
if (!function_exists('getOrderProducts')) {
    function getOrderProducts($orderId)
    {
        $model = new \App\Models\OrderModel();
        return $model->getOrderProducts($orderId);
    }
}

//cart discount coupon
if (!function_exists('getCartDiscountCoupon')) {
    function getCartDiscountCoupon()
    {
        if (!empty(helperGetSession('mds_cart_coupon_code'))) {
            return helperGetSession('mds_cart_coupon_code');
        }
    }
}

//get payment gateway
if (!function_exists('getPaymentGateway')) {
    function getPaymentGateway($nameKey)
    {
        $model = new \App\Models\SettingsModel();
        return $model->getPaymentGateway($nameKey);
    }
}

//get payment method
if (!function_exists('getPaymentMethod')) {
    function getPaymentMethod($paymentMethod)
    {
        if ($paymentMethod == 'Bank Transfer') {
            return trans("bank_transfer");
        } elseif ($paymentMethod == 'Cash On Delivery') {
            return trans("cash_on_delivery");
        } else {
            return $paymentMethod;
        }
    }
}

//get payment status
if (!function_exists('getPaymentStatus')) {
    function getPaymentStatus($paymentStatus)
    {
        if ($paymentStatus == "payment_received") {
            return trans("payment_received");
        } elseif ($paymentStatus == "awaiting_payment") {
            return trans("awaiting_payment");
        } elseif ($paymentStatus == "Completed") {
            return trans("completed");
        } else {
            return $paymentStatus;
        }
    }
}

//get active payment gateways
if (!function_exists('getActivePaymentGateways')) {
    function getActivePaymentGateways()
    {
        $model = new \App\Models\SettingsModel();
        return $model->getActivePaymentGateways();
    }
}

//get transaction by order id
if (!function_exists('getTransactionByOrderId')) {
    function getTransactionByOrderId($orderId)
    {
        $model = new \App\Models\OrderAdminModel();
        return $model->getTransactionByOrderId($orderId);
    }
}

//get cart customer data
if (!function_exists('getCartCustomerData')) {
    function getCartCustomerData()
    {
        $user = null;
        if (authCheck()) {
            $user = user();
        } else {
            $user = new stdClass();
            $user->id = 0;
            $user->first_name = '';
            $user->last_name = '';
            $user->email = "unknown@domain.com";
            $user->phone_number = "11111111";
            $cartShipping = helperGetSession('mds_cart_shipping');
            if (!empty($cartShipping)) {
                if (!empty($cartShipping->guest_shipping_address)) {
                    if (!empty($cartShipping->guest_shipping_address['first_name'])) {
                        $user->first_name = $cartShipping->guest_shipping_address['first_name'];
                    }
                    if (!empty($cartShipping->guest_shipping_address['last_name'])) {
                        $user->last_name = $cartShipping->guest_shipping_address['last_name'];
                    }
                    if (!empty($cartShipping->guest_shipping_address['email'])) {
                        $user->email = $cartShipping->guest_shipping_address['email'];
                    }
                    if (!empty($cartShipping->guest_shipping_address['phone_number'])) {
                        $user->phone_number = $cartShipping->guest_shipping_address['phone_number'];
                    }
                }
            }
        }
        return $user;
    }
}



