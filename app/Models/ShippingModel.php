<?php namespace App\Models;

use CodeIgniter\Model;

class ShippingModel extends BaseModel
{
    protected $builderZones;
    protected $builderZoneLocations;
    protected $builderZoneMethods;
    protected $builderClasses;
    protected $buildersDeliveryTimes;

    public function __construct()
    {
        parent::__construct();
        $this->builderZones = $this->db->table('shipping_zones');
        $this->builderZoneLocations = $this->db->table('shipping_zone_locations');
        $this->builderZoneMethods = $this->db->table('shipping_zone_methods');
        $this->builderClasses = $this->db->table('shipping_classes');
        $this->buildersDeliveryTimes = $this->db->table('shipping_delivery_times');
    }

    /*
     * --------------------------------------------------------------------
     * Cart
     * --------------------------------------------------------------------
     */

    //get seller shipping methods array
    public function getSellerShippingMethodsArray($cartItems, $stateId, $setSession = true)
    {
        //calculate total for each seller
        $sellerTotal = array();
        $sellerIds = array();
        if (!empty($cartItems)) {
            foreach ($cartItems as $item) {
                if ($item->product_type == 'physical') {
                    if (!isset($sellerTotal[$item->seller_id])) {
                        $sellerTotal[$item->seller_id] = 0;
                    }
                    $sellerTotal[$item->seller_id] += $item->total_price;
                    if (!in_array($item->seller_id, $sellerIds)) {
                        array_push($sellerIds, $item->seller_id);
                    }
                }
            }
        }
        //get shipping methods by seller
        $sellerShippingMethods = array();
        $arrayShippingCost = array();
        if (!empty($sellerIds)) {
            foreach ($sellerIds as $sellerId) {
                $seller = getUser($sellerId);
                if (!empty($seller)) {
                    $item = new \stdClass();
                    $item->shop_id = $seller->id;
                    $item->total_shipping_cost = 0;
                    $item->username = getUsername($seller);
                    $item->methods = array();
                    $shippingMethods = $this->getCartShippingMethods($seller->id, $stateId);
                    if (!empty($shippingMethods)) {
                        foreach ($shippingMethods as $shippingMethod) {
                            $method = new \stdClass();
                            $method->id = $shippingMethod->id;
                            $method->method_type = $shippingMethod->method_type;
                            $method->name = @parseSerializedNameArray($shippingMethod->name_array, selectedLangId());
                            $method->is_selected = 0;
                            $method->is_free_shipping = 0;
                            $method->free_shipping_min_amount = 0;
                            $method->cost = null;
                            //calculate shipping cost
                            $freeShippingMinAmount = getPrice($shippingMethod->free_shipping_min_amount, 'decimal');
                            $localPickupCost = getPrice($shippingMethod->local_pickup_cost, 'decimal');
                            if ($shippingMethod->method_type == 'free_shipping') {
                                if (isset($sellerTotal[$seller->id])) {
                                    $total = $sellerTotal[$seller->id];
                                    if ($total >= $freeShippingMinAmount) {
                                        $method->is_free_shipping = 1;
                                        $method->free_shipping_min_amount = $freeShippingMinAmount;
                                    }
                                }
                            } elseif ($shippingMethod->method_type == 'local_pickup') {
                                $method->cost = $localPickupCost;
                            } elseif ($shippingMethod->method_type == 'flat_rate') {
                                $method->cost = $this->getFlatRateCost($shippingMethod, $cartItems, $sellerId);
                            }
                            if (!empty($method->cost)) {
                                $method->cost = number_format($method->cost, 2, '.', '');
                            }
                            //add shipping cost
                            $arrayShippingCost[$method->id] = $method->cost;
                            if ($setSession == true) {
                                helperSetSession('mds_array_shipping_cost', $arrayShippingCost);
                                helperSetSession('mds_array_cart_seller_ids', $sellerIds);
                            }
                            array_push($item->methods, $method);
                        }
                    }
                    array_push($sellerShippingMethods, $item);
                }
            }
        }
        //set selected shipping methods
        $totalShippingCost = 0;
        if (!empty($sellerShippingMethods)) {
            foreach ($sellerShippingMethods as $item) {
                if (!empty($item->methods)) {
                    $i = 0;
                    foreach ($item->methods as $method) {
                        if ($i == 0) {
                            if ($method->method_type == 'free_shipping') {
                                if ($method->is_free_shipping == 1) {
                                    $method->is_selected = 1;
                                    $i++;
                                }
                            } else {
                                $method->is_selected = 1;
                                $totalShippingCost += $method->cost;
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        return $sellerShippingMethods;
    }

    //get cart shipping methods
    public function getCartShippingMethods($sellerId, $stateId)
    {
        $continentCode = '';
        $countryId = '';
        //get the state
        $state = getState($stateId);
        if (!empty($state)) {
            //get country
            $country = getCountry($state->country_id);
            if (!empty($country)) {
                $countryId = $country->id;
                $continentCode = $country->continent_code;
            }
            //get shipping options by state
            $zoneLocations = array();
            $zoneIds = array();
            if (!empty($state->id)) {
                $zoneLocations = $this->builderZoneLocations->where('state_id', clrNum($state->id))->where('user_id', clrNum($sellerId))->get()->getResult();
            }
            //get shipping options by country
            if (empty($zoneLocations) && countItems($zoneLocations) < 1 && !empty($countryId)) {
                $zoneLocations = $this->builderZoneLocations->where('country_id', clrNum($countryId))->where('state_id', 0)->where('user_id', clrNum($sellerId))->get()->getResult();
            }
            //get shipping options by continent
            if (empty($zoneLocations) && countItems($zoneLocations) < 1 && !empty($continentCode)) {
                $zoneLocations = $this->builderZoneLocations->where('continent_code', cleanStr($continentCode))->where('country_id', 0)->where('state_id', 0)->where('user_id', clrNum($sellerId))->get()->getResult();
            }
            if (!empty($zoneLocations)) {
                foreach ($zoneLocations as $location) {
                    array_push($zoneIds, $location->zone_id);
                }
            }
            //get shipping methods
            if (!empty($zoneIds)) {
                return $this->builderZoneMethods->whereIn('zone_id', $zoneIds, FALSE)->where('user_id', clrNum($sellerId))->where('status', 1)->orderBy("FIELD(method_type, 'free_shipping', 'local_pickup', 'flat_rate')")->get()->getResult();
            }
        }
        return array();
    }

    //calculate cart shipping total cost
    public function calculateCartShippingTotalCost()
    {
        $result = [
            'is_valid' => 1,
            'total_cost' => 0
        ];
        $arrayShippingCost = helperGetSession('mds_array_shipping_cost');
        $arrayCartSellerIds = helperGetSession('mds_array_cart_seller_ids');
        $arraySellerShippingCosts = array();
        $selectedShippingMethodIds = array();
        if (!empty($arrayCartSellerIds)) {
            foreach ($arrayCartSellerIds as $sellerId) {
                $methodId = inputPost('shipping_method_' . $sellerId);
                if (!empty($methodId)) {
                    $cost = 0;
                    if (!array_key_exists($methodId, $arrayShippingCost)) {
                        $result['is_valid'] = 0;
                    }
                    if (isset($arrayShippingCost[$methodId])) {
                        $cost = $arrayShippingCost[$methodId];
                        $result['total_cost'] += $cost;
                    }
                    array_push($selectedShippingMethodIds, $methodId);
                    $item = new \stdClass();
                    $item->cost = $cost;
                    $item->shipping_method_id = $methodId;
                    $arraySellerShippingCosts[$sellerId] = $item;
                }
            }
        }
        helperSetSession('mds_selected_shipping_method_ids', $selectedShippingMethodIds);
        helperSetSession('mds_seller_shipping_costs', $arraySellerShippingCosts);
        return $result;
    }

    //get flat rate cost
    public function getFlatRateCost($shippingMethod, $cartItems, $sellerId)
    {
        $totalCost = 0;
        if (!empty($shippingMethod)) {
            $items = array();
            if (!empty($cartItems)) {
                foreach ($cartItems as $cartItem) {
                    if ($cartItem->seller_id == $sellerId && $cartItem->product_type == 'physical') {
                        $cost = $shippingMethod->flat_rate_cost;
                        if (!empty($cartItem->shipping_class_id)) {
                            $classCost = getShippingClassCostByMethod($shippingMethod->flat_rate_class_costs_array, $cartItem->shipping_class_id);
                            if (!empty($classCost)) {
                                $cost = $classCost;
                            }
                        }
                        if ($shippingMethod->flat_rate_cost_calculation_type == 'each_product') {
                            $totalCost += $cost * $cartItem->quantity;
                        } elseif ($shippingMethod->flat_rate_cost_calculation_type == 'each_different_product') {
                            $totalCost += $cost;
                        } elseif ($shippingMethod->flat_rate_cost_calculation_type == 'cart_total') {
                            if ($cost > $totalCost) {
                                $totalCost = $cost;
                            }
                        }
                    }
                }
            }
        }
        if (!empty($totalCost)) {
            $totalCost = getPrice($totalCost, 'decimal');
        }
        return $totalCost;
    }

    //get product shipping cost
    public function getProductShippingCost($stateId, $productId)
    {
        $product = getProduct($productId);
        if (!empty($product)) {
            $items = array();
            $item = new \stdClass();
            $item->product_id = $product->id;
            $item->product_type = $product->product_type;
            $item->quantity = 1;
            $item->total_price = $product->price;
            $item->seller_id = $product->user_id;
            $item->shipping_class_id = $product->shipping_class_id;
            array_push($items, $item);
            $shippingMethods = $this->getSellerShippingMethodsArray($items, $stateId, false);

            $hasMethods = false;
            if (!empty($shippingMethods)) {
                foreach ($shippingMethods as $shippingMethod) {
                    if (!empty($shippingMethod->methods) && countItems($shippingMethod->methods) > 0) {
                        $hasMethods = true;
                    }
                }
            }
            $response = '';
            if (!empty($shippingMethods)) {
                foreach ($shippingMethods as $shippingMethod) {
                    if (!empty($shippingMethod->methods)) {
                        foreach ($shippingMethod->methods as $method) {
                            if ($method->method_type == 'free_shipping') {
                                $response .= "<p><strong class='method-name'>" . esc($method->name) . "</strong><strong>&nbsp(" . trans("minimum_order_amount") . ":&nbsp;" . priceDecimal($method->free_shipping_min_amount, getSelectedCurrency()->code, true) . ")</strong></p>";
                            } else {
                                $response .= "<p><strong class='method-name'>" . esc($method->name) . "</strong><strong>:&nbsp;" . priceDecimal($method->cost, getSelectedCurrency()->code, true) . "</strong></p>";
                            }
                        }
                    }
                }
            }
            if (empty($response)) {
                $response = '<p class="text-muted">' . trans("product_does_not_ship_location") . '</p>';
            }
            $data = [
                'result' => 1,
                'response' => $response
            ];
            echo json_encode($data);
        }
    }

    /*
     * --------------------------------------------------------------------
     * Dashboard
     * --------------------------------------------------------------------
     */

    //add shipping zone
    public function addShippingZone()
    {
        $nameArray = array();
        foreach ($this->activeLanguages as $language) {
            $item = [
                'lang_id' => $language->id,
                'name' => inputPost('zone_name_lang_' . $language->id)
            ];
            array_push($nameArray, $item);
        }
        $data = [
            'name_array' => serialize($nameArray),
            'user_id' => user()->id
        ];
        if ($this->builderZones->insert($data)) {
            $zoneId = $this->db->insertID();
            //add locations
            $this->addShippingZoneLocations($zoneId);
            //add paymenet methods
            $this->addShippingZonePaymentMethods($zoneId);
            return true;
        }
        return false;
    }

    //add shipping zone locations
    public function addShippingZoneLocations($zoneId)
    {
        $continentCodes = inputPost('continent');
        if (!empty($continentCodes)) {
            foreach ($continentCodes as $continentCode) {
                if (in_array($continentCode, array('EU', 'AS', 'AF', 'NA', 'SA', 'OC', 'AN'))) {
                    //check if already exists
                    $zoneContinent = $this->builderZoneLocations->where('continent_code', cleanStr($continentCode))->where('zone_id', clrNum($zoneId))->get()->getRow();
                    if (empty($zoneContinent)) {
                        $item = [
                            'zone_id' => $zoneId,
                            'user_id' => user()->id,
                            'continent_code' => $continentCode,
                            'country_id' => 0,
                            'state_id' => 0
                        ];
                        $this->builderZoneLocations->insert($item);
                    }
                }
            }
        }
        $countryIds = inputPost('country');
        if (!empty($countryIds)) {
            foreach ($countryIds as $countryId) {
                $country = getCountry($countryId);
                if (!empty($country)) {
                    //check if already exists
                    $zoneCountry = $this->builderZoneLocations->where('country_id', clrNum($countryId))->where('zone_id', clrNum($zoneId))->get()->getRow();
                    if (empty($zoneCountry)) {
                        $item = [
                            'zone_id' => $zoneId,
                            'user_id' => user()->id,
                            'continent_code' => $country->continent_code,
                            'country_id' => $country->id,
                            'state_id' => 0
                        ];
                        $this->builderZoneLocations->insert($item);
                    }
                }
            }
        }
        $stateIds = inputPost('state');
        if (!empty($stateIds)) {
            foreach ($stateIds as $stateId) {
                $state = getState($stateId);
                if (!empty($state)) {
                    $country = getCountry($state->country_id);
                    if (!empty($country)) {
                        //check if already exists
                        $zoneState = $this->builderZoneLocations->where('state_id', clrNum($stateId))->where('zone_id', clrNum($zoneId))->get()->getRow();
                        if (empty($zoneState)) {
                            $item = [
                                'zone_id' => $zoneId,
                                'user_id' => user()->id,
                                'continent_code' => $country->continent_code,
                                'country_id' => $country->id,
                                'state_id' => $state->id
                            ];
                            $this->builderZoneLocations->insert($item);
                        }
                    }
                }
            }
        }
    }

    //add shipping zone payment methods
    public function addShippingZonePaymentMethods($zoneId)
    {
        $optionUniqueIds = inputPost('option_unique_id');
        if (!empty($optionUniqueIds)) {
            foreach ($optionUniqueIds as $optionUniqueId) {
                $nameArray = array();
                foreach ($this->activeLanguages as $language) {
                    $item = [
                        'lang_id' => $language->id,
                        'name' => inputPost('method_name_' . $optionUniqueId . '_lang_' . $language->id)
                    ];
                    array_push($nameArray, $item);
                }
                $data = [
                    'name_array' => serialize($nameArray),
                    'zone_id' => $zoneId,
                    'user_id' => user()->id,
                    'method_type' => inputPost('method_type_' . $optionUniqueId),
                    'flat_rate_cost_calculation_type' => inputPost('flat_rate_cost_calculation_type_' . $optionUniqueId),
                    'flat_rate_cost' => inputPost('flat_rate_cost_' . $optionUniqueId),
                    'local_pickup_cost' => inputPost('local_pickup_cost_' . $optionUniqueId),
                    'free_shipping_min_amount' => inputPost('free_shipping_min_amount_' . $optionUniqueId),
                    'status' => inputPost('status_' . $optionUniqueId)
                ];
                $data['flat_rate_cost_calculation_type'] = !empty($data['flat_rate_cost_calculation_type']) ? $data['flat_rate_cost_calculation_type'] : '';
                $data['flat_rate_cost'] = !empty($data['flat_rate_cost']) ? $data['flat_rate_cost'] : 0;
                $data['local_pickup_cost'] = !empty($data['local_pickup_cost']) ? $data['local_pickup_cost'] : 0;
                $data['free_shipping_min_amount'] = !empty($data['free_shipping_min_amount']) ? $data['free_shipping_min_amount'] : 0;
                $data["flat_rate_cost"] = getPrice($data['flat_rate_cost'], 'database');
                $data["local_pickup_cost"] = getPrice($data['local_pickup_cost'], 'database');
                $data["free_shipping_min_amount"] = getPrice($data['free_shipping_min_amount'], 'database');
                //shipping classes
                $classArray = array();
                $shippingClasses = $this->getActiveShippingClasses(user()->id);
                if (!empty($shippingClasses)) {
                    foreach ($shippingClasses as $shippingClass) {
                        $item = [
                            'class_id' => $shippingClass->id,
                            'cost' => inputPost('flat_rate_cost_' . $optionUniqueId . '_class_' . $shippingClass->id)
                        ];
                        $item['cost'] = getPrice($item['cost'], 'database');
                        array_push($classArray, $item);
                    }
                }
                $data['flat_rate_class_costs_array'] = '';
                if (!empty($classArray)) {
                    $data['flat_rate_class_costs_array'] = serialize($classArray);
                }
                $this->builderZoneMethods->insert($data);
            }
        }
    }

    //edit shipping zone
    public function editShippingZone($zoneId)
    {
        $nameArray = array();
        foreach ($this->activeLanguages as $language) {
            $item = [
                'lang_id' => $language->id,
                'name' => inputPost('zone_name_lang_' . $language->id)
            ];
            array_push($nameArray, $item);
        }
        $data = ['name_array' => serialize($nameArray)];
        if ($this->builderZones->where('id', clrNum($zoneId))->update($data)) {
            //add locations
            $this->addShippingZoneLocations($zoneId);
            //edit paymenet methods
            $this->editShippingZonePaymentMethods($zoneId);
            return true;
        }
        return false;
    }

    //edit shipping zone payment methods
    public function editShippingZonePaymentMethods($zoneId)
    {
        $optionUniqueIds = inputPost('option_unique_id');
        if (!empty($optionUniqueIds)) {
            foreach ($optionUniqueIds as $optionUniqueId) {
                $nameArray = array();
                foreach ($this->activeLanguages as $language) {
                    $item = [
                        'lang_id' => $language->id,
                        'name' => inputPost('method_name_' . $optionUniqueId . '_lang_' . $language->id)
                    ];
                    array_push($nameArray, $item);
                }
                $data = [
                    'name_array' => serialize($nameArray),
                    'zone_id' => $zoneId,
                    'method_type' => inputPost('method_type_' . $optionUniqueId),
                    'flat_rate_cost_calculation_type' => inputPost('flat_rate_cost_calculation_type_' . $optionUniqueId),
                    'flat_rate_cost' => inputPost('flat_rate_cost_' . $optionUniqueId),
                    'local_pickup_cost' => inputPost('local_pickup_cost_' . $optionUniqueId),
                    'free_shipping_min_amount' => inputPost('free_shipping_min_amount_' . $optionUniqueId),
                    'status' => inputPost('status_' . $optionUniqueId)
                ];
                $data['flat_rate_cost_calculation_type'] = !empty($data['flat_rate_cost_calculation_type']) ? $data['flat_rate_cost_calculation_type'] : '';
                $data['flat_rate_cost'] = !empty($data['flat_rate_cost']) ? $data['flat_rate_cost'] : 0;
                $data['local_pickup_cost'] = !empty($data['local_pickup_cost']) ? $data['local_pickup_cost'] : 0;
                $data['free_shipping_min_amount'] = !empty($data['free_shipping_min_amount']) ? $data['free_shipping_min_amount'] : 0;
                $data['flat_rate_cost'] = getPrice($data['flat_rate_cost'], 'database');
                $data['local_pickup_cost'] = getPrice($data['local_pickup_cost'], 'database');
                $data['free_shipping_min_amount'] = getPrice($data['free_shipping_min_amount'], 'database');
                //shipping classes
                $classArray = array();
                $shippingClasses = $this->getShippingClasses(user()->id);
                if (!empty($shippingClasses)) {
                    foreach ($shippingClasses as $shippingClass) {
                        $item = [
                            'class_id' => $shippingClass->id,
                            'cost' => inputPost('flat_rate_cost_' . $optionUniqueId . '_class_' . $shippingClass->id)
                        ];
                        if (empty($item['cost'])) {
                            $item['cost'] = 0;
                        }
                        $item['cost'] = getPrice($item['cost'], 'database');
                        array_push($classArray, $item);
                    }
                }
                $data['flat_rate_class_costs_array'] = '';
                if (!empty($classArray)) {
                    $data['flat_rate_class_costs_array'] = serialize($classArray);
                }
                if (inputPost('method_operation_' . $optionUniqueId, true) == 'edit') {
                    $this->builderZoneMethods->where('id', clrNum($optionUniqueId))->update($data);
                } else {
                    $data['user_id'] = user()->id;
                    $this->builderZoneMethods->insert($data);
                }
            }
        }
    }

    //get shipping zone
    public function getShippingZone($id)
    {
        return $this->builderZones->where('id', clrNum($id))->get()->getRow();
    }

    //get shipping zones
    public function getShippingZones($userId)
    {
        return $this->builderZones->where('user_id', clrNum($userId))->orderBy('id DESC')->get()->getResult();
    }

    //get shipping locations by zone
    public function getShippingLocationsByZone($zoneId)
    {
        return $this->builderZoneLocations->select("shipping_zone_locations.*, (SELECT name FROM location_countries WHERE location_countries.id = shipping_zone_locations.country_id LIMIT 1) As country_name, 
        (SELECT name FROM location_states WHERE location_states.id = shipping_zone_locations.state_id LIMIT 1) As state_name")->where('zone_id', clrNum($zoneId))->get()->getResult();
    }

    //get shipping payment methods by zone
    public function getShippingPaymentMethodsByZone($zoneId)
    {
        return $this->builderZoneMethods->where('zone_id', clrNum($zoneId))->orderBy('id DESC')->get()->getResult();
    }

    //add shipping class
    public function addShippingClass()
    {
        $nameArray = array();
        foreach ($this->activeLanguages as $language) {
            $item = [
                'lang_id' => $language->id,
                'name' => inputPost('name_lang_' . $language->id)
            ];
            array_push($nameArray, $item);
        }
        $data = [
            'user_id' => user()->id,
            'name_array' => serialize($nameArray),
            'status' => inputPost('status')
        ];
        if (empty($data['status'])) {
            $data['status'] = 0;
        }
        return $this->builderClasses->insert($data);
    }

    //edit shipping class
    public function editShippingClass($id)
    {
        $row = $this->getShippingClass($id);
        if (empty($row) || $row->user_id != user()->id) {
            return false;
        }
        $nameArray = array();
        foreach ($this->activeLanguages as $language) {
            $item = [
                'lang_id' => $language->id,
                'name' => inputPost('name_lang_' . $language->id)
            ];
            array_push($nameArray, $item);
        }
        $data = [
            'name_array' => serialize($nameArray),
            'status' => inputPost('status')
        ];
        if (empty($data['status'])) {
            $data['status'] = 0;
        }
        return $this->builderClasses->where('id', $row->id)->update($data);
    }

    //get shipping classes
    public function getShippingClasses($userId)
    {
        return $this->builderClasses->where('user_id', clrNum($userId))->orderBy('id DESC')->get()->getResult();
    }

    //get active shipping classes
    public function getActiveShippingClasses($userId)
    {
        return $this->builderClasses->where('user_id', clrNum($userId))->where('status', 1)->orderBy('id DESC')->get()->getResult();
    }

    //get shipping class
    public function getShippingClass($id)
    {
        return $this->builderClasses->where('id', clrNum($id))->get()->getRow();
    }

    //delete shipping class
    public function deleteShippingClass($id)
    {
        $row = $this->getShippingClass($id);
        if (!empty($row) && $row->user_id == user()->id) {
            return $this->builderClasses->where('id', clrNum($id))->delete();
        }
        return false;
    }

    //add shipping delivery time
    public function addShippingDeliveryTime()
    {
        $optionArray = array();
        foreach ($this->activeLanguages as $language) {
            $item = [
                'lang_id' => $language->id,
                'option' => inputPost('option_lang_' . $language->id)
            ];
            array_push($optionArray, $item);
        }
        $data = [
            'user_id' => user()->id,
            'option_array' => serialize($optionArray)
        ];
        return $this->buildersDeliveryTimes->insert($data);
    }

    //edit shipping delivery time
    public function editShippingDeliveryTime($id)
    {
        $row = $this->getShippingDeliveryTime($id);
        if (empty($row) || $row->user_id != user()->id) {
            return false;
        }
        $optionArray = array();
        foreach ($this->activeLanguages as $language) {
            $item = [
                'lang_id' => $language->id,
                'option' => inputPost('option_lang_' . $language->id, true)
            ];
            array_push($optionArray, $item);
        }
        $data = [
            'option_array' => serialize($optionArray)
        ];
        return $this->buildersDeliveryTimes->where('id', $row->id)->update($data);
    }

    //get shipping delivery times
    public function getShippingDeliveryTimes($userId, $sort = '')
    {
        $this->buildersDeliveryTimes->where('user_id', clrNum($userId));
        if (!empty($sort)) {
            $this->buildersDeliveryTimes->orderBy('id DESC');
        } else {
            $this->buildersDeliveryTimes->orderBy('id');
        }
        return $this->buildersDeliveryTimes->get()->getResult();
    }

    //get shipping delivery time
    public function getShippingDeliveryTime($id)
    {
        return $this->buildersDeliveryTimes->where('id', clrNum($id))->get()->getRow();
    }

    //delete shipping location
    public function deleteShippingLocation($id)
    {
        $row = $this->builderZoneLocations->join('shipping_zones', 'shipping_zones.id = shipping_zone_locations.zone_id')->select('shipping_zone_locations.*')
            ->where('shipping_zone_locations.id', clrNum($id))->where('shipping_zones.user_id', user()->id)->get()->getRow();
        if (!empty($row)) {
            return $this->builderZoneLocations->where('id', clrNum($id))->delete();
        }
        return false;
    }

    //delete shipping method
    public function deleteShippingMethod($id)
    {
        $row = $this->builderZoneMethods->join('shipping_zones', 'shipping_zones.id = shipping_zone_methods.zone_id')->select('shipping_zone_methods.*')
            ->where('shipping_zone_methods.id', clrNum($id))->where('shipping_zones.user_id', user()->id)->get()->getRow();
        if (!empty($row)) {
            return $this->builderZoneMethods->where('id', clrNum($id))->delete();
        }
    }

    //delete shipping delivery time
    public function deleteShippingDeliveryTime($id)
    {
        $row = $this->getShippingDeliveryTime($id);
        if (!empty($row) && $row->user_id == user()->id) {
            return $this->buildersDeliveryTimes->where('id', clrNum($id))->delete();
        }
        return false;
    }

    //delete shipping zone
    public function deleteShippingZone($id)
    {
        $row = $this->builderZones->where('shipping_zones.id', clrNum($id))->where('shipping_zones.user_id', user()->id)->get()->getRow();
        if (!empty($row)) {
            //delete locations
            $this->builderZoneLocations->where('zone_id', clrNum($id))->delete();
            //delete methods
            $this->builderZoneMethods->where('zone_id', clrNum($id))->delete();
            //delete zone
            $this->builderZones->where('id', clrNum($id))->delete();
        }
    }
}