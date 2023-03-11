<?php namespace App\Models;

use CodeIgniter\Model;

class LanguageModel extends BaseModel
{
    protected $builder;
    protected $builderTranslations;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('languages');
        $this->builderTranslations = $this->db->table('language_translations');
    }

    //input values
    public function inputValues()
    {
        return [
            'name' => inputPost('name'),
            'short_form' => inputPost('short_form'),
            'language_code' => inputPost('language_code'),
            'language_order' => inputPost('language_order'),
            'text_direction' => inputPost('text_direction'),
            'text_editor_lang' => inputPost('text_editor_lang'),
            'status' => inputPost('status')
        ];
    }

    //add language
    public function addLanguage()
    {
        $data = $this->inputValues();
        $data['flag_path'] = '';
        $uploadModel = new UploadModel();
        $tempFile = $uploadModel->uploadTempFile('file');
        if (!empty($tempFile) && !empty($tempFile['path'])) {
            $data['flag_path'] = $uploadModel->uploadFlag($tempFile['path']);
            $uploadModel->deleteTempFile($tempFile['path']);
        }
        if ($this->builder->insert($data)) {
            $lastId = $this->db->insertID();
            $translations = $this->getLanguageTranslations(1);
            if (!empty($translations)) {
                foreach ($translations as $translation) {
                    $dataTranslation = [
                        'lang_id' => $lastId,
                        'label' => $translation->label,
                        'translation' => $translation->translation
                    ];
                    $this->builderTranslations->insert($dataTranslation);
                }
            }
            $this->addLanguageRows($lastId);
            return true;
        }
        return false;
    }

    //edit language
    public function editLanguage($id)
    {
        $language = $this->getLanguage($id);
        if (!empty($language)) {
            $data = $this->inputValues();
            $uploadModel = new UploadModel();
            $tempFile = $uploadModel->uploadTempFile('file');
            if (!empty($tempFile) && !empty($tempFile['path'])) {
                $data['flag_path'] = $uploadModel->uploadFlag($tempFile['path']);
                deleteFile($language->flag_path);
                $uploadModel->deleteTempFile($tempFile['path']);
            }
            return $this->builder->where('id', $language->id)->update($data);
        }
        return false;
    }

    //add language rows
    public function addLanguageRows($langId)
    {
        //add settings
        $settings = [
            'lang_id' => $langId,
            'site_font' => 19,
            'dashboard_font' => 22,
            'site_title' => 'Modesy',
            'homepage_title' => 'Index',
            'site_description ' => 'Modesy',
            'keywords' => 'modesy',
            'facebook_url' => '',
            'twitter_url' => '',
            'instagram_url' => '',
            'pinterest_url' => '',
            'linkedin_url' => '',
            'vk_url' => '',
            'whatsapp_url' => '',
            'telegram_url' => '',
            'youtube_url' => '',
            'about_footer' => '',
            'contact_text' => '',
            'contact_address' => '',
            'contact_email' => '',
            'contact_phone' => '',
            'copyright' => '',
            'cookies_warning' => 1,
            'cookies_warning_text' => 'This site uses cookies. By continuing to browse the site you are agreeing to our use of cookies.',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('settings')->insert($settings);

        //add pages
        $pageTerms = [
            'lang_id' => $langId,
            'title' => 'Terms & Conditions',
            'slug' => 'terms-conditions',
            'description' => 'Terms & Conditions Page',
            'keywords' => 'Terms, Conditions, Page',
            'page_content' => '',
            'page_order' => 1,
            'visibility' => 1,
            'title_active' => 1,
            'location' => 'information',
            'is_custom' => 0,
            'page_default_name' => 'terms_conditions',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('pages')->insert($pageTerms);

        $pageContact = [
            'lang_id' => $langId,
            'title' => 'Contact',
            'slug' => 'contact',
            'description' => 'Contact Page',
            'keywords' => 'Contact, Page',
            'page_content' => '',
            'page_order' => 1,
            'visibility' => 1,
            'title_active' => 1,
            'location' => 'top_menu',
            'is_custom' => 0,
            'page_default_name' => 'contact',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('pages')->insert($pageContact);

        $pageBlog = [
            'lang_id' => $langId,
            'title' => 'Blog',
            'slug' => 'blog',
            'description' => 'Blog Page',
            'keywords' => 'Blog, Page',
            'page_content' => '',
            'page_order' => 1,
            'visibility' => 1,
            'title_active' => 1,
            'location' => 'quick_links',
            'is_custom' => 0,
            'page_default_name' => 'blog',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('pages')->insert($pageBlog);

        $pageShops = [
            'lang_id' => $langId,
            'title' => 'Shops',
            'slug' => 'shops',
            'description' => 'Shops Page',
            'keywords' => 'Shops, Page',
            'page_content' => '',
            'page_order' => 1,
            'visibility' => 1,
            'title_active' => 1,
            'location' => 'quick_links',
            'is_custom' => 0,
            'page_default_name' => 'shops',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('pages')->insert($pageShops);
    }

    //get language
    public function getLanguage($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get languages
    public function getLanguages()
    {
        return $this->builder->orderBy('language_order')->get()->getResult();
    }

    //get language translations
    public function getLanguageTranslations($langId)
    {
        return $this->builderTranslations->where('lang_id', clrNum($langId))->get()->getResult();
    }

    //get paginated translations
    public function getTranslationsPaginated($langId, $perPage, $offset)
    {
        $this->filterTranslations();
        return $this->builderTranslations->where('lang_id', clrNum($langId))->orderBy('id')->limit($perPage, $offset)->get()->getResult();
    }

    //get translations count
    public function getTranslationCount($langId)
    {
        $this->filterTranslations();
        return $this->builderTranslations->where('lang_id', clrNum($langId))->countAllResults();
    }

    //filter translations
    public function filterTranslations()
    {
        $q = cleanStr(inputGet('q'));
        if (!empty($q)) {
            $this->builderTranslations->groupStart()->like('label', $q)->orLike('translation', $q)->groupEnd();
        }
    }

    //set default language
    public function setDefaultLanguage()
    {
        $data = ['site_lang' => inputPost('site_lang')];
        $lang = $this->getLanguage($data['site_lang']);
        if (!empty($lang)) {
            return $this->db->table('general_settings')->where('id', 1)->update($data);
        }
        return false;
    }

    //delete language
    public function deleteLanguage($id)
    {
        $language = $this->getLanguage($id);
        if (!empty($language)) {
            $this->builderTranslations->where('lang_id', $language->id)->delete();
            $this->db->table('settings')->where('lang_id', $language->id)->delete();
            $this->db->table('pages')->where('lang_id', $language->id)->delete();
            deleteFile($language->flag_path);
            return $this->builder->where('id', $language->id)->delete();
        }
        return false;
    }

    //edit translation
    public function editTranslations($langId, $id, $translation)
    {
        $data = ['translation' => $translation];
        return $this->builderTranslations->where('lang_id', clrNum($langId))->where('id', clrNum($id))->update($data);
    }

    //import language
    public function importLanguage()
    {
        $uploadModel = new UploadModel();
        $uploadedFile = $uploadModel->uploadTempFile('file');
        if (!empty($uploadedFile) && !empty($uploadedFile['path'])) {
            $json = file_get_contents($uploadedFile['path']);
            if (!empty($json)) {
                $count = countItems($this->getLanguages());
                $jsonArray = json_decode($json);
                $language = $jsonArray->language;
                //upload flag
                $flagPath = '';
                $uploadModel = new UploadModel();
                $flag = $uploadModel->uploadTempFile('flag');
                if (!empty($flag) && !empty($flag['path'])) {
                    $flagPath = $uploadModel->uploadFlag($flag['path']);
                }
                //add language
                if (isset($jsonArray->language)) {
                    $data = array(
                        'name' => isset($jsonArray->language->name) ? $jsonArray->language->name : 'language',
                        'short_form' => isset($jsonArray->language->short_form) ? $jsonArray->language->short_form : 'ln',
                        'language_code' => isset($jsonArray->language->language_code) ? $jsonArray->language->language_code : 'cd',
                        'text_direction' => isset($jsonArray->language->text_direction) ? $jsonArray->language->text_direction : 'ltr',
                        'text_editor_lang' => isset($jsonArray->language->text_editor_lang) ? $jsonArray->language->text_editor_lang : 'ln',
                        'status' => 1,
                        'language_order' => $count + 1,
                        'flag_path' => $flagPath
                    );
                    $this->builder->insert($data);
                    $insertId = $this->db->insertID();
                    $this->addLanguageRows($insertId);
                    if (isset($jsonArray->translations)) {
                        foreach ($jsonArray->translations as $translation) {
                            $dataTranslation = [
                                'lang_id' => $insertId,
                                'label' => $translation->label,
                                'translation' => $translation->translation
                            ];
                            $this->builderTranslations->insert($dataTranslation);
                        }
                    }
                }
            }
            @unlink($uploadedFile['path']);
            @unlink($flag['path']);
            return true;
        }
        return false;
    }

    //export language
    public function exportLanguage()
    {
        $langId = inputPost("lang_id");
        $language = $this->getLanguage($langId);
        if (!empty($language)) {
            $arrayLang = array();
            $objLang = new \stdClass();
            $objLang->name = $language->name;
            $objLang->short_form = $language->short_form;
            $objLang->language_code = $language->language_code;
            $objLang->text_direction = $language->text_direction;
            $objLang->text_editor_lang = $language->text_editor_lang;
            $arrayLang['language'] = $objLang;
            $arrayLang['translations'] = $this->builderTranslations->select('label,translation')->where('lang_id', clrNum($langId))->orderBy('id')->get()->getResult();
            return $arrayLang;
        }
        return null;
    }
}
