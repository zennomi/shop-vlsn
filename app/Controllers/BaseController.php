<?php

namespace App\Controllers;

use App\Models\AdModel;
use App\Models\AuthModel;
use App\Models\CategoryModel;
use App\Models\CommonModel;
use App\Models\CurrencyModel;
use App\Models\LocationModel;
use App\Models\MembershipModel;
use App\Models\PageModel;
use App\Models\ProductModel;
use App\Models\SettingsModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Globals;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['text', 'security', 'app', 'product'];

    public $session;
    public $settingsModel;
    public $authModel;
    public $pageModel;
    public $locationModel;
    public $categoryModel;
    public $productModel;
    public $commonModel;
    public $generalSettings;
    public $paymentSettings;
    public $productSettings;
    public $settings;
    public $activeLanguages;
    public $activeLang;
    public $currencies;
    public $defaultCurrency;
    public $selectedCurrency;
    public $activeFonts;
    public $menuLinks;
    public $activeCountries;
    public $categoriesArray;
    public $parentCategories;
    public $adSpaces;
    public $baseVars;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {

        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $this->settingsModel = new SettingsModel();
        $this->authModel = new AuthModel();
        $this->pageModel = new PageModel();
        $this->locationModel = new LocationModel();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
        $this->commonModel = new CommonModel();

        //general settings
        $this->generalSettings = Globals::$generalSettings;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            setActiveLangPostRequest();
        }

        //payment settings
        $this->paymentSettings = Globals::$paymentSettings;
        //product settings
        $this->productSettings = Globals::$productSettings;
        //settings
        $this->settings = Globals::$settings;
        //active languages
        $this->activeLanguages = Globals::$languages;
        //active lang
        $this->activeLang = Globals::$activeLang;
        //currencies
        $this->currencies = Globals::$currencies;
        //default currency
        $this->defaultCurrency = Globals::$defaultCurrency;
        //default currency
        $this->selectedCurrency = getSelectedCurrency();
        //fonts
        $this->activeFonts = $this->settingsModel->getSelectedFonts($this->settings);
        //menu links
        $this->menuLinks = $this->pageModel->getMenuLinks($this->activeLang->id);
        //active countries
        $this->activeCountries = $this->locationModel->getActiveCountries();
        //categories array
        $this->categoriesArray = $this->categoryModel->getCategoriesArray();
        //parent categories
        $this->parentCategories = $this->categoryModel->getParentCategories();
        //ad spaces
        $this->adSpaces = $this->commonModel->getAdSpaces();

        //variables
        $this->baseVars = new \stdClass();
        $this->baseVars->appName = $this->generalSettings->application_name;
        $this->baseVars->rtl = false;
        $this->baseVars->unreadMessageCount = 0;
        $this->baseVars->usernameMaxlength = 40;
        $this->baseVars->perPage = 15;
        $this->baseVars->perPageProducts = 24;
        $this->baseVars->recaptchaStatus = false;
        $this->baseVars->defaultLocation = Globals::$defaultLocation;
        $this->baseVars->defaultLocationInput = $this->locationModel->getDefaultLocationInput($this->baseVars->defaultLocation);
        $this->baseVars->isSaleActive = false;
        if ($this->activeLang->text_direction == 'rtl') {
            $this->baseVars->rtl = true;
        }
        if (isRecaptchaEnabled()) {
            $this->baseVars->recaptchaStatus = true;
        }
        if ($this->generalSettings->marketplace_system == 1 || $this->generalSettings->bidding_system == 1) {
            $this->baseVars->isSaleActive = true;
        }
        $this->baseVars->thousandsSeparator = '.';
        $this->baseVars->inputInitialPrice = '0.00';
        if ($this->defaultCurrency->currency_format == 'european') {
            $this->baseVars->thousandsSeparator = ',';
            $this->baseVars->inputInitialPrice = '0,00';
        }
        if (authCheck()) {
            $this->baseVars->unreadMessageCount = getUnreadConversationsCount(user()->id);
            $this->authModel->updateLastSeen();
        }
        //maintenance mode
        if ($this->generalSettings->maintenance_mode_status == 1) {
            if (!isAdmin()) {
                echo view('maintenance', ['generalSettings' => $this->generalSettings, 'baseSettings' => $this->settings]);
            }
        }

        if (checkCronTime(1)) {
            //update currency rates
            if ($this->paymentSettings->auto_update_exchange_rates == 1) {
                $currencyModel = new CurrencyModel();
                $currencyModel->updateCurrencyRates();
            }
            //check promoted products
            $this->productModel->checkPromotedProducts();
            //check users membership plans
            $membershipModel = new MembershipModel();
            $membershipModel->checkMembershipPlansExpired();
            //delete old sessions
            $this->settingsModel->deleteOldSessions();
            //update cron time
            $this->settingsModel->setLastCronUpdate();
        }

        //view variables
        $view = \Config\Services::renderer();
        $view->setData(['generalSettings' => $this->generalSettings, 'paymentSettings' => $this->paymentSettings, 'productSettings' => $this->productSettings, 'baseSettings' => $this->settings, 'activeLanguages' => $this->activeLanguages, 'activeLang' => $this->activeLang, 'currencies' => $this->currencies, 'defaultCurrency' => $this->defaultCurrency, 'selectedCurrency' => $this->selectedCurrency, 'activeFonts' => $this->activeFonts, 'menuLinks' => $this->menuLinks,
            'activeCountries' => $this->activeCountries, 'categoriesArray' => $this->categoriesArray, 'parentCategories' => $this->parentCategories, 'adSpaces' => $this->adSpaces, 'baseVars' => $this->baseVars]);
    }
}
