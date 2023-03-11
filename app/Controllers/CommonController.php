<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\SitemapModel;
use CodeIgniter\Controller;
use Config\Globals;

class CommonController extends Controller
{
    protected $authModel;
    protected $session;
    protected $generalSettings;
    protected $settings;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authModel = new AuthModel();
        $this->session = \Config\Services::session();
        $this->generalSettings = Globals::$generalSettings;
        $this->settings = Globals::$settings;
    }

    /**
     * Admin Login
     */
    public function adminLogin()
    {
        if (authCheck()) {
            return redirect()->to(adminUrl());
        }
        $data['title'] = trans("login");
        $data['description'] = trans("login") . " - " . $this->settings->site_title;
        $data['keywords'] = trans("login") . ', ' . $this->generalSettings->application_name;
        
        $data['generalSettings'] = $this->generalSettings;
        $data['baseSettings'] = $this->settings;
        echo view('admin/login', $data);
    }

    /**
     * Admin Login Post
     */
    public function adminLoginpost()
    {
        $val = \Config\Services::validation();
        $val->setRule('email', trans("form_email"), 'required|max_length[200]');
        $val->setRule('password', trans("form_password"), 'required|max_length[200]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $authModel = new AuthModel();
            $user = $authModel->getUserByEmail(inputPost('email'));
            if (!empty($user) && !hasPermission('admin_panel', $user) && $this->generalSettings->maintenance_mode_status == 1) {
                $this->session->setFlashdata('error', "Site under construction! Please try again later.");
                return redirect()->to(adminUrl('login'));
            }
            if ($authModel->login()) {
                return redirect()->to(adminUrl());
            } else {
                $this->session->setFlashdata('error', trans("login_error"));
                return redirect()->to(adminUrl('login'));
            }
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->authModel->logout();
        return redirect()->back();
    }
}
