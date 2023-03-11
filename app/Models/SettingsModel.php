<?php namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends BaseModel
{
    protected $builder;
    protected $builderGeneral;
    protected $builderStorage;
    protected $builderFonts;
    protected $builderPaymentSettings;
    protected $builderPaymentGateways;
    protected $builderProductSettings;
    protected $builderRoutes;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('settings');
        $this->builderGeneral = $this->db->table('general_settings');
        $this->builderStorage = $this->db->table('storage_settings');
        $this->builderFonts = $this->db->table('fonts');
        $this->builderPaymentSettings = $this->db->table('payment_settings');
        $this->builderPaymentGateways = $this->db->table('payment_gateways');
        $this->builderProductSettings = $this->db->table('product_settings');
        $this->builderRoutes = $this->db->table('routes');
    }

    //edit homepage manager settings
    public function editHomepageManagerSettings()
    {
        $data = [
            'featured_categories' => inputPost('featured_categories'),
            'index_promoted_products' => inputPost('index_promoted_products'),
            'index_latest_products' => inputPost('index_latest_products'),
            'index_blog_slider' => inputPost('index_blog_slider'),
            'index_promoted_products_count' => inputPost('index_promoted_products_count'),
            'index_latest_products_count' => inputPost('index_latest_products_count')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update settings
    public function updateSettings()
    {
        $data = [
            'site_title' => inputPost('site_title'),
            'homepage_title' => inputPost('homepage_title'),
            'site_description' => inputPost('site_description'),
            'keywords' => inputPost('keywords'),
            'facebook_url' => inputPost('facebook_url'),
            'twitter_url' => inputPost('twitter_url'),
            'instagram_url' => inputPost('instagram_url'),
            'pinterest_url' => inputPost('pinterest_url'),
            'linkedin_url' => inputPost('linkedin_url'),
            'vk_url' => inputPost('vk_url'),
            'whatsapp_url' => inputPost('whatsapp_url'),
            'telegram_url' => inputPost('telegram_url'),
            'youtube_url' => inputPost('youtube_url'),
            'about_footer' => inputPost('about_footer'),
            'contact_text' => inputPost('contact_text'),
            'contact_address' => inputPost('contact_address'),
            'contact_email' => inputPost('contact_email'),
            'contact_phone' => inputPost('contact_phone'),
            'copyright' => inputPost('copyright'),
            'cookies_warning' => inputPost('cookies_warning'),
            'cookies_warning_text' => inputPost('cookies_warning_text')
        ];
        $langId = inputPost('lang_id');
        $language = getLanguage($langId);
        if (!empty($language)) {
            return $this->builder->where('lang_id', $language->id)->update($data);
        }
        return false;
    }

    //update general settings
    public function updateGeneralSettings()
    {
        $data = [
            'application_name' => inputPost('application_name'),
            'custom_header_codes' => inputPost('custom_header_codes'),
            'custom_footer_codes' => inputPost('custom_footer_codes'),
            'facebook_comment_status' => inputPost('facebook_comment_status'),
            'facebook_comment' => inputPost('facebook_comment')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update recaptcha settings
    public function updateRecaptchaSettings()
    {
        $data = [
            'recaptcha_site_key' => inputPost('recaptcha_site_key'),
            'recaptcha_secret_key' => inputPost('recaptcha_secret_key')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update maintenance mode settings
    public function updateMaintenanceModeSettings()
    {
        $data = [
            'maintenance_mode_title' => inputPost('maintenance_mode_title'),
            'maintenance_mode_description' => inputPost('maintenance_mode_description'),
            'maintenance_mode_status' => inputPost('maintenance_mode_status'),
        ];
        if (empty($data['maintenance_mode_status'])) {
            $data['maintenance_mode_status'] = 0;
        }
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update email settings
    public function updateEmailSettings()
    {
        $data = [
            'mail_protocol' => inputPost('mail_protocol'),
            'mail_service' => inputPost('mail_service'),
            'mail_title' => inputPost('mail_title'),
            'mail_encryption' => inputPost('mail_encryption'),
            'mail_host' => inputPost('mail_host'),
            'mail_port' => inputPost('mail_port'),
            'mail_username' => inputPost('mail_username'),
            'mail_password' => inputPost('mail_password'),
            'mail_reply_to' => inputPost('mail_reply_to'),
            'mailjet_api_key' => inputPost('mailjet_api_key'),
            'mailjet_secret_key' => inputPost('mailjet_secret_key'),
            'mailjet_email_address' => inputPost('mailjet_email_address')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update email options
    public function updateEmailOptions()
    {
        $data = [
            'email_verification' => inputPost('email_verification'),
            'send_email_new_product' => inputPost('send_email_new_product'),
            'send_email_buyer_purchase' => inputPost('send_email_buyer_purchase'),
            'send_email_order_shipped' => inputPost('send_email_order_shipped'),
            'send_email_contact_messages' => inputPost('send_email_contact_messages'),
            'send_email_shop_opening_request' => inputPost('send_email_shop_opening_request'),
            'send_email_bidding_system' => inputPost('send_email_bidding_system'),
            'mail_options_account' => inputPost('mail_options_account')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update social login
    public function updateSocialLoginSettings($submit)
    {
        if ($submit == 'facebook') {
            $data = [
                'facebook_app_id' => inputPost('facebook_app_id'),
                'facebook_app_secret' => inputPost('facebook_app_secret')
            ];
        }
        if ($submit == 'google') {
            $data = [
                'google_client_id' => inputPost('google_client_id'),
                'google_client_secret' => inputPost('google_client_secret')
            ];
        }
        if ($submit == 'vk') {
            $data = [
                'vk_app_id' => inputPost('vk_app_id'),
                'vk_secure_key' => inputPost('vk_secure_key')
            ];
        }
        if (!empty($data)) {
            return $this->builderGeneral->where('id', 1)->update($data);
        }
        return false;
    }

    //update seo tools
    public function updateSeoTools()
    {
        $data = [
            'google_analytics' => inputPost('google_analytics')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update payment gateway
    public function updatePaymentGateway($nameKey)
    {
        $gateway = $this->getPaymentGateway($nameKey);
        if (!empty($gateway)) {
            $data = [
                'public_key' => inputPost('public_key'),
                'secret_key' => inputPost('secret_key'),
                'environment' => !empty(inputPost('environment')) ? inputPost('environment') : 'production',
                'status' => !empty(inputPost('status')) ? 1 : 0,
            ];
            $paymentSettings = $this->getPaymentSettings();
            if (!empty($paymentSettings) && $paymentSettings->currency_converter == 1) {
                $data['base_currency'] = inputPost('base_currency');
            }
            return $this->builderPaymentGateways->where('name_key', cleanStr($nameKey))->update($data);
        }
        return false;
    }

    //update bank transfer settings
    public function updateBankTransferSettings()
    {
        $data = [
            'bank_transfer_enabled' => inputPost('bank_transfer_enabled'),
            'bank_transfer_accounts' => inputPost('bank_transfer_accounts')
        ];
        return $this->builderPaymentSettings->where('id', 1)->update($data);
    }

    //update cash on delivery settings
    public function updateCashOnDeliverySettings()
    {
        $data = [
            'cash_on_delivery_enabled' => inputPost('cash_on_delivery_enabled')
        ];
        return $this->builderPaymentSettings->where('id', 1)->update($data);
    }

    //get payment gateway
    public function getPaymentGateway($nameKey)
    {
        return $this->builderPaymentGateways->where('name_key', strSlug($nameKey))->get()->getRow();
    }

    //get active payment gateways
    public function getActivePaymentGateways()
    {
        return $this->builderPaymentGateways->where('status', 1)->get()->getResult();
    }

    //update pricing settings
    public function updateFeaturedProductsPricingSettings()
    {
        $data = [
            'price_per_day' => inputPost('price_per_day'),
            'price_per_month' => inputPost('price_per_month'),
            'free_product_promotion' => inputPost('free_product_promotion')
        ];
        $data['price_per_day'] = getPrice($data['price_per_day'], 'database');
        $data['price_per_month'] = getPrice($data['price_per_month'], 'database');
        return $this->builderPaymentSettings->where('id', 1)->update($data);
    }

    //update preferences
    public function updatePreferences($form)
    {
        if ($form == 'homepage') {
            $data = [
                'index_promoted_products' => inputPost('index_promoted_products'),
            ];
        } elseif ($form == 'general') {
            $data = [
                'multilingual_system' => inputPost('multilingual_system'),
                'rss_system' => inputPost('rss_system'),
                'vendor_verification_system' => inputPost('vendor_verification_system'),
                'hide_vendor_contact_information' => inputPost('hide_vendor_contact_information'),
                'guest_checkout' => inputPost('guest_checkout'),
                'location_search_header' => inputPost('location_search_header'),
                'pwa_status' => inputPost('pwa_status')
            ];
        } elseif ($form == 'products') {
            $data = [
                'approve_before_publishing' => inputPost('approve_before_publishing'),
                'promoted_products' => inputPost('promoted_products'),
                'vendor_bulk_product_upload' => inputPost('vendor_bulk_product_upload'),
                'show_sold_products' => inputPost('show_sold_products'),
                'product_link_structure' => inputPost('product_link_structure')
            ];
        } elseif ($form == 'reviews_comments') {
            $data = [
                'reviews' => inputPost('reviews'),
                'product_comments' => inputPost('product_comments'),
                'blog_comments' => inputPost('blog_comments'),
                'comment_approval_system' => inputPost('comment_approval_system')
            ];
        } elseif ($form == 'shop') {
            $data = [
                'show_customer_email_seller' => inputPost('show_customer_email_seller'),
                'show_customer_phone_seller' => inputPost('show_customer_phone_seller'),
                'request_documents_vendors' => inputPost('request_documents_vendors'),
                'explanation_documents_vendors' => inputPost('explanation_documents_vendors')
            ];
        }
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update visual settings
    public function updateVisualSettings()
    {
        $data = ['site_color' => inputPost('site_color')];

        $uploadModel = new UploadModel();
        $logo = $uploadModel->uploadLogo('logo');
        if (!empty($logo) && !empty($logo['path'])) {
            deleteFile($this->generalSettings->logo);
            $data['logo'] = $logo['path'];
        }
        $logoEmail = $uploadModel->uploadLogo('logo_email');
        if (!empty($logoEmail) && !empty($logoEmail['path'])) {
            deleteFile($this->generalSettings->logo_email);
            $data['logo_email'] = $logoEmail['path'];
        }
        $favicon = $uploadModel->uploadLogo('favicon');
        if (!empty($favicon) && !empty($favicon['path'])) {
            deleteFile($this->generalSettings->favicon);
            $data['favicon'] = $favicon['path'];
        }
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update watermark settings
    public function updateWatermarkSettings()
    {
        $data = [
            'watermark_text' => inputPost('watermark_text'),
            'watermark_font_size' => inputPost('watermark_font_size'),
            'watermark_product_images' => inputPost('watermark_product_images'),
            'watermark_blog_images' => inputPost('watermark_blog_images'),
            'watermark_thumbnail_images' => inputPost('watermark_thumbnail_images'),
            'watermark_vrt_alignment' => inputPost('watermark_vrt_alignment'),
            'watermark_hor_alignment' => inputPost('watermark_hor_alignment')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update cache system
    public function updateCacheSystem()
    {
        $data = [
            'cache_system' => inputPost('cache_system'),
            'refresh_cache_database_changes' => inputPost('refresh_cache_database_changes'),
            'cache_refresh_time' => inputPost('cache_refresh_time') * 60
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update storage settings
    public function updateStorageSettings()
    {
        $data = ['storage' => inputPost('storage')];
        return $this->builderStorage->where('id', 1)->update($data);
    }

    //update system settings
    public function updateSystemSettings()
    {
        $data = [
            'physical_products_system' => inputPost('physical_products_system'),
            'digital_products_system' => inputPost('digital_products_system'),
            'marketplace_system' => inputPost('marketplace_system'),
            'classified_ads_system' => inputPost('classified_ads_system'),
            'bidding_system' => inputPost('bidding_system'),
            'selling_license_keys_system' => inputPost('selling_license_keys_system'),
            'multi_vendor_system' => inputPost('multi_vendor_system'),
            'vat_status' => inputPost('vat_status'),
            'commission_rate' => inputPost('commission_rate'),
            'timezone' => trim(inputPost('timezone'))
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //get routes
    public function getRoutes()
    {
        return $this->builderRoutes->get()->getResult();
    }

    //get route by key
    public function getRouteByKey($key)
    {
        return $this->builderRoutes->where('route_key', cleanStr($key))->get()->getRow();
    }

    //update route settings
    public function updateRouteSettings()
    {
        $routes = $this->getRoutes();
        if (!empty($routes)) {
            foreach ($routes as $route) {
                $data = [
                    'route' => inputPost('route_' . $route->id)
                ];
                $this->builderRoutes->where('id', $route->id)->update($data);
            }
        }
        return true;
    }

    //update aws s3 settings
    public function updateAwsS3Settings()
    {
        $data = [
            'aws_key' => inputPost('aws_key'),
            'aws_secret' => inputPost('aws_secret'),
            'aws_bucket' => inputPost('aws_bucket'),
            'aws_region' => inputPost('aws_region')
        ];
        return $this->builderStorage->where('id', 1)->update($data);
    }

    //edit navigation
    public function editNavigation()
    {
        $data = [
            'menu_limit' => inputPost('menu_limit'),
            'selected_navigation' => inputPost('navigation')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //get payment settings
    public function getPaymentSettings()
    {
        return $this->builderPaymentSettings->where('id', 1)->get()->getRow();
    }

    //get storage settings
    public function getStorageSettings()
    {
        return $this->builderStorage->where('id', 1)->get()->getRow();
    }

    //get settings
    public function getSettings($langId)
    {
        return $this->builder->where('lang_id', clrNum($langId))->get()->getRow();
    }

    //update product settings
    public function updateProductSettings()
    {
        $submit = inputPost('submit');
        if ($submit == 'marketplace') {
            $data = [
                'marketplace_sku' => getCheckboxValue(inputPost('marketplace_sku')),
                'marketplace_variations' => getCheckboxValue(inputPost('marketplace_variations')),
                'marketplace_shipping' => getCheckboxValue(inputPost('marketplace_shipping')),
                'marketplace_product_location' => getCheckboxValue(inputPost('marketplace_product_location'))
            ];
        } elseif ($submit == 'classified_ads') {
            $data = [
                'classified_price' => getCheckboxValue(inputPost('classified_price')),
                'classified_price_required' => getCheckboxValue(inputPost('classified_price_required')),
                'classified_product_location' => getCheckboxValue(inputPost('classified_product_location')),
                'classified_external_link' => getCheckboxValue(inputPost('classified_external_link'))
            ];
        } elseif ($submit == 'physical_products') {
            $data = [
                'physical_demo_url' => getCheckboxValue(inputPost('physical_demo_url')),
                'physical_video_preview' => getCheckboxValue(inputPost('physical_video_preview')),
                'physical_audio_preview' => getCheckboxValue(inputPost('physical_audio_preview'))
            ];
        } elseif ($submit == 'digital_products') {
            $data = [
                'digital_demo_url' => getCheckboxValue(inputPost('digital_demo_url')),
                'digital_video_preview' => getCheckboxValue(inputPost('digital_video_preview')),
                'digital_audio_preview' => getCheckboxValue(inputPost('digital_audio_preview')),
                'digital_allowed_file_extensions' => ''
            ];
            $extArray = @explode(',', inputPost('digital_allowed_file_extensions'));
            if (!empty($extArray)) {
                $exts = json_encode($extArray);
                if (!empty($exts)) {
                    $exts = str_replace('[', '', $exts);
                    $exts = str_replace(']', '', $exts);
                    $exts = str_replace('.', '', $exts);
                    $exts = strtolower($exts);
                }
                $data['digital_allowed_file_extensions'] = $exts;
            }
        } elseif ($submit == 'file_upload') {
            $data = [
                'product_image_limit' => inputPost('product_image_limit'),
                'max_file_size_image' => inputPost('max_file_size_image') * 1048576,
                'max_file_size_video' => inputPost('max_file_size_video') * 1048576,
                'max_file_size_audio' => inputPost('max_file_size_audio') * 1048576,
            ];
        }
        if (!empty($data)) {
            return $this->builderProductSettings->where('id', 1)->update($data);
        }
        return false;
    }

    /*
     * --------------------------------------------------------------------
     * Font Settings
     * --------------------------------------------------------------------
     */

    //get selected fonts
    public function getSelectedFonts($settings)
    {
        $arrayFonts = array();
        $fonts = $this->builderFonts->whereIn('id', [clrNum($settings->site_font), clrNum($settings->dashboard_font)], false)->get()->getResult();
        if (!empty($fonts)) {
            foreach ($fonts as $font) {
                if ($font->id == $settings->site_font) {
                    $arrayFonts['site_font'] = $font;
                }
                if ($font->id == $settings->dashboard_font) {
                    $arrayFonts['dashboard_font'] = $font;
                }
            }
        }
        return $arrayFonts;
    }

    //get fonts
    public function getFonts()
    {
        return $this->builderFonts->orderBy('font_name')->get()->getResult();
    }

    //get font
    public function getFont($id)
    {
        return $this->builderFonts->where('id', clrNum($id))->get()->getRow();
    }

    //add font
    public function addFont()
    {
        $data = [
            'font_name' => inputPost('font_name'),
            'font_url' => inputPost('font_url'),
            'font_family' => inputPost('font_family'),
            'is_default' => 0
        ];
        return $this->builderFonts->insert($data);
    }

    //set site font
    public function setSiteFont()
    {
        $langId = inputPost('lang_id');
        $data = [
            'site_font' => inputPost('site_font'),
            'dashboard_font' => inputPost('dashboard_font')
        ];
        return $this->builder->where('lang_id', clrNum($langId))->update($data);
    }

    //edit font
    public function editFont($id)
    {
        $font = $this->getFont($id);
        if (!empty($font)) {
            $data = array(
                'font_name' => inputPost('font_name'),
                'font_url' => inputPost('font_url'),
                'font_family' => inputPost('font_family')
            );
            return $this->builderFonts->where('id', clrNum($id))->update($data);
        }
        return false;
    }

    //delete font
    public function deleteFont($id)
    {
        $font = $this->getFont($id);
        if (!empty($font)) {
            return $this->builderFonts->where('id', $font->id)->delete();
        }
        return false;
    }

    //delete old sessions
    function deleteOldSessions()
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('ci_sessions')->where("timestamp < DATE_SUB('" . $now . "', INTERVAL 6 DAY)")->delete();
    }

    //set last cron update
    public function setLastCronUpdate()
    {
        $this->builderGeneral->where('id', 1)->update(['last_cron_update' => date('Y-m-d H:i:s')]);
    }

    //download database backup
    public function downloadBackup()
    {
        $prefs = array(
            'tables' => array(),
            'ignore' => array(),
            'filename' => '',
            'format' => 'gzip', // gzip, zip, txt
            'add_drop' => TRUE,
            'add_insert' => TRUE,
            'newline' => "\n",
            'foreign_key_checks' => TRUE
        );
        if (count($prefs['tables']) === 0) {
            $prefs['tables'] = $this->db->listTables();
        }
        // Extract the prefs for simplicity
        extract($prefs);
        $output = '';
        // Do we need to include a statement to disable foreign key checks?
        if ($foreign_key_checks === FALSE) {
            $output .= 'SET foreign_key_checks = 0;' . $newline;
        }
        foreach ((array)$tables as $table) {
            // Is the table in the "ignore" list?
            if (in_array($table, (array)$ignore, TRUE)) {
                continue;
            }
            // Get the table schema
            $query = $this->db->query('SHOW CREATE TABLE ' . $this->db->escapeIdentifiers($this->db->database . '.' . $table));
            // No result means the table name was invalid
            if ($query === FALSE) {
                continue;
            }
            // Write out the table schema
            $output .= '#' . $newline . '# TABLE STRUCTURE FOR: ' . $table . $newline . '#' . $newline . $newline;

            if ($add_drop === TRUE) {
                $output .= 'DROP TABLE IF EXISTS ' . $this->db->protectIdentifiers($table) . ';' . $newline . $newline;
            }
            $i = 0;
            $result = $query->getResultArray();
            foreach ($result[0] as $val) {
                if ($i++ % 2) {
                    $output .= $val . ';' . $newline . $newline;
                }
            }
            // If inserts are not needed we're done...
            if ($add_insert === FALSE) {
                continue;
            }
            // Grab all the data from the current table
            $query = $this->db->query('SELECT * FROM ' . $this->db->protectIdentifiers($table));

            if ($query->getFieldCount() === 0) {
                continue;
            }
            // Fetch the field names and determine if the field is an
            // integer type. We use this info to decide whether to
            // surround the data with quotes or not
            $i = 0;
            $field_str = '';
            $isInt = array();
            while ($field = $query->resultID->fetch_field()) {
                // Most versions of MySQL store timestamp as a string
                $isInt[$i] = in_array($field->type, array(MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_INT24, MYSQLI_TYPE_LONG), TRUE);

                // Create a string of field names
                $field_str .= $this->db->escapeIdentifiers($field->name) . ', ';
                $i++;
            }
            // Trim off the end comma
            $field_str = preg_replace('/, $/', '', $field_str);
            // Build the insert string
            foreach ($query->getResultArray() as $row) {
                $valStr = '';
                $i = 0;
                foreach ($row as $v) {
                    if ($v === NULL) {
                        $valStr .= 'NULL';
                    } else {
                        // Escape the data if it's not an integer
                        $valStr .= ($isInt[$i] === FALSE) ? $this->db->escape($v) : $v;
                    }
                    // Append a comma
                    $valStr .= ', ';
                    $i++;
                }
                // Remove the comma at the end of the string
                $valStr = preg_replace('/, $/', '', $valStr);
                // Build the INSERT string
                $output .= 'INSERT INTO ' . $this->db->protectIdentifiers($table) . ' (' . $field_str . ') VALUES (' . $valStr . ');' . $newline;
            }
            $output .= $newline . $newline;
        }
        // Do we need to include a statement to re-enable foreign key checks?
        if ($foreign_key_checks === FALSE) {
            $output .= 'SET foreign_key_checks = 1;' . $newline;
        }
        return $output;
    }
}