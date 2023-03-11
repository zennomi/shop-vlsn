<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\EmailModel;
use PHPMailer\PHPMailer\Exception;

class AuthController extends BaseController
{

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /**
     * Login Post
     */
    public function loginPost()
    {
        //check auth
        if (authCheck()) {
            echo json_encode(['result' => 1]);
            exit();
        }
        $val = \Config\Services::validation();
        $val->setRule('email', trans("email_address"), 'required|max_length[255]');
        $val->setRule('password', trans("password"), 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            echo view('partials/_messages');
        } else {
            if ($this->authModel->login()) {
                echo json_encode(['result' => 1]);
            } else {
                $data = [
                    'result' => 0,
                    'errorMessage' => view('partials/_messages')
                ];
                echo json_encode($data);
            }
            resetFlashData();
        }
    }

    /**
     * Connect with Facebook
     */
    public function connectWithFacebook()
    {
        $state = generateToken();
        $fbUrl = "https://www.facebook.com/v2.10/dialog/oauth?client_id=" . $this->generalSettings->facebook_app_id . "&redirect_uri=" . langBaseUrl() . "/facebook-callback&scope=email&state=" . $state;
        $this->session->set('oauth2state', $state);
        $this->session->set('fbLoginReferrer', previous_url());
        return redirect()->to($fbUrl);
    }

    /**
     * Facebook Callback
     */
    public function facebookCallback()
    {
        require_once APPPATH . "ThirdParty/facebook/vendor/autoload.php";
        $provider = new \League\OAuth2\Client\Provider\Facebook([
            'clientId' => $this->generalSettings->facebook_app_id,
            'clientSecret' => $this->generalSettings->facebook_app_secret,
            'redirectUri' => langBaseUrl() . '/facebook-callback',
            'graphApiVersion' => 'v2.10',
        ]);
        if (!isset($_GET['code'])) {
            echo 'Error: Invalid Login';
            exit();
            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $this->session->get('oauth2state'))) {
            $this->session->remove('oauth2state');
            echo 'Error: Invalid State';
            exit();
        }
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
        try {
            $user = $provider->getResourceOwner($token);
            $fbUser = new \stdClass();
            $fbUser->id = $user->getId();
            $fbUser->email = $user->getEmail();
            $fbUser->name = $user->getName();
            $fbUser->firstName = $user->getFirstName();
            $fbUser->lastName = $user->getLastName();
            $fbUser->pictureURL = $user->getPictureUrl();
            $model = new AuthModel();
            $model->loginWithFacebook($fbUser);
            if (!empty($this->session->get('fbLoginReferrer'))) {
                return redirect()->to($this->session->get('fbLoginReferrer'));
            } else {
                return redirect()->to(langBaseUrl());
            }
        } catch (\Exception $e) {
            echo 'Error: Invalid User';
            exit();
        }
    }

    /**
     * Connect with Google
     */
    public function connectWithGoogle()
    {
        require_once APPPATH . 'ThirdParty/google/vendor/autoload.php';
        $provider = new \League\OAuth2\Client\Provider\Google([
            'clientId' => $this->generalSettings->google_client_id,
            'clientSecret' => $this->generalSettings->google_client_secret,
            'redirectUri' => base_url('connect-with-google'),
        ]);

        if (!empty($_GET['error'])) {
            exit('Got error: ' . esc($_GET['error'], ENT_QUOTES, 'UTF-8'));
        } elseif (empty($_GET['code'])) {
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            $this->session->set('gLoginReferrer', previous_url());
            return redirect()->to($authUrl);
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        } else {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
            try {
                $user = $provider->getResourceOwner($token);
                $gUser = new \stdClass();
                $gUser->id = $user->getId();
                $gUser->email = $user->getEmail();
                $gUser->name = $user->getName();
                $gUser->firstName = $user->getFirstName();
                $gUser->lastName = $user->getLastName();
                $gUser->avatar = $user->getAvatar();

                $model = new AuthModel();
                $model->loginWithGoogle($gUser);
                if (!empty($this->session->get('gLoginReferrer'))) {
                    return redirect()->to($this->session->get('gLoginReferrer'));
                } else {
                    return redirect()->to(langBaseUrl());
                }
            } catch (Exception $e) {
                exit('Something went wrong: ' . $e->getMessage());
            }
        }
    }

    /**
     * Connect with VK
     */
    public function connectWithVK()
    {
        require_once APPPATH . "ThirdParty/vkontakte/vendor/autoload.php";
        $provider = new \J4k\OAuth2\Client\Provider\Vkontakte([
            'clientId' => $this->generalSettings->vk_app_id,
            'clientSecret' => $this->generalSettings->vk_secure_key,
            'redirectUri' => base_url('connect-with-vk'),
            'scopes' => ['email'],
        ]);
        // Authorize if needed
        if (PHP_SESSION_NONE === session_status()) session_start();
        $isSessionActive = PHP_SESSION_ACTIVE === session_status();
        $code = !empty($_GET['code']) ? $_GET['code'] : null;
        $state = !empty($_GET['state']) ? $_GET['state'] : null;
        $sessionState = 'oauth2state';
        // No code – get some
        if (!$code) {
            $authUrl = $provider->getAuthorizationUrl();
            if ($isSessionActive) $_SESSION[$sessionState] = $provider->getState();
            $this->session->set('vkLoginReferrer', previous_url());
            return redirect()->to($authUrl);
        } // Anti-CSRF
        elseif ($isSessionActive && (empty($state) || ($state !== $_SESSION[$sessionState]))) {
            unset($_SESSION[$sessionState]);
            throw new \RuntimeException('Invalid state');
        } else {
            try {
                $providerAccessToken = $provider->getAccessToken('authorization_code', ['code' => $code]);
                $user = $providerAccessToken->getValues();
                //get user details with cURL
                $url = 'http://api.vk.com/method/users.get?uids=' . $providerAccessToken->getValues()['user_id'] . '&access_token=' . $providerAccessToken->getToken() . '&v=5.95&fields=photo_200,status';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                $response = curl_exec($ch);
                curl_close($ch);

                $userDetails = json_decode($response);
                $vkUser = new \stdClass();
                $vkUser->id = $providerAccessToken->getValues()['user_id'];
                $vkUser->email = $providerAccessToken->getValues()['email'];
                $vkUser->name = @$userDetails->response['0']->first_name . " " . @$userDetails->response['0']->last_name;
                $vkUser->firstName = @$userDetails->response['0']->first_name;
                $vkUser->lastName = @$userDetails->response['0']->last_name;
                $vkUser->avatar = @$userDetails->response['0']->photo_200;

                $model = new AuthModel();
                $model->loginWithVK($vkUser);
                if (!empty($this->session->get('vkLoginReferrer'))) {
                    return redirect()->to($this->session->get('vkLoginReferrer'));
                } else {
                    return redirect()->to(langBaseUrl());
                }
            } catch (IdentityProviderException $e) {
                error_log($e->getMessage());
            }
        }
    }

    /**
     * Register
     */
    public function register()
    {
        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("register");
        $data['description'] = trans("register") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("register") . ',' . $this->baseVars->appName;
        
        echo view('partials/_header', $data);
        echo view('auth/register');
        echo view('partials/_footer');
    }

    /**
     * Register Post
     */
    public function registerPost()
    {
        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        if ($this->baseVars->recaptchaStatus) {
            if (reCAPTCHA('validate') == 'invalid') {
                setErrorMessage(trans("msg_recaptcha"));
                return redirect()->to(generateUrl('register'));
            }
        }
        $val = \Config\Services::validation();
        $val->setRule('email', trans("email_address"), 'required|max_length[255]');
        $val->setRule('password', trans("password"), 'required|min_length[4]|max_length[255]');
        $val->setRule('confirm_password', trans("password_confirm"), 'required|matches[password]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(generateUrl('register'))->withInput();
        } else {
            $email = inputPost('email');
            if (!$this->authModel->isEmailUnique($email)) {
                setErrorMessage(trans("msg_email_unique_error"));
                return redirect()->to(generateUrl('register'))->withInput();
            }
            if ($this->authModel->register()) {
                setSuccessMessage(trans("msg_register_success"));
                return redirect()->to(generateUrl('settings', 'edit_profile'));
            }
        }
        setErrorMessage(trans("msg_error"));
        return redirect()->to(generateUrl('register'));
    }

    /**
     * Register Success
     */
    public function registerSuccess()
    {
        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("register");
        $data['description'] = trans("register") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("register") . ',' . $this->baseVars->appName;
        $token = inputGet('u');
        $data['user'] = $this->authModel->getUserByToken($token);
        if (empty($data['user']) || $data['user']->email_status == 1) {
            return redirect()->to(langBaseUrl());
        }

        echo view('partials/_header', $data);
        echo view('auth/register_success', $data);
        echo view('partials/_footer');
    }

    /**
     * Confirm Account
     */
    public function confirmAccount()
    {
        $data['title'] = trans("confirm_your_account");
        $data['description'] = trans("confirm_your_account") . " - " . $this->baseVars->appName;
        $data['keywords'] = trans("confirm_your_account") . "," . $this->baseVars->appName;

        $token = trim(inputGet('token') ?? '');
        $data['user'] = $this->authModel->getUserByToken($token);
        if (empty($data['user'])) {
            return redirect()->to(langBaseUrl());
        }
        if ($data['user']->email_status == 1) {
            return redirect()->to(langBaseUrl());
        }
        if ($this->authModel->verifyEmail($data['user'])) {
            $data['success'] = trans("msg_confirmed");
        } else {
            $data['error'] = trans("msg_error");
        }
        echo view('partials/_header', $data);
        echo view('auth/confirm_email', $data);
        echo view('partials/_footer');
    }

    /**
     * Forgot Password
     */
    public function forgotPassword()
    {
        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("reset_password");
        $data['description'] = trans("reset_password") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("reset_password") . ',' . $this->baseVars->appName;

        echo view('partials/_header', $data);
        echo view('auth/forgot_password');
        echo view('partials/_footer');
    }

    /**
     * Forgot Password Post
     */
    public function forgotPasswordPost()
    {
        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $email = inputPost('email');
        $user = $this->authModel->getUserByEmail($email);
        if (empty($user)) {
            setErrorMessage(trans("msg_reset_password_error"));
            return redirect()->to(generateUrl('forgot_password'));
        } else {
            $token = $user->token;
            if (empty($token)) {
                $token = generateToken();
                $this->authModel->updateUserToken($user->id, $token);
            }
            $emailData = [
                'email_type' => 'reset_password',
                'email_address' => $user->email,
                'email_data' => serialize([
                    'content' => trans("email_reset_password"),
                    'url' => generateUrl("reset_password") . '?token=' . $token,
                    'buttonText' => trans("reset_password")
                ]),
                'email_priority' => 1,
                'email_subject' => trans("reset_password"),
                'template_path' => 'email/main'
            ];
            addToEmailQueue($emailData);
            setSuccessMessage(trans("msg_reset_password_success"));
            return redirect()->to(generateUrl('forgot_password'));
        }
    }

    /**
     * Reset Password
     */
    public function resetPassword()
    {
        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("reset_password");
        $data['description'] = trans("reset_password") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("reset_password") . ',' . $this->baseVars->appName;
        $token = inputGet('token');
        $data['user'] = $this->authModel->getUserByToken($token);
        $data['success'] = $this->session->getFlashdata('success');
        if (empty($data['user']) && empty($data['success'])) {
            return redirect()->to(langBaseUrl());
        }

        echo view('partials/_header', $data);
        echo view('auth/reset_password');
        echo view('partials/_footer');
    }

    /**
     * Reset Password Post
     */
    public function resetPasswordPost()
    {
        $success = inputPost('success');
        if ($success == 1) {
            return redirect()->to(langBaseUrl());
        }
        $val = \Config\Services::validation();
        $val->setRule('password', trans("new_password"), 'required|min_length[4]|max_length[255]');
        $val->setRule('password_confirm', trans("password_confirm"), 'required|matches[password]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $token = inputPost('token');
            $user = $this->authModel->getUserByToken($token);
            if (!empty($user)) {
                if ($this->authModel->resetPassword($user)) {
                    setSuccessMessage(trans("msg_change_password_success"));
                    return redirect()->back();
                }
                setErrorMessage(trans("msg_change_password_error"));
                return redirect()->back()->withInput();
            }
        }
    }

    /**
     * Send Activation Email
     */
    public function sendActivationEmailPost()
    {
        $token = inputPost('token');
        $user = $this->authModel->getUserByToken($token);
        if(!empty($user)){
            $this->authModel->addActivationEmail($user);
        }
        $emailModel = new EmailModel();
        $emailModel->runEmailQueue();
        $data = [
            'result' => 1,
            'successMessage' => '<div class="text-success text-center m-b-15">' . trans("activation_email_sent") . '</div>'
        ];
        echo json_encode($data);
    }
}
