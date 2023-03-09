<?php

use Config\Globals;

if (strpos($_SERVER['REQUEST_URI'], '/index.php') !== false) {
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if (!empty($url)) {
        $url = str_replace('/index.php', '', $url);
    }
    header('Location: ' . $url);
    exit();
}

if (strpos($_SERVER['REQUEST_URI'], '/mds-iyzico-payment-callback') !== false) {
    $token = $_POST['token'];
    $urlArray = parse_url($_SERVER['REQUEST_URI'] ?? '');
    if (!empty($urlArray['query'])) {
        parse_str($urlArray['query'], $paramArray);

        $paymentType = isset($paramArray['payment_type']) ? $paramArray['payment_type'] : '';
        $baseUrl = isset($paramArray['base_url']) ? $paramArray['base_url'] : '';
        $conversationId = isset($paramArray['conversation_id']) ? $paramArray['conversation_id'] : '';
        $lang = isset($paramArray['lang']) ? $paramArray['lang'] : '';
        $mdsToken = isset($paramArray['mds_token']) ? $paramArray['mds_token'] : '';

        header('Location: ' . $baseUrl . '/iyzico-payment-post?token=' . $token . '&payment_type=' . $paymentType . '&conversation_id=' . $conversationId . '&lang=' . $lang . '&mds_token=' . $mdsToken);
        exit();
    }
    redirectToUrl(base_url());
}

//current full url
if (!function_exists('getCurrentUrl')) {
    function getCurrentUrl($esc = true)
    {
        $currentURL = current_url();
        if (!empty($_SERVER['QUERY_STRING'])) {
            $currentURL = $currentURL . "?" . $_SERVER['QUERY_STRING'];
        }
        if ($esc) {
            return esc($currentURL);
        }
        return $currentURL;
    }
}

//language base URL
if (!function_exists('langBaseUrl')) {
    function langBaseUrl($route = null)
    {
        if (!empty($route)) {
            return Globals::$langBaseUrl . '/' . $route;
        }
        return Globals::$langBaseUrl;
    }
}

//generate base URL by language id
if (!function_exists('generateBaseURLByLangId')) {
    function generateBaseURLByLangId($langId)
    {
        if ($langId == Globals::$generalSettings->site_lang) {
            return base_url() . '/';
        } else {
            $languages = Globals::$languages;
            $shortForm = '';
            if (!empty($languages)) {
                foreach ($languages as $language) {
                    if ($langId == $language->id) {
                        $shortForm = $language->short_form;
                    }
                }
            }
            if ($shortForm != '') {
                return base_url($shortForm) . '/';
            }
        }
        return base_url() . '/';
    }
}

//admin url
if (!function_exists('adminUrl')) {
    function adminUrl($route = null)
    {
        if (!empty($route)) {
            return base_url(Globals::$customRoutes->admin . '/' . $route);
        }
        return base_url(Globals::$customRoutes->admin);
    }
}

//dashboard url
if (!function_exists('dashboardUrl')) {
    function dashboardUrl($route = null)
    {
        if (!empty($route)) {
            return base_url(Globals::$customRoutes->dashboard . '/' . $route);
        }
        return base_url(Globals::$customRoutes->dashboard);
    }
}

//auth check
if (!function_exists('authCheck')) {
    function authCheck()
    {
        return Globals::$authCheck;
    }
}

//get active user
if (!function_exists('user')) {
    function user()
    {
        return Globals::$authUser;
    }
}

//get active user id
if (!function_exists('activeUserId')) {
    function activeUserId()
    {
        if (authCheck()) {
            return user()->id;
        }
        return 0;
    }
}

//get user by id
if (!function_exists('getUser')) {
    function getUser($id)
    {
        $model = new \App\Models\AuthModel();
        return $model->getUser($id);
    }
}

//get username
if (!function_exists('getUsername')) {
    function getUsername($user)
    {
        $isMember = true;
        if (!empty($user)) {
            if (hasPermission('all', $user) || hasPermission('admin_panel', $user) || hasPermission('vendor', $user)) {
                $isMember = false;
            }
        }
        if (!$isMember && !empty($user->username)) {
            return $user->username;
        }
        return $user->first_name . ' ' . $user->last_name;
    }
}

//get username by user id
if (!function_exists('getUsernameByUserId')) {
    function getUsernameByUserId($userId)
    {
        $user = getUser($userId);
        return getUsername($user);
    }
}

//is super admin
if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin()
    {
        if (authCheck() && hasPermission('all')) {
            return true;
        }
        return false;
    }
}

//is admin
if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        if (authCheck() && hasPermission('admin_panel')) {
            return true;
        }
        return false;
    }
}

//is vendor
if (!function_exists('isVendor')) {
    function isVendor($user = null)
    {
        if ($user == null && authCheck()) {
            $user = user();
        }
        if (!empty($user)) {
            if ($user->role_id == 1) {
                return true;
            }
            if (Globals::$generalSettings->multi_vendor_system == 1) {
                if (Globals::$generalSettings->vendor_verification_system != 1) {
                    return true;
                } else {
                    if (hasPermission('vendor', $user)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}

//get logo
if (!function_exists('getLogo')) {
    function getLogo()
    {
        if (!empty(Globals::$generalSettings->logo) && file_exists(FCPATH . Globals::$generalSettings->logo)) {
            return base_url(Globals::$generalSettings->logo);
        }
        return base_url('assets/img/logo.svg');
    }
}

//get logo email
if (!function_exists('getLogoEmail')) {
    function getLogoEmail()
    {
        if (!empty(Globals::$generalSettings->logo_email) && file_exists(FCPATH . Globals::$generalSettings->logo_email)) {
            return base_url(Globals::$generalSettings->logo_email);
        }
        return base_url('assets/img/logo.png');
    }
}

//get favicon
if (!function_exists('getFavicon')) {
    function getFavicon()
    {
        if (!empty(Globals::$generalSettings->favicon) && file_exists(FCPATH . Globals::$generalSettings->favicon)) {
            return base_url(Globals::$generalSettings->favicon);
        }
        return base_url('assets/img/favicon.png');
    }
}

//get selected currency
if (!function_exists('getSelectedCurrency')) {
    function getSelectedCurrency()
    {
        $session = \Config\Services::session();
        if (Globals::$paymentSettings->currency_converter == 1) {
            $sessCurrency = helperGetSession('mds_selected_currency');
            if (!empty($sessCurrency) && isset(Globals::$currencies[$sessCurrency])) {
                return Globals::$currencies[$sessCurrency];
            }
        }
        return Globals::$defaultCurrency;
    }
}

//redirect to URL
if (!function_exists('redirectToUrl')) {
    function redirectToUrl($url)
    {
        header('Location: ' . $url);
        exit();
    }
}

//redirect to back URL
if (!function_exists('redirectToBackUrl')) {
    function redirectToBackUrl()
    {
        $backURL = inputPost('back_url');
        if (!empty($backURL) && strpos($backURL, base_url()) !== false) {
            redirectToUrl($backURL);
        }
        redirectToUrl(langBaseUrl());
    }
}

//character limiter
if (!function_exists('characterLimiter')) {
    function characterLimiter($str, $limit, $endChar = '')
    {
        if (!empty($str)) {
            return character_limiter($str, $limit, $endChar);
        }
    }
}

//translation
if (!function_exists('trans')) {
    function trans($string, $clearQuotes = false)
    {
        if (isset(Globals::$languageTranslations[$string])) {
            if ($clearQuotes) {
                return clrQuotes(Globals::$languageTranslations[$string]);
            }
            return Globals::$languageTranslations[$string];
        }
        return '';
    }
}

//get translated message
if (!function_exists('transWithField')) {
    function transWithField($string, $value)
    {
        if (!empty(Globals::$languageTranslations[$string])) {
            $trans = Globals::$languageTranslations[$string];
            if (!empty($trans)) {
                $trans = str_replace('{field}', $value, $trans);
            }
            return $trans;
        }
        return '';
    }
}

//convert URL by language
if (!function_exists('convertUrlByLanguage')) {
    function convertUrlByLanguage($language)
    {
        $langSegment = Globals::$langSegment;
        $pageUri = '';
        $baseUrl = base_url() . '/';
        if (empty($langSegment)) {
            $pageUri = str_replace($baseUrl, '', getCurrentUrl());
        } else {
            $baseUrl = base_url() . '/' . $langSegment;
            $pageUri = str_replace($baseUrl, '', getCurrentUrl());
        }
        if (!empty($pageUri)) {
            $pageUri = trim($pageUri, '/');
        }
        $newBaseUrl = base_url() . '/';
        if (Globals::$generalSettings->site_lang != $language->id) {
            $newBaseUrl = base_url() . '/' . $language->short_form . '/';
        }
        return $newBaseUrl . $pageUri;
    }
}

//get validation rules
if (!function_exists('getValRules')) {
    function getValRules($val)
    {
        $rules = $val->getRules();
        $newRules = array();
        if (!empty($rules)) {
            foreach ($rules as $key => $rule) {
                $newRules[$key] = [
                    'label' => $rule['label'],
                    'rules' => $rule['rules'],
                    'errors' => [
                        'required' => trans("form_validation_required"),
                        'min_length' => trans("form_validation_min_length"),
                        'max_length' => trans("form_validation_max_length"),
                        'matches' => trans("form_validation_matches"),
                        'is_unique' => trans("form_validation_is_unique")
                    ]
                ];
            }
        }
        return $newRules;
    }
}

//get segment value
if (!function_exists('getSegmentValue')) {
    function getSegmentValue($segmentNumber)
    {
        try {
            $uri = service('uri');
            if ($uri->getSegment($segmentNumber) !== null) {
                return $uri->getSegment($segmentNumber);
            }
        } catch (Exception $e) {
        }
        return null;
    }
}

//get request
if (!function_exists('inputGet')) {
    function inputGet($inputName)
    {
        $input = \Config\Services::request()->getGet($inputName);
        if (!empty($input) && !is_array($input)) {
            $input = trim($input);
        }
        return $input;
    }
}

//post request
if (!function_exists('inputPost')) {
    function inputPost($inputName)
    {
        $input = \Config\Services::request()->getPost($inputName);
        if (!empty($input) && !is_array($input)) {
            $input = trim($input);
        }
        return $input;
    }
}

//get array column values
if (!function_exists('getArrayColumnValues')) {
    function getArrayColumnValues($array, $column)
    {
        $values = array();
        if (!empty($array) && !empty($column)) {
            foreach ($array as $item) {
                if (!empty($item)) {
                    if (is_object($item)) {
                        if (!empty($item->$column)) {
                            $values[] = $item->$column;
                        }
                    } else {
                        if (!empty($item[$column])) {
                            $values[] = $item[$column];
                        }
                    }
                }
            }
        }
        return $values;
    }
}

//is value exists in array
if (!function_exists('isItemInArray')) {
    function isItemInArray($item, $array)
    {
        if (empty($array) || empty($item) || !is_array($array)) {
            return false;
        }
        if (in_array($item, $array)) {
            return true;
        }
        return false;
    }
}

//get ids from array
if (!function_exists('getIdsFromArray')) {
    function getIdsFromArray($array, $column = 'id')
    {
        if (!empty($array)) {
            return getArrayColumnValues($array, $column);
        }
        return array();
    }
}

//generate ids string
if (!function_exists('generateIdsString')) {
    function generateIdsString($array)
    {
        if (!empty($array)) {
            return implode(',', $array);
        }
        return '0';
    }
}

//convert string to slug
if (!function_exists('strSlug')) {
    function strSlug($str)
    {
        $str = trim($str ?? '');
        if (!empty($str)) {
            return url_title(convert_accented_characters($str), '-', TRUE);
        }
    }
}

//generate slug
if (!function_exists('generateSlug')) {
    function generateSlug($slug, $title)
    {
        if (empty($slug)) {
            return strSlug($title);
        } else {
            $newSlug = removeSpecialCharacters($slug);
            if (!empty($newSlug)) {
                $newSlug = str_replace(' ', '-', $newSlug);
            }
            return $newSlug;
        }
    }
}

//clean string
if (!function_exists('cleanStr')) {
    function cleanStr($str)
    {
        $str = trim($str ?? '');
        $str = esc($str ?? '');
        return removeSpecialCharacters($str);
    }
}

//clean number
if (!function_exists('clrNum')) {
    function clrNum($num)
    {
        $num = trim($num ?? '');
        $num = esc($num ?? '');
        $num = intval($num ?? '');
        if (!empty($num)) {
            return $num;
        }
        return 0;
    }
}

//remove forbidden characters
if (!function_exists('removeForbiddenCharacters')) {
    function removeForbiddenCharacters($str)
    {
        $str = trim($str ?? '');
        $str = str_replace(';', '', $str ?? '');
        $str = str_replace('"', '', $str ?? '');
        $str = str_replace('$', '', $str ?? '');
        $str = str_replace('%', '', $str ?? '');
        $str = str_replace('*', '', $str ?? '');
        $str = str_replace('/', '', $str ?? '');
        $str = str_replace('\'', '', $str ?? '');
        $str = str_replace('<', '', $str ?? '');
        $str = str_replace('>', '', $str ?? '');
        $str = str_replace('=', '', $str ?? '');
        $str = str_replace('?', '', $str ?? '');
        $str = str_replace('[', '', $str ?? '');
        $str = str_replace(']', '', $str ?? '');
        $str = str_replace('\\', '', $str ?? '');
        $str = str_replace('^', '', $str ?? '');
        $str = str_replace('`', '', $str ?? '');
        $str = str_replace('{', '', $str ?? '');
        $str = str_replace('}', '', $str ?? '');
        $str = str_replace('|', '', $str ?? '');
        $str = str_replace('~', '', $str ?? '');
        $str = str_replace('+', '', $str ?? '');
        return $str;
    }
}

//remove special characters
if (!function_exists('removeSpecialCharacters')) {
    function removeSpecialCharacters($str, $removeQuotes = false)
    {
        $str = removeForbiddenCharacters($str);
        $str = str_replace('#', '', $str ?? '');
        $str = str_replace('!', '', $str ?? '');
        $str = str_replace('(', '', $str ?? '');
        $str = str_replace(')', '', $str ?? '');
        if ($removeQuotes) {
            $str = clrQuotes($str);
        }
        return $str;
    }
}

//clean quotes
if (!function_exists('clrQuotes')) {
    function clrQuotes($str)
    {
        $str = str_replace('"', '', $str ?? '');
        $str = str_replace("'", '', $str ?? '');
        return $str;
    }
}

//set success message
if (!function_exists('setSuccessMessage')) {
    function setSuccessMessage($message)
    {
        if (!empty($message)) {
            $session = \Config\Services::session();
            $session->setFlashdata('success', $message);
        }
    }
}

//set error message
if (!function_exists('setErrorMessage')) {
    function setErrorMessage($message)
    {
        if (!empty($message)) {
            $session = \Config\Services::session();
            $session->setFlashdata('error', $message);
        }
    }
}

//generate unique id
if (!function_exists('generateToken')) {
    function generateToken()
    {
        $token = uniqid('', TRUE);
        $token = str_replace('.', '-', $token ?? '');
        return $token . '-' . rand(10000000, 99999999);
    }
}

//count items
if (!function_exists('countItems')) {
    function countItems($items)
    {
        if (!empty($items) && is_array($items)) {
            return count($items);
        }
        return 0;
    }
}

//get font
if (!function_exists('getFontClient')) {
    function getFontClient($activeFonts, $type)
    {
        if (!empty($activeFonts)) {
            if ($type == 'site' && !empty($activeFonts['site_font'])) {
                return $activeFonts['site_font'];
            }
            if ($type == 'dashboard' && !empty($activeFonts['dashboard_font'])) {
                return $activeFonts['dashboard_font'];
            }
        }
        return null;
    }
}

//get route
if (!function_exists('getRoute')) {
    function getRoute($key, $slash = false)
    {
        $route = $key;
        if (!empty(Globals::$customRoutes->$key)) {
            $route = Globals::$customRoutes->$key;
            if ($slash == true) {
                $route .= '/';
            }
        }
        return $route;
    }
}

//generate static url
if (!function_exists('generateUrl')) {
    function generateUrl($route1, $route2 = null)
    {
        if (!empty($route2)) {
            return langBaseUrl(getRoute($route1, true) . getRoute($route2));
        } else {
            return langBaseUrl(getRoute($route1));
        }
    }
}

//generate menu item url
if (!function_exists('generateMenuItemUrl')) {
    function generateMenuItemUrl($item)
    {
        if (!empty($item)) {
            return langBaseUrl($item->slug);
        }
    }
}

//generate profile url
if (!function_exists('generateProfileUrl')) {
    function generateProfileUrl($slug)
    {
        if (!empty($slug)) {
            return langBaseUrl(getRoute('profile', true) . $slug);
        }
    }
}

//generate category url
if (!function_exists('generateCategoryUrl')) {
    function generateCategoryUrl($category)
    {
        if (!empty($category)) {
            if ($category->parent_id == 0) {
                return langBaseUrl($category->slug);
            } else {
                return langBaseUrl($category->parent_slug . '/' . $category->slug);
            }
        }
    }
}

//generate product url
if (!function_exists('generateProductUrl')) {
    function generateProductUrl($product)
    {
        if (!empty($product)) {
            return langBaseUrl($product->slug);
        }
    }
}

//generate product url by slug
if (!function_exists('generateProductUrlBySlug')) {
    function generateProductUrlBySlug($slug)
    {
        if (!empty($slug)) {
            return langBaseUrl($slug);
        }
    }
}

//generate blog url
if (!function_exists('generatePostUrl')) {
    function generatePostUrl($post)
    {
        if (!empty($post)) {
            return langBaseUrl(getRoute('blog', true) . $post->category_slug . '/' . $post->slug);
        }
    }
}

//generate dash url
if (!function_exists('generateDashUrl')) {
    function generateDashUrl($route1, $route2 = null)
    {
        if (!empty($route1)) {
            return dashboardUrl(getRoute($route1, true) . getRoute($route2));
        } else {
            return dashboardUrl(getRoute($route1));
        }
    }
}

//get category image url
if (!function_exists('getCategoryImageUrl')) {
    function getCategoryImageUrl($category)
    {
        if ($category->storage == 'aws_s3') {
            return getAWSBaseUrl() . $category->image;
        } else {
            return base_url($category->image);
        }
    }
}

//get AWS base url
if (!function_exists('getAWSBaseURL')) {
    function getAWSBaseURL()
    {
        return 'https://s3.' . Globals::$storageSettings->aws_region . '.amazonaws.com/' . Globals::$storageSettings->aws_bucket . '/';
    }
}

//get permissions array
if (!function_exists('getPermissionsArray')) {
    function getPermissionsArray()
    {
        return ['1' => 'admin_panel', '2' => 'vendor', '3' => 'navigation', '4' => 'slider', '5' => 'homepage_manager', '6' => 'orders', '7' => 'digital_sales',
            '8' => 'earnings', '9' => 'payouts', '10' => 'refund_requests', '11' => 'products', '12' => 'quote_requests', '13' => 'categories', '14' => 'custom_fields',
            '15' => 'pages', '16' => 'blog', '17' => 'location', '18' => 'membership', '19' => 'help_center', '20' => 'storage', '21' => 'cache_system', '22' => 'seo_tools',
            '23' => 'ad_spaces', '24' => 'contact_messages', '25' => 'reviews', '26' => 'comments', '27' => 'abuse_reports', '28' => 'newsletter', '29' => 'preferences',
            '30' => 'general_settings', '31' => 'product_settings', '32' => 'payment_settings', '33' => 'visual_settings', '34' => 'system_settings'];
    }
}

//get permission index key
if (!function_exists('getPermissionIndex')) {
    function getPermissionIndex($permission)
    {
        $array = getPermissionsArray();
        foreach ($array as $key => $value) {
            if ($value == $permission) {
                return $key;
            }
        }
        return null;
    }
}

//get permission by index
if (!function_exists('getPermissionByIndex')) {
    function getPermissionByIndex($index)
    {
        $array = getPermissionsArray();
        if (isset($array[$index])) {
            return $array[$index];
        }
        return null;
    }
}

//has permission
if (!function_exists('hasPermission')) {
    function hasPermission($permission, $user = null)
    {
        if (authCheck() && empty($user)) {
            $user = user();
        }
        if (!empty($user) && !empty($user->permissions)) {
            if ($user->permissions == 'all') {
                return true;
            }
            $array = explode(',', $user->permissions);
            $index = getPermissionIndex($permission);
            if (!empty($index) && !empty($array) && in_array($index, $array)) {
                return true;
            }
        }
        return false;
    }
}

//check permission
if (!function_exists('checkPermission')) {
    function checkPermission($permission)
    {
        if (!hasPermission($permission)) {
            redirectToUrl(base_url());
        }
    }
}

//check admin nav
if (!function_exists('isAdminNavActive')) {
    function isAdminNavActive($arrayNavItems)
    {
        $segment = getSegmentValue(2);
        if (!empty($segment) && !empty($arrayNavItems)) {
            if (in_array($segment, $arrayNavItems)) {
                echo ' ' . 'active';
            }
        }
    }
}

//date format
if (!function_exists('formatDate')) {
    function formatDate($timestamp)
    {
        if (!empty($timestamp)) {
            return date('Y-m-d / H:i', strtotime($timestamp));
        }
    }
}

//date format
if (!function_exists('formatDateLong')) {
    function formatDateLong($datetime, $showDay = true)
    {
        $date = date('j M Y', strtotime($datetime));
        if ($showDay == false) {
            $date = date('M Y', strtotime($datetime));
        }
        $date = str_replace('Jan', trans("january"), $date);
        $date = str_replace('Feb', trans("february"), $date);
        $date = str_replace('Mar', trans("march"), $date);
        $date = str_replace('Apr', trans("april"), $date);
        $date = str_replace('May', trans("may"), $date);
        $date = str_replace('Jun', trans("june"), $date);
        $date = str_replace('Jul', trans("july"), $date);
        $date = str_replace('Aug', trans("august"), $date);
        $date = str_replace('Sep', trans("september"), $date);
        $date = str_replace('Oct', trans("october"), $date);
        $date = str_replace('Nov', trans("november"), $date);
        $date = str_replace('Dec', trans("december"), $date);
        return $date;
    }
}

//get language
if (!function_exists('getLanguage')) {
    function getLanguage($langId)
    {
        $model = new \App\Models\LanguageModel();
        return $model->getLanguage($langId);
    }
}

//unserialize data
if (!function_exists('unserializeData')) {
    function unserializeData($serializedData)
    {
        $data = @unserialize($serializedData);
        if (empty($data) && preg_match('/^[aOs]:/', $serializedData)) {
            $serializedData = preg_replace_callback('/s\:(\d+)\:\"(.*?)\";/s', function ($matches) {
                return 's:' . strlen($matches[2]) . ':"' . $matches[2] . '";';
            }, $serializedData);
            $data = @unserialize($serializedData);
        }
        return $data;
    }
}

//get csv value
if (!function_exists('getCsvValue')) {
    function getCsvValue($array, $key, $dataType = 'string')
    {
        if (!empty($array)) {
            if (!empty($array[$key])) {
                return $array[$key];
            }
        }
        if ($dataType == 'int') {
            return 0;
        }
        return '';
    }
}

//parse serialized name array
if (!function_exists('parseSerializedNameArray')) {
    function parseSerializedNameArray($nameArray, $langId, $getMainName = true)
    {
        if (!empty($nameArray)) {
            $nameArray = unserializeData($nameArray);
            if (!empty($nameArray)) {
                foreach ($nameArray as $item) {
                    if ($item['lang_id'] == $langId && !empty($item['name'])) {
                        return esc($item['name']);
                    }
                }
            }
            //if not exist
            if ($getMainName == true) {
                if (!empty($nameArray)) {
                    foreach ($nameArray as $item) {
                        if ($item['lang_id'] == Globals::$defaultLang->id && !empty($item['name'])) {
                            return esc($item['name']);
                        }
                    }
                }
            }
        }
        return '';
    }
}

//parse serialized option array
if (!function_exists('parseSerializedOptionArray')) {
    function parseSerializedOptionArray($optionArray, $langId, $getMainName = true)
    {
        if (!empty($optionArray)) {
            $optionArray = unserializeData($optionArray);
            if (!empty($optionArray)) {
                foreach ($optionArray as $item) {
                    if ($item['lang_id'] == $langId && !empty($item['option'])) {
                        return esc($item['option']);
                    }
                }
            }
            //if not exist
            if ($getMainName == true) {
                if (!empty($optionArray)) {
                    foreach ($optionArray as $item) {
                        if ($item['lang_id'] == Globals::$defaultLang->id && !empty($item['option'])) {
                            return esc($item['option']);
                        }
                    }
                }
            }
        }
        return '';
    }
}


//set cookie
if (!function_exists('helperSetCookie')) {
    function helperSetCookie($name, $value, $time = null)
    {
        if ($time == null) {
            $time = time() + (86400 * 30);
        }
        if (empty($params)) {
            $config = config('App');
            $params = [
                'expires' => $time,
                'path' => $config->cookiePath,
                'domain' => $config->cookieDomain,
                'secure' => $config->cookieSecure,
                'httponly' => $config->cookieHTTPOnly,
                'samesite' => $config->cookieSameSite,
            ];
        }
        if (!empty(getenv('cookie.prefix'))) {
            $name = getenv('cookie.prefix') . $name;
        }
        setcookie($name, $value, $params);
    }
}

//get cookie
if (!function_exists('helperGetCookie')) {
    function helperGetCookie($name)
    {
        if (!empty(getenv('cookie.prefix'))) {
            $name = getenv('cookie.prefix') . $name;
        }
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return false;
    }
}

//delete cookie
if (!function_exists('helperDeleteCookie')) {
    function helperDeleteCookie($name)
    {
        if (!empty(helperGetCookie($name))) {
            helperSetCookie($name, '', time() - 3600);
        }
    }
}

//set session
if (!function_exists('helperSetSession')) {
    function helperSetSession($name, $value)
    {
        $session = \Config\Services::session();
        $session->set($name, $value);
    }
}

//get session
if (!function_exists('helperGetSession')) {
    function helperGetSession($name)
    {
        $session = \Config\Services::session();
        if ($session->get($name) !== null) {
            return $session->get($name);
        }
        return null;
    }
}

//delete session
if (!function_exists('helperDeleteSession')) {
    function helperDeleteSession($name)
    {
        $session = \Config\Services::session();
        if ($session->get($name) !== null) {
            $session->remove($name);
        }
    }
}

//product location cache key
if (!function_exists('getLocationCacheKey')) {
    function getLocationCacheKey()
    {
        $key = '';
        if (!empty(Globals::$defaultLocation->country_id)) {
            $key .= Globals::$defaultLocation->country_id;
        }
        if (!empty(Globals::$defaultLocation->state_id)) {
            $key .= '_' . Globals::$defaultLocation->state_id;
        }
        if (!empty(Globals::$defaultLocation->city_id)) {
            $key .= '_' . Globals::$defaultLocation->city_id;
        }
        if (empty($key)) {
            $key = '1';
        }
        if (!empty($key)) {
            $key = trim($key);
        }
        return $key;
    }
}

//set cache data
if (!function_exists('setCacheData')) {
    function setCacheData($key, $data, $langId = null)
    {
        if (Globals::$generalSettings->cache_system == 1) {
            if (empty($langId)) {
                $langId = selectedLangId();
            }
            $key = 'cache_' . $key . '_lang' . $langId;
            $cache = \Config\Services::cache();
            cache()->save($key, $data, Globals::$generalSettings->cache_refresh_time);
        }
    }
}

//get cache data
if (!function_exists('getCacheData')) {
    function getCacheData($key, $langId = null)
    {
        if (Globals::$generalSettings->cache_system == 1) {
            if (empty($langId)) {
                $langId = selectedLangId();
            }
            $key = 'cache_' . $key . '_lang' . $langId;
            $cache = \Config\Services::cache();
            if ($data = cache($key)) {
                return $data;
            }
        }
        return null;
    }
}


//set cache product
if (!function_exists('setCacheProduct')) {
    function setCacheProduct($key, $data, $filterByLocation = true)
    {
        if (Globals::$generalSettings->cache_system == 1) {
            $key = 'cache_' . $key . '_lang' . selectedLangId();
            $cache = \Config\Services::cache();
            $cacheData = cache($key);
            if (empty($cacheData)) {
                $cacheData = array();
            }
            if ($filterByLocation) {
                $cacheData[getLocationCacheKey()] = $data;
            } else {
                $cacheData = $data;
            }
            cache()->save($key, $cacheData, Globals::$generalSettings->cache_refresh_time);
        }
    }
}

//get cache product
if (!function_exists('getCacheProduct')) {
    function getCacheProduct($key, $filterByLocation = true)
    {
        if (Globals::$generalSettings->cache_system == 1) {
            $key = 'cache_' . $key . '_lang' . selectedLangId();
            $cache = \Config\Services::cache();
            if ($data = cache($key)) {
                if ($filterByLocation) {
                    $locationKey = getLocationCacheKey();
                    if (!empty($data[$locationKey])) {
                        return $data[$locationKey];
                    }
                } else {
                    return $data;
                }
            }
        }
        return null;
    }
}

//reset cache data on change
if (!function_exists('resetCacheDataOnChange')) {
    function resetCacheDataOnChange()
    {
        if (Globals::$generalSettings->refresh_cache_database_changes == 1) {
            resetCacheData();
        }
    }
}

//reset cache data
if (!function_exists('resetCacheData')) {
    function resetCacheData()
    {
        $cachePath = WRITEPATH . 'cache/';
        $files = glob($cachePath . '*');
        if (!empty($files)) {
            foreach ($files as $file) {
                if (strpos($file, 'index.html') === false) {
                    @unlink($file);
                }
            }
        }
    }
}

//reset product img cache data
if (!function_exists('resetProductImgCacheData')) {
    function resetProductImgCacheData($product_id)
    {
        /*

        $path = $ci->config->item('cache_path');
        $cache_path = ($path == '') ? APPPATH . 'cache/' : $path;
        $handle = opendir($cache_path);
        while (($file = readdir($handle)) !== FALSE) {
            //Leave the directory protection alone
            if ($file != '.htaccess' && $file != 'index.html') {
                if (strpos($file, 'img_product_' . $product_id) !== false) {
                    @unlink($cache_path . '/' . $file);
                }
            }
        }
        closedir($handle);
        */
    }
}

//get checkbox value
if (!function_exists('getCheckboxValue')) {
    function getCheckboxValue($inputPost)
    {
        if (empty($inputPost)) {
            return 0;
        }
        return 1;
    }
}

//generate token
if (!function_exists('generateToken')) {
    function generateToken($short = false)
    {
        $token = uniqid('', TRUE);
        $token = strReplace('.', '-', $token);
        if ($short) {
            return $token;
        }
        return $token . '-' . rand(10000000, 99999999);
    }
}

//generate purchase code
if (!function_exists('generatePurchaseCode')) {
    function generatePurchaseCode()
    {
        $id = uniqid('', TRUE);
        $id = str_replace('.', '-', $id);
        $id .= '-' . rand(100000, 999999);
        $id .= '-' . rand(100000, 999999);
        return $id;
    }
}

//generate transaction number
if (!function_exists('generateTransactionNumber')) {
    function generateTransactionNumber()
    {
        $num = uniqid('', TRUE);
        return str_replace('.', '-', $num);
    }
}

//delete file from server
if (!function_exists('deleteFile')) {
    function deleteFile($path)
    {
        if (file_exists(FCPATH . $path)) {
            @unlink(FCPATH . $path);
        }
    }
}

if (!function_exists('addHTTPS')) {
    function addHTTPS($url)
    {
        if (!empty(trim($url))) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                if (strpos(base_url(), 'https://') !== false) {
                    $url = 'https://' . $url;
                } else {
                    $url = 'http://' . $url;
                }
            }
        }
        return $url;
    }
}

//download file
if (!function_exists('downloadFile')) {
    function downloadFile($path, $fileName = null)
    {
        $response = \Config\Services::response();
        if (file_exists($path)) {
            if (!empty($fileName)) {
                return $response->download($path, null)->setFileName($fileName);
            }
            return $response->download($path, null);
        }
        return redirect()->back();
    }
}

//paginate
if (!function_exists('paginate')) {
    function paginate($perPage, $total)
    {
        $page = @intval(inputGet('page') ?? '');
        if (empty($page) || $page < 1) {
            $page = 1;
        }
        $pager = \Config\Services::pager();
        $pager->makeLinks($page, $perPage, $total);
        $pager->page = $page;
        $pager->offset = ($page - 1) * $perPage;
        return $pager;
    }
}

//date diff
if (!function_exists('dateDifference')) {
    function dateDifference($endDate, $startDate, $format = '%a')
    {
        $datetime1 = date_create($endDate);
        $datetime2 = date_create($startDate);
        $diff = date_diff($datetime1, $datetime2);
        $day = $diff->format($format) + 1;
        if ($startDate > $endDate) {
            $day = 0 - $day;
        }
        return $day;
    }
}

//date difference in hours
if (!function_exists('dateDifferenceInHours')) {
    function dateDifferenceInHours($date1, $date2)
    {
        $datetime1 = date_create($date1);
        $datetime2 = date_create($date2);
        $diff = date_diff($datetime1, $datetime2);
        $days = $diff->format('%a');
        $hours = $diff->format('%h');
        return $hours + ($days * 24);
    }
}

//check cron time
if (!function_exists('checkCronTime')) {
    function checkCronTime($hour)
    {
        if (empty(Globals::$generalSettings->last_cron_update) || dateDifferenceInHours(date('Y-m-d H:i:s'), Globals::$generalSettings->last_cron_update) >= $hour) {
            return true;
        }
        return false;
    }
}

//time ago
if (!function_exists('timeAgo')) {
    function timeAgo($timestamp)
    {
        $timeAgo = strtotime($timestamp);
        $currentTime = time();
        $timeDifference = $currentTime - $timeAgo;
        $seconds = $timeDifference;
        $minutes = round($seconds / 60);
        $hours = round($seconds / 3600);
        $days = round($seconds / 86400);
        $weeks = round($seconds / 604800);
        $months = round($seconds / 2629440);
        $years = round($seconds / 31553280);
        if ($seconds <= 60) {
            return trans("just_now");
        } else if ($minutes <= 60) {
            if ($minutes == 1) {
                return '1 ' . trans("minute_ago");
            } else {
                return "$minutes " . trans("minutes_ago");
            }
        } else if ($hours <= 24) {
            if ($hours == 1) {
                return '1 ' . trans("hour_ago");
            } else {
                return "$hours " . trans("hours_ago");
            }
        } else if ($days <= 30) {
            if ($days == 1) {
                return '1 ' . trans("day_ago");
            } else {
                return "$days " . trans("days_ago");
            }
        } else if ($months <= 12) {
            if ($months == 1) {
                return '1 ' . trans("month_ago");
            } else {
                return "$months " . trans("months_ago");
            }
        } else {
            if ($years == 1) {
                return '1 ' . trans("year_ago");
            } else {
                return "$years " . trans("years_ago");
            }
        }
    }
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

//reset flash data
if (!function_exists('resetFlashData')) {
    function resetFlashData()
    {
        $session = \Config\Services::session();
        $session->setFlashdata('errors', '');
        $session->setFlashdata('error', '');
        $session->setFlashdata('success', '');
    }
}

//load library
if (!function_exists('loadLibrary')) {
    function loadLibrary($library)
    {
        $path = APPPATH . 'Libraries/' . $library . '.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }
}

//is recaptcha enabled
if (!function_exists('isRecaptchaEnabled')) {
    function isRecaptchaEnabled()
    {
        if (!empty(Globals::$generalSettings->recaptcha_site_key) && !empty(Globals::$generalSettings->recaptcha_secret_key)) {
            return true;
        }
        return false;
    }
}

//get recaptcha
if (!function_exists('reCaptcha')) {
    function reCaptcha($action)
    {
        if (isRecaptchaEnabled()) {
            loadLibrary('reCAPTCHA');
            $reCAPTCHA = new reCAPTCHA(Globals::$generalSettings->recaptcha_site_key, Globals::$generalSettings->recaptcha_secret_key);
            $reCAPTCHA->setLanguage(Globals::$activeLang->short_form);
            if ($action == 'generate') {
                echo $reCAPTCHA->getScript();
                echo $reCAPTCHA->getHtml();
            } elseif ($action == 'validate') {
                if (!$reCAPTCHA->isValid($_POST['g-recaptcha-response'])) {
                    return 'invalid';
                }
            }
        }
    }
}

//get IP address
if (!function_exists('getIPAddress')) {
    function getIPAddress()
    {
        $request = \Config\Services::request();
        return $request->getIPAddress();
    }
}

//check newsletter modal
if (!function_exists('checkNewsletterModal')) {
    function checkNewsletterModal()
    {
        if (!authCheck() && Globals::$generalSettings->newsletter_status == 1 && Globals::$generalSettings->newsletter_popup == 1) {
            if (helperGetCookie('nws_popup') != 1) {
                helperSetCookie('nws_popup', '1');
                return true;
            }
        }
        return false;
    }
}

//set active language ajax post
if (!function_exists('setActiveLangPostRequest')) {
    function setActiveLangPostRequest()
    {
        $sysLangId = clrNum(inputPost('sysLangId'));
        if (!empty($sysLangId) && Globals::$generalSettings->site_lang != $sysLangId) {
            $language = getLanguage($sysLangId);
            if (!empty($language)) {
                Globals::setActiveLanguage($language->id);
                Globals::updateLangBaseURL($language->short_form);
            }
        }
    }
}

//convert xml character
if (!function_exists('convertToXmlCharacter')) {
    function convertToXmlCharacter($string)
    {
        if (!empty($string)) {
            return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $string);
        }
    }
}