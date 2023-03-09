<?php namespace App\Models;

use CodeIgniter\Model;

class NewsletterModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('subscribers');
    }

    //add to subscriber
    public function addSubscriber($email)
    {
        $data = [
            'email' => $email,
            'token' => generateToken(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->builder->insert($data);
    }

    //update subscriber token
    public function updateSubscriberToken($email)
    {
        $this->builder->where('email', cleanStr($email))->update(['token' => generateToken()]);
    }

    //get subscribers
    public function getSubscribers()
    {
        return $this->builder->orderBy('id DESC')->get()->getResult();
    }

    //get subscriber
    public function getSubscriber($email)
    {
        return $this->builder->where('email', cleanStr($email))->get()->getRow();
    }

    //get subscriber by id
    public function getSubscriberById($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //delete from subscribers
    public function deleteFromSubscribers($id)
    {
        return $this->builder->where('id', clrNum($id))->delete();
    }

    //get subscriber by token
    public function getSubscriberByToken($token)
    {
        return $this->builder->where('token', cleanStr($token))->get()->getRow();
    }

    //unsubscribe email
    public function unSubscribeEmail($email)
    {
        $this->builder->where('email', cleanStr($email))->delete();
    }

    //update settings
    public function updateSettings()
    {
        $data = [
            'newsletter_status' => inputPost('newsletter_status'),
            'newsletter_popup' => inputPost('newsletter_popup')
        ];
        $uploadModel = new UploadModel();
        $file = $uploadModel->uploadTempFile('file');
        if (!empty($file) && !empty($file['path'])) {
            $data['newsletter_image'] = $uploadModel->uploadNewsletterImage($file['path']);
        }
        return $this->db->table('general_settings')->where('id', 1)->update($data);
    }

    //send email
    public function sendEmail()
    {
        $emailModel = new EmailModel();
        $email = inputPost('email');
        $subject = inputPost('subject');
        $body = inputPost('body');
        $emailType = inputPost('email_type');
        if ($emailModel->sendEmailNewsletter($email, $subject, $body, $emailType)) {
            return true;
        }
        return false;
    }
}