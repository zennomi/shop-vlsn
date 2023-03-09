<?php

namespace App\Controllers;

use App\Models\AdModel;
use App\Models\AuthModel;
use App\Models\CategoryModel;
use App\Models\CommonModel;
use App\Models\FileModel;
use App\Models\LocationModel;
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
abstract class BaseAdminController extends Controller
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
    public $commonModel;
    public $categoryModel;
    public $fileModel;
    public $generalSettings;
    public $paymentSettings;
    public $productSettings;
    public $storageSettings;
    public $settings;
    public $activeLanguages;
    public $activeLang;
    public $defaultCurrency;
    public $perPage;
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
        $this->commonModel = new CommonModel();
        $this->categoryModel = new CategoryModel();
        $this->fileModel = new FileModel();
        //check auth
        if (!authCheck()) {
            redirectToUrl(adminUrl('login'));
            exit();
        }
        //check admin
        if (!isAdmin()) {
            redirectToUrl(langBaseUrl());
            exit();
        }
        //general settings
        $this->generalSettings = Globals::$generalSettings;
        //payment settings
        $this->paymentSettings = Globals::$paymentSettings;
        //product settings
        $this->productSettings = Globals::$productSettings;
        //storage settings
        $this->storageSettings = Globals::$storageSettings;
        //settings
        $this->settings = Globals::$settings;

        //set control panel lang
        if (!empty($this->session->get('mds_control_panel_lang'))) {
            Globals::setActiveLanguage($this->session->get('mds_control_panel_lang'));
        }

        //active languages
        $this->activeLanguages = Globals::$languages;
        //active lang
        $this->activeLang = Globals::$activeLang;
        //default currency
        $this->defaultCurrency = Globals::$defaultCurrency;

        //per page
        $this->perPage = 15;
        if (!empty(clrNum(inputGet('show')))) {
            $this->perPage = clrNum(inputGet('show'));
        }

        //variables
        $this->baseVars = new \stdClass();
        $this->baseVars->rtl = false;
        $this->baseVars->thousandsSeparator = '.';
        $this->baseVars->inputInitialPrice = '0.00';
        if ($this->defaultCurrency->currency_format == 'european') {
            $this->baseVars->thousandsSeparator = ',';
            $this->baseVars->inputInitialPrice = '0,00';
        }

        //maintenance mode
        if ($this->generalSettings->maintenance_mode_status == 1) {
            if (!isAdmin()) {
                $authModel = new AuthModel();
                $authModel->logout();
                redirectToUrl(adminUrl('login'));
                exit();
            }
        }

        //view variables
        $view = \Config\Services::renderer();
        $view->setData(['generalSettings' => $this->generalSettings, 'paymentSettings' => $this->paymentSettings, 'productSettings' => $this->productSettings, 'baseSettings' => $this->settings, 'activeLanguages' => $this->activeLanguages, 'activeLang' => $this->activeLang,
            'defaultCurrency' => $this->defaultCurrency, 'baseVars' => $this->baseVars]);
    }
}
