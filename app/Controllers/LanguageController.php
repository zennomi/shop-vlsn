<?php

namespace App\Controllers;

use App\Models\LanguageModel;

class LanguageController extends BaseAdminController
{
    protected $languageModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        checkPermission('general_settings');
        $this->languageModel = new LanguageModel();
    }

    /**
     * Language Settings
     */
    public function languageSettings()
    {
        $data["title"] = trans("language_settings");
        $data["languages"] = $this->languageModel->getLanguages();
        
        echo view('admin/includes/_header', $data);
        echo view('admin/language/languages', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Set Language Post
     */
    public function setDefaultLanguagePost()
    {
        if ($this->languageModel->setDefaultLanguage()) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Add Language Post
     */
    public function addLanguagePost()
    {
        $val = \Config\Services::validation();
        $val->setRule('name', trans("language_name"), 'required|max_length[200]');
        $val->setRule('short_form', trans("short_form"), 'required|max_length[100]');
        $val->setRule('language_code', trans("language_code"), 'required|max_length[100]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('language-settings'))->withInput();
        } else {
            if ($this->languageModel->addLanguage()) {
                setSuccessMessage(trans("msg_added"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        redirectToBackUrl();
    }

    /**
     * Edit Language
     */
    public function editLanguage($id)
    {
        $data['title'] = trans("update_language");
        $data['language'] = $this->languageModel->getLanguage($id);
        if (empty($data['language'])) {
            return redirect()->to(adminUrl('language-languages'));
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/language/edit_language', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Language Post
     */
    public function editLanguagePost()
    {
        $val = \Config\Services::validation();
        $val->setRule('name', trans("language_name"), 'required|max_length[200]');
        $val->setRule('short_form', trans("short_form"), 'required|max_length[100]');
        $val->setRule('language_code', trans("language_code"), 'required|max_length[100]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $id = inputPost('id');
            $language = getLanguage($id);
            if (!empty($language) && $language->id == $this->generalSettings->site_lang && inputPost('status') != 1) {
                $this->session->setFlashdata('error', trans("msg_error"));
                redirectToBackUrl();
            }
            if ($this->languageModel->editLanguage($id)) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        return redirect()->to(adminUrl('language-settings'));
    }

    /**
     * Delete Language Post
     */
    public function deleteLanguagePost()
    {
        $id = inputPost('id');
        $language = $this->languageModel->getLanguage($id);
        if ($language->id == 1) {
            setErrorMessage(trans("msg_default_language_delete"));
            exit();
        }
        if ($this->languageModel->deleteLanguage($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Edit Translations
     */
    public function editTranslations($id)
    {
        $data['title'] = trans('edit_translations');
        $data['language'] = $this->languageModel->getLanguage($id);
        if (empty($data['language'])) {
            return redirect()->to(adminUrl('language-settings'));
        }
        
        $numRows = $this->languageModel->getTranslationCount($data['language']->id);
        $pager = paginate($this->perPage, $numRows);
        $data['translations'] = $this->languageModel->getTranslationsPaginated($data['language']->id, $this->perPage, $pager->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/language/translations', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Translations Post
     */
    public function editTranslationsPost()
    {
        $langId = inputPost("lang_id");
        $ids = \Config\Services::request()->getPost();
        foreach ($ids as $key => $value) {
            if ($key != 'lang_id') {
                $this->languageModel->editTranslations($langId, $key, $value);
            }
        }
        setSuccessMessage(trans("msg_updated"));
        return redirect()->back();
    }

    /**
     * Import Language
     */
    public function importLanguagePost()
    {
        if ($this->languageModel->importLanguage()) {
            setSuccessMessage(trans("the_operation_completed"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Export Language
     */
    public function exportLanguagePost()
    {
        if (!is_writable(FCPATH . 'uploads/temp')) {
            setErrorMessage('"uploads/temp" folder is not writable!');
            redirectToBackUrl();
        }
        $files = glob(FCPATH . 'uploads/temp/*.json');
        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
        $arrayLang = $this->languageModel->exportLanguage();
        if (!empty($arrayLang)) {
            $filePath = FCPATH . 'uploads/temp/' . $arrayLang['language']->name . '.json';
            $json = json_encode($arrayLang);
            $file = fopen($filePath, 'w+');
            fwrite($file, $json);
            fclose($file);
            if (file_exists($filePath)) {
                return \Config\Services::response()->download($filePath, null);
            }
        }
        redirectToBackUrl();
    }
}