<?php

namespace App\Controllers;

use App\Models\FileModel;
use App\Models\SupportModel;

class SupportController extends BaseController
{
    protected $supportModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->supportModel = new SupportModel();
    }

    /**
     * Help Center
     */
    public function helpCenter()
    {
        $data['title'] = trans("help_center");
        $data['description'] = $this->baseVars->appName . ' - ' . trans("help_center");
        $data['keywords'] = $this->baseVars->appName . ',' . trans("help_center");
        $data['supportCategories'] = $this->supportModel->getCategoriesByLang(selectedLangId());
        
        echo view('partials/_header', $data);
        echo view('support/index', $data);
        echo view('partials/_footer');
    }

    /**
     * Category
     */
    public function category($slug)
    {
        $data['category'] = $this->supportModel->getCategoryBySlug($slug);
        if (empty($data['category'])) {
            return redirect()->to(generateUrl('help_center'));
        }
        $data['articles'] = $this->supportModel->getContentsByCategory($data['category']->id);
        if (empty($data['articles'])) {
            return redirect()->to(generateUrl('help_center'));
        }

        $data['article'] = $this->supportModel->getFirstContentByCategory($data['category']->id);
        $data['title'] = $data['category']->name . ' - ' . trans("help_center");
        $data['description'] = $this->baseVars->appName . ' - ' . $data['title'];
        $data['keywords'] = $this->baseVars->appName . ',' . trans("help_center");
        
        echo view('partials/_header', $data);
        echo view('support/content', $data);
        echo view('partials/_footer');
    }

    /**
     * Article
     */
    public function article($slugCategory, $slugArticle)
    {
        $data['category'] = $this->supportModel->getCategoryBySlug($slugCategory);
        if (empty($data['category'])) {
            return redirect()->to(generateUrl('help_center'));
        }
        $data['articles'] = $this->supportModel->getContentsByCategory($data['category']->id);
        $data['article'] = $this->supportModel->getContentBySlug($slugArticle);
        if (empty($data['article'])) {
            return redirect()->to(generateUrl('help_center'));
        }
        $data['title'] = $data['category']->name . ' - ' . trans("help_center");
        $data['description'] = $this->baseVars->appName . ' - ' . $data['title'];
        $data['keywords'] = $this->baseVars->appName . ',' . trans("help_center");
        
        echo view('partials/_header', $data);
        echo view('support/content', $data);
        echo view('partials/_footer');
    }

    /**
     * Search
     */
    public function search()
    {
        $q = inputGet('q');
        if (empty($q)) {
            return redirect()->to(generateUrl('help_center'));
        }
        $data['title'] = trans("search") . ' - ' . esc($q) . " - " . trans("help_center");
        $data['description'] = $this->baseVars->appName . " - " . $data['title'];
        $data['keywords'] = $this->baseVars->appName . "," . trans("help_center");
        $data['q'] = $q;
        
        $data['numRows'] = $this->supportModel->getContentSearchCount(selectedLangId(), $q);
        $pager = paginate($this->baseVars->perPage, $data['numRows']);
        $data['contents'] = $this->supportModel->getContentSearchResults(selectedLangId(), $q, $this->baseVars->perPage, $pager->offset);

        echo view('partials/_header', $data);
        echo view('support/search', $data);
        echo view('partials/_footer');
    }

    /**
     * Tickets
     */
    public function tickets()
    {
        if (!authCheck()) {
            return redirect()->to(generateUrl('help_center'));
        }
        $data['title'] = trans("support_tickets") . ' - ' . trans("help_center");
        $data['description'] = $this->baseVars->appName . ' - ' . $data['title'];
        $data['keywords'] = $this->baseVars->appName . ',' . trans("help_center");
        $data['numRows'] = $this->supportModel->getUserTicketsCount(user()->id);
        $pager = paginate($this->baseVars->perPage, $data['numRows']);
        $data['tickets'] = $this->supportModel->getUserTicketsPaginated(user()->id, $this->baseVars->perPage, $pager->offset);
        
        echo view('partials/_header', $data);
        echo view('support/tickets', $data);
        echo view('partials/_footer');
    }

    /**
     * Support
     */
    public function submitRequest()
    {
        $data['title'] = trans("submit_a_request") . ' - ' . trans("help_center");
        $data['description'] = $this->baseVars->appName . ' - ' . $data['title'];
        $data['keywords'] = $this->baseVars->appName . ',' . trans("help_center");
        $data['loadSupportEditor'] = true;
        
        echo view('partials/_header', $data);
        echo view('support/submit_request', $data);
        echo view('partials/_footer');
    }

    /**
     * Submit a Request Post
     */
    public function submitRequestPost()
    {
        $val = \Config\Services::validation();
        if (!authCheck()) {
            $val->setRule('name', trans("name"), 'required|max_length[255]');
            $val->setRule('email', trans("email"), 'required|max_length[255]');
        }
        $val->setRule('subject', trans("subject"), 'required|max_length[500]');
        $val->setRule('message', trans("message"), 'required|max_length[10000]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            if (reCAPTCHA('validate') == 'invalid') {
                setErrorMessage(trans("msg_recaptcha"));
                return redirect()->back()->withInput();
            } else {
                $isSupportReply = false;
                if ($this->supportModel->addTicket($isSupportReply)) {
                    $msg = trans("msg_message_sent") . '&nbsp;<a href="' . generateUrl('help_center', 'tickets') . '" style="color: #107ef4; border-bottom: 1px solid #107ef4;">' . trans('support_tickets') . '</a>';
                    if (!authCheck()) {
                        $msg = trans("msg_message_sent");
                    }
                    setSuccessMessage($msg);
                    return redirect()->to(generateUrl('help_center', 'submit_request'));
                } else {
                    setErrorMessage(trans("msg_error"));
                    return redirect()->back()->withInput();
                }
            }
        }
        return redirect()->to(generateUrl('help_center', 'submit_request'));
    }

    /**
     * Reply Ticket Post
     */
    public function replyTicketPost()
    {
        $ticketId = inputPost('ticket_id');
        $val = \Config\Services::validation();
        $val->setRule('message', trans("message"), 'required|max_length[10000]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $isSupportReply = false;
            if ($this->supportModel->addSubticket($ticketId, $isSupportReply)) {
                setSuccessMessage(trans("msg_message_sent"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
        return redirect()->to(generateUrl('help_center', 'ticket') . '/' . clrNum($ticketId));
    }

    /**
     * Ticket
     */
    public function ticket($id)
    {
        if (!authCheck()) {
            return redirect()->to(generateUrl('help_center'));
        }

        $data['title'] = trans("submit_a_request") . ' - ' . trans("help_center");
        $data['description'] = $this->baseVars->appName . " - " . $data['title'];
        $data['keywords'] = $this->baseVars->appName . "," . trans("help_center");
        $data['loadSupportEditor'] = true;
        $data['ticket'] = $this->supportModel->getTicket($id);
        $data['subtickets'] = $this->supportModel->getSubTickets($id);
        
        if (empty($data['ticket'])) {
            redirect(lang_base_url());
            exit();
        }
        if ($data['ticket']->user_id != user()->id) {
            return redirect()->to(generateUrl('help_center'));
        }

        echo view('partials/_header', $data);
        echo view('support/ticket', $data);
        echo view('partials/_footer');
    }

    /**
     * Close Ticket
     */
    public function closeTicketPost()
    {
        $id = inputPost('id');
        $this->supportModel->closeTicket($id);
    }

    /**
     * Upload Support Attachment
     */
    public function uploadSupportAttachment()
    {
        $ticketType = inputPost('ticket_type');
        $fileModel = new FileModel();
        $fileModel->uploadAttachment($ticketType);
        $this->printSupportAttachments($ticketType);
    }

    /**
     * Delete Support Attachment
     */
    public function deleteSupportAttachmentPost()
    {
        $id = inputPost('id');
        $ticketType = inputPost('ticket_type');
        $fileModel = new FileModel();
        $fileModel->deleteAttachment($id);
        $this->printSupportAttachments($ticketType);
    }

    /**
     * Download Support Attachment
     */
    public function downloadAttachmentPost()
    {
        $orjName = sanitize_filename(inputPost('orj_name'));
        $name = sanitize_filename(inputPost('name'));
        $storage = inputPost('storage');
        $path = '';
        if ($storage == 'aws_s3') {
            $path = getAWSBaseUrl() . 'uploads/support/' . $name;
        } else {
            $path = FCPATH . 'uploads/support/' . $name;
        }
        return downloadFile($path, $orjName);
    }

    //print attachments
    private function printSupportAttachments($ticketType)
    {
        $html = '';
        $ticketAttachments = helperGetSession('ticket_attachments');
        if (!empty($ticketAttachments)) {
            foreach ($ticketAttachments as $file) {
                if (!empty($file->fileId) && !empty($file->name) && !empty($file->ticketType) && $file->ticketType == $ticketType) {
                    $icon = '<i class="fa fa-times"></i>';
                    if ($file->ticketType == 'client') {
                        $icon = '<i class="icon-times"></i>';
                    }
                    $html .= '<div class="item"><div class="item-inner">';
                    $html .= esc($file->name) . '<a href="javascript:void(0)" onclick="deleteSupportAttachment(\'' . $file->fileId . '\')">' . $icon . '</a>';
                    $html .= '</div></div>';
                }
            }
        }
        $response = [
            'result' => 1,
            'response' => $html
        ];
        echo json_encode($response);
    }

}

