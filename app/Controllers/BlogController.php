<?php

namespace App\Controllers;

use App\Models\BlogModel;

class BlogController extends BaseAdminController
{
    protected $blogModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->blogModel = new BlogModel();
    }

    /**
     * Posts
     */
    public function posts()
    {
        checkPermission('blog');
        $data['title'] = trans("blog_posts");
        
        $numRows = $this->blogModel->getPostAdminCount();
        $pager = paginate($this->perPage, $numRows);
        $data['posts'] = $this->blogModel->getPostsAdminPaginated($this->perPage, $pager->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/blog/posts', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Post
     */
    public function addPost()
    {
        checkPermission('blog');
        $data['title'] = trans("add_post");
        $data['categories'] = $this->blogModel->getCategoriesByLang(selectedLangId());
        
        echo view('admin/includes/_header', $data);
        echo view('admin/blog/add_post', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Post Post
     */
    public function addPostPost()
    {
        checkPermission('blog');
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        $val->setRule('category_id', trans("category"), 'required');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            if ($this->blogModel->addPost()) {
                setSuccessMessage(trans("msg_added"));
                return redirect()->back();
            }
        }
        setErrorMessage(trans("msg_error"));
        return redirect()->back()->withInput();
    }

    /**
     * Edit Post
     */
    public function editPost($id)
    {
        checkPermission('blog');
        $data['title'] = trans("update_post");
        $data['post'] = $this->blogModel->getPost($id);
        if (empty($data['post'])) {
            redirectToUrl(adminUrl('blog-posts'));
        }
        //combine post tags
        $data['tags'] = '';
        $count = 0;
        $tagsArray = $this->blogModel->getPostTags($id);
        foreach ($tagsArray as $item) {
            if ($count > 0) {
                $data['tags'] .= ',';
            }
            $data['tags'] .= $item->tag;
            $count++;
        }
        $data['categories'] = $this->blogModel->getCategoriesByLang($data['post']->lang_id);

        echo view('admin/includes/_header', $data);
        echo view('admin/blog/edit_post', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Post Post
     */
    public function editPostPost()
    {
        checkPermission('blog');
        $val = \Config\Services::validation();
        $val->setRule('title', trans("title"), 'required|max_length[500]');
        $val->setRule('category_id', trans("category"), 'required');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $id = inputPost('id');
            if ($this->blogModel->editPost($id)) {
                setSuccessMessage(trans("msg_updated"));
                redirectToBackUrl();
            }
        }
        setErrorMessage(trans("msg_error"));
        return redirect()->back()->withInput();
    }

    /**
     * Delete Post Image Post
     */
    public function deletePostImagePost()
    {
        checkPermission('blog');
        $id = inputPost('post_id');
        $this->blogModel->deletePostImage($id);
    }

    /**
     * Delete Post
     */
    public function deletePostPost()
    {
        checkPermission('blog');
        $id = inputPost('id');
        if ($this->blogModel->deletePost($id)) {
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /*
     * --------------------------------------------------------------------
     * Category
     * --------------------------------------------------------------------
     */

    /**
     * Categories
     */
    public function categories()
    {
        checkPermission('blog');
        $data['title'] = trans("categories");
        $data['categories'] = $this->blogModel->getCategories();
        $data['langSearchColumn'] = 2;
        
        echo view('admin/includes/_header', $data);
        echo view('admin/blog/categories', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add Category Post
     */
    public function addCategoryPost()
    {
        checkPermission('blog');
        $val = \Config\Services::validation();
        $val->setRule('name', trans("category_name"), 'required|max_length[200]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            if ($this->blogModel->addCategory()) {
                setSuccessMessage(trans("msg_added"));
                return redirect()->back();
            }
        }
        setErrorMessage(trans("msg_error"));
        return redirect()->back()->withInput();
    }

    /**
     * Edit Category
     */
    public function editCategory($id)
    {
        checkPermission('blog');
        $data['title'] = trans("update_post");
        $data['category'] = $this->blogModel->getCategory($id);
        if (empty($data['category'])) {
            redirectToUrl(adminUrl('blog-categories'));
        }

        echo view('admin/includes/_header', $data);
        echo view('admin/blog/edit_category', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit Category Post
     */
    public function editCategoryPost()
    {
        checkPermission('blog');
        $val = \Config\Services::validation();
        $val->setRule('name', trans("category_name"), 'required|max_length[200]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $id = inputPost('id');
            if ($this->blogModel->editCategory($id)) {
                setSuccessMessage(trans("msg_updated"));
                redirectToUrl(adminUrl('blog-categories'));
            }
        }
        setErrorMessage(trans("msg_error"));
        return redirect()->back()->withInput();
    }

    /**
     * Delete Category Post
     */
    public function deleteCategoryPost()
    {
        checkPermission('blog');
        $id = inputPost('id');
        if (!empty($this->blogModel->getPostCountByCategory($id))) {
            setErrorMessage(trans("msg_delete_posts"));
        } else {
            if ($this->blogModel->deleteCategory($id)) {
                setSuccessMessage(trans("msg_deleted"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
        }
    }

    /*
     * --------------------------------------------------------------------
     * Comments
     * --------------------------------------------------------------------
     */

    /**
     * Comments
     */
    public function comments()
    {
        checkPermission('comments');
        $data['title'] = trans("approved_comments");
        $data['comments'] = $this->blogModel->getApprovedComments();
        $data['topButtonText'] = trans("pending_comments");
        $data['topButtonURL'] = adminUrl('pending-blog-comments');
        $data['showApproveButton'] = false;
        
        echo view('admin/includes/_header', $data);
        echo view('admin/blog/comments', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Pending Comments
     */
    public function pendingComments()
    {
        checkPermission('comments');
        $data['title'] = trans("pending_comments");
        $data['comments'] = $this->blogModel->getPendingComments();
        $data['topButtonText'] = trans("approved_comments");
        $data['topButtonURL'] = adminUrl('blog-comments');
        $data['showApproveButton'] = true;

        echo view('admin/includes/_header', $data);
        echo view('admin/blog/comments', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Aprrove Comment Post
     */
    public function approveCommentPost()
    {
        checkPermission('comments');
        $id = inputPost('id');
        if ($this->blogModel->approveComment($id)) {
            setSuccessMessage(trans("msg_comment_approved"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        return redirect()->back();
    }

    /**
     * Approve Selected Comments
     */
    public function approveSelectedComments()
    {
        checkPermission('comments');
        $commentIds = inputPost('comment_ids');
        $this->blogModel->approveComments($commentIds);
    }

    /**
     * Delete Comment
     */
    public function deleteComment()
    {
        checkPermission('comments');
        $id = inputPost('id');
        if ($this->blogModel->deleteComment($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Delete Selected Comments
     */
    public function deleteSelectedComments()
    {
        checkPermission('comments');
        $commentIds = inputPost('comment_ids');
        $this->blogModel->deleteComments($commentIds);
    }
}
