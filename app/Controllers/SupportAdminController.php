<?php

namespace App\Controllers;

use App\Models\SupportModel;

class SupportAdminController extends BaseAdminController
{
    protected $supportModel;
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        checkPermission('help_center');
        $this->supportModel = new SupportModel();
    }

    /**
     * Knowledge Base
     */
    public function knowledgeBase()
    {
        $data['title'] = trans("knowledge_base");
        $langId = inputGet('lang');
        if (empty($langId) || empty(getLanguage($langId))) {
            return redirect()->to(adminUrl('knowledge-base?lang=' . $this->generalSettings->site_lang));
        }
        $data['contents'] = $this->supportModel->getContentsByLang($langId);
        $data['categories'] = $this->supportModel->getCategoriesByLang($langId);
        $data['langId'] = $langId;
        
        echo view('admin/includes/_header', $data);
        echo view('admin/support/knowledge_base', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Content
     */
    public function addContent()
    {
        $data['title'] = trans("add_content");
        $data['categories'] = $this->supportModel->getCategoriesByLang(inputGet('lang'));

        echo view('admin/includes/_header', $data);
        echo view('admin/support/add_content', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Content Post
     */
    public function addContentPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            if ($this->supportModel->addContent()) {
                setSuccessMessage(trans("msg_added"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        redirectToBackUrl();
    }

    /**
     * Edit Content
     */
    public function editContent($id)
    {
        $data['title'] = trans("edit_content");
        $data['content'] = $this->supportModel->getContent($id);
        if (empty($data['content'])) {
            return redirect()->to(adminUrl('knowledge-base'));
        }
        $data['categories'] = $this->supportModel->getCategoriesByLang($data['content']->lang_id);

        echo view('admin/includes/_header', $data);
        echo view('admin/support/edit_content', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Content Post
     */
    public function editContentPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $id = inputPost('id');
            if ($this->supportModel->editContent($id)) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        redirectToBackUrl();
    }

    /**
     * Delete Content Post
     */
    public function deleteContentPost()
    {
        $id = inputPost('id');
        if ($this->supportModel->deleteContent($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setSuccessMessage(trans("msg_error"));
        }
    }

    /**
     * Add Category
     */
    public function addCategory()
    {
        $data['title'] = trans("add_category");

        echo view('admin/includes/_header', $data);
        echo view('admin/support/add_category', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Category Post
     */
    public function addCategoryPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('name', trans("name"), 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            if ($this->supportModel->addCategory()) {
                setSuccessMessage(trans("msg_added"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        redirectToBackUrl();
    }

    /**
     * Edit Category
     */
    public function editCategory($id)
    {
        $data['title'] = trans("update_category");
        $data['category'] = $this->supportModel->getCategory($id);
        if (empty($data['category'])) {
            return redirect()->to(adminUrl('knowledge-base'));
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/support/edit_category', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Category Post
     */
    public function editCategoryPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('name', trans("name"), 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $id = inputPost('id');
            if ($this->supportModel->editCategory($id)) {
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        redirectToBackUrl();
    }

    /**
     * Delete Category Post
     */
    public function deleteCategoryPost()
    {
        $id = inputPost('id');
        if ($this->supportModel->deleteCategory($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Support Tickets
     */
    public function supportTickets()
    {
        $data['title'] = trans("support_tickets");
        $status = clrNum(inputGet('status'));
        if ($status != 1 && $status != 2 && $status != 3) {
            $status = 1;
        }
        $data['status'] = $status;
        $data['numRows'] = $this->supportModel->getTicketsCount($status);
        $data['numRowsOpen'] = $this->supportModel->getTicketsCount(1);
        $data['numRowsResponded'] = $this->supportModel->getTicketsCount(2);
        $data['numRowsClosed'] = $this->supportModel->getTicketsCount(3);
        $pager = paginate($this->perPage, $data['numRows']);
        $data['tickets'] = $this->supportModel->getTicketsPaginated($status, $this->perPage, $pager->offset);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/support/tickets', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Support Ticket
     */
    public function supportTicket($id)
    {
        $data['ticket'] = $this->supportModel->getTicket($id);
        if (empty($data['ticket'])) {
            return redirect()->to(adminUrl('support-tickets'));
        }
        $data['title'] = trans("ticket");
        $data['subTickets'] = $this->supportModel->getSubTickets($id);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/support/ticket', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Send Message Post
     */
    public function sendMessagePost()
    {
        $ticketId = inputPost('ticket_id');
        if ($this->supportModel->addSubTicket($ticketId, true)) {
            setSuccessMessage(trans("msg_message_sent"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        redirectToBackUrl();
    }

    /**
     * Change Ticket Status
     */
    public function changeTicketStatusPost()
    {
        $id = inputPost('id');
        $status = inputPost('status');
        $this->supportModel->changeTicketStatus($id, $status);
    }

    /**
     * Delete Ticket Post
     */
    public function deleteTicketPost()
    {
        $id = inputPost('id');
        if ($this->supportModel->deleteTicket($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        exit();
    }

    //get categories by language
    public function getCategoriesByLang()
    {
        $langId = inputPost('lang_id');
        if (!empty($langId)) {
            $categories = $this->supportModel->getCategoriesByLang($langId);
            if (!empty($categories)) {
                foreach ($categories as $item) {
                    echo '<option value="' . $item->id . '">' . esc($item->name) . '</option>';
                }
            }
        }
    }
}

