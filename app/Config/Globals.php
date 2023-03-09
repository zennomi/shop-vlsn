<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Globals extends BaseConfig
{
    private static $db = null;
    public static $generalSettings = array();
    public static $paymentSettings = array();
    public static $productSettings = array();
    public static $storageSettings = array();
    public static $settings = array();
    public static $customRoutes = array();
    public static $languages = array();
    public static $defaultLang = array();
    public static $languageTranslations = array();
    public static $rolesPermissions = array();
    public static $activeLang = array();
    public static $langBaseUrl = '';
    public static $langSegment = '';
    public static $authCheck = false;
    public static $authUser = null;
    public static $currencies = array();
    public static $defaultCurrency = array();
    public static $defaultLocation = array();

    public static function setGlobals()
    {
        self::$db = \Config\Database::connect();
        $session = \Config\Services::session();
        //set general settings
        self::$generalSettings = self::$db->table('general_settings')->where('id', 1)->get()->getRow();
        //set payment settings
        self::$paymentSettings = self::$db->table('payment_settings')->where('id', 1)->get()->getRow();
        //set payment settings
        self::$productSettings = self::$db->table('product_settings')->where('id', 1)->get()->getRow();
        //set storage settings
        self::$storageSettings = self::$db->table('storage_settings')->where('id', 1)->get()->getRow();
        //set routes
        $routes = self::$db->table('routes')->get()->getResult();
        self::$customRoutes = new \stdClass();
        if (!empty($routes)) {
            foreach ($routes as $route) {
                $routeKey = $route->route_key;
                self::$customRoutes->$routeKey = $route->route;
            }
        }
        //set languages
        self::$languages = self::$db->table('languages')->where('status', 1)->get()->getResult();
        //set roles permissions
        self::$rolesPermissions = self::$db->table('roles_permissions')->get()->getResult();
        //set timezone
        if (!empty(self::$generalSettings->timezone)) {
            date_default_timezone_set(self::$generalSettings->timezone);
        }
        //set active language
        self::$defaultLang = self::$db->table('languages')->where('id', self::$generalSettings->site_lang)->get()->getRow();
        if (empty(self::$defaultLang)) {
            self::$defaultLang = self::$db->table('languages')->get()->getFirstRow();
        }
        $langSegment = getSegmentValue(1);
        $langId = null;
        if (!empty(self::$languages)) {
            foreach (self::$languages as $lang) {
                if ($langSegment == $lang->short_form) {
                    $langId = $lang->id;
                    self::$langSegment = $lang->short_form;
                    break;
                }
            }
        }

        if (empty($langId)) {
            $langId = self::$defaultLang->id;
        }
        self::setActiveLanguage($langId);
        if (empty(self::$activeLang)) {
            self::$activeLang = self::$defaultLang;
        }
        //set language base URL
        self::$langBaseUrl = base_url(self::$activeLang->short_form);
        if (self::$activeLang->id == self::$defaultLang->id) {
            self::$langBaseUrl = base_url();
        }
        //set settings
        self::$settings = self::$db->table('settings')->where('lang_id', self::$activeLang->id)->get()->getRow();
        //authentication
        if (!empty($session->get('mds_ses_id')) && !empty($session->get('mds_ses_role_id')) && !empty($session->get('mds_ses_pass'))) {
            $user = self::$db->table('users')->join('roles_permissions', 'roles_permissions.id = users.role_id')
                ->where('users.id', clrNum($session->get('mds_ses_id')))->select('users.*, role_name, permissions, is_super_admin, is_admin, is_vendor, is_member')->get()->getRow();
            if (!empty($user) && md5($user->password ?? '') == $session->get('mds_ses_pass')) {
                self::$authCheck = true;
                self::$authUser = $user;
            }
        }
        //set currencies
        $currencies = self::$db->table('currencies')->orderBy('status DESC, id')->get()->getResult();
        if (!empty($currencies)) {
            foreach ($currencies as $currency) {
                self::$currencies[$currency->code] = $currency;
                if ($currency->code == self::$paymentSettings->default_currency) {
                    self::$defaultCurrency = $currency;
                }
            }
            if (empty(self::$defaultCurrency) && !empty($currency)) {
                self::$defaultCurrency = $currency;
            }
        }
        //default location
        $location = new \stdClass();
        $location->country_id = '';
        $location->state_id = '';
        $location->city_id = '';
        $sessLocation = $session->get('mds_default_location');
        if (!empty($sessLocation)) {
            $sessLocation = unserializeData($sessLocation);
            $location->country_id = $sessLocation->country_id;
            $location->state_id = $sessLocation->state_id;
            $location->city_id = $sessLocation->city_id;
        }
        self::$defaultLocation = $location;
    }

    public static function setActiveLanguage($langId)
    {
        if (!empty(self::$languages)) {
            foreach (self::$languages as $lang) {
                if ($langId == $lang->id) {
                    self::$activeLang = $lang;
                    //set language translations
                    self::$languageTranslations = self::$db->table('language_translations')->where('lang_id', self::$activeLang->id)->get()->getResult();
                    $arrayTranslations = array();
                    if (!empty(self::$languageTranslations)) {
                        foreach (self::$languageTranslations as $item) {
                            $arrayTranslations[$item->label] = $item->translation;
                        }
                    }
                    self::$languageTranslations = $arrayTranslations;
                    break;
                }
            }
        }
    }

    public static function updateLangBaseURL($shortForm)
    {
        self::$langBaseUrl = base_url($shortForm);
    }
}

Globals::setGlobals();
