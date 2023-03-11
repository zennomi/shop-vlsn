<?php namespace App\Models;

use CodeIgniter\Model;

class BlogModel extends BaseModel
{
    protected $builder;
    protected $builderCategories;
    protected $builderTags;
    protected $builderComments;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('blog_posts');
        $this->builderCategories = $this->db->table('blog_categories');
        $this->builderTags = $this->db->table('blog_tags');
        $this->builderComments = $this->db->table('blog_comments');
    }

    //input values
    public function inputValues()
    {
        return [
            'lang_id' => inputPost('lang_id'),
            'title' => inputPost('title'),
            'slug' => inputPost('slug'),
            'summary' => inputPost('summary'),
            'keywords' => inputPost('keywords'),
            'category_id' => inputPost('category_id'),
            'content' => inputPost('content'),
            'user_id' => user()->id
        ];
    }

    //add post
    public function addPost()
    {
        $data = $this->inputValues();
        $data['slug'] = generateSlug($data['slug'], $data['title']);
        $data['created_at'] = date('Y-m-d H:i:s');

        $blogImageId = inputPost('blog_image_id');
        $fileModel = new FileModel();
        $image = $fileModel->getBlogImage($blogImageId);
        if (!empty($image)) {
            $data['image_default'] = $image->image_path;
            $data['image_small'] = $image->image_path_thumb;
            $data['storage'] = $image->storage;
        }
        if ($this->builder->insert($data)) {
            $lastId = $this->db->insertID();
            $this->updateSlug($lastId);
            $this->addTags($lastId);
            return true;
        }
        return false;
    }

    //update post
    public function editPost($id)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $data = $this->inputValues();
            $data['slug'] = generateSlug($data['slug'], $data['title']);

            $blogImageId = inputPost('blog_image_id');
            $fileModel = new FileModel();
            $image = $fileModel->getBlogImage($blogImageId);
            if (!empty($image)) {
                $data['image_default'] = $image->image_path;
                $data['image_small'] = $image->image_path_thumb;
                $data['storage'] = $image->storage;
            }
            if ($this->builder->where('id', $post->id)->update($data)) {
                $this->updateSlug($post->id);
                $this->deleteTags($post->id);
                $this->addTags($post->id);
            }
            return true;
        }
        return false;
    }

    //update slug
    public function updateSlug($id)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            if (empty($post->slug) || $post->slug == '-') {
                $data = ['slug' => $post->id];
                $this->builder->where('id', $post->id)->update($data);
            } else {
                $numRows = $this->builder->where('slug', cleanStr($post->slug))->where('id !=', $post->id)->countAllResults();
                if ($numRows > 0) {
                    $data = ['slug' => $post->slug . '-' . $post->id];
                    $this->builder->where('id', $post->id)->update($data);
                }
            }
        }
    }

    //query string
    public function builQuery($langId = null)
    {
        if (empty($langId)) {
            $langId = selectedLangId();
        }
        $this->builder->resetQuery();
        $this->builder->select('blog_posts.*, blog_categories.name AS category_name, blog_categories.slug AS category_slug')->
        join('blog_categories', 'blog_posts.category_id = blog_categories.id')->where('blog_posts.lang_id', clrNum($langId));
    }

    //get post
    public function getPost($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get post joined
    public function getPostJoined($id)
    {
        $this->builQuery();
        return $this->builder->where('blog_posts.id', clrNum($id))->get()->getRow();
    }

    //get post by slug
    public function getPostBySlug($slug)
    {
        $this->builQuery();
        return $this->builder->where('blog_posts.slug', cleanStr($slug))->get()->getRow();
    }

    //get posts
    public function getPosts($limit)
    {
        $this->builQuery();
        return $this->builder->orderBy('created_at DESC')->get(clrNum($limit))->getResult();
    }

    //get post count
    public function getPostAdminCount()
    {
        $this->filterPosts();
        return $this->builder->countAllResults();
    }

    //get posts paginated
    public function getPostsAdminPaginated($perPage, $offset)
    {
        $this->filterPosts();
        return $this->builder->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter posts
    public function filterPosts()
    {
        $langId = clrNum(inputGet('lang_id'));
        $q = inputGet('q');
        if (!empty($langId)) {
            $this->builder->where('lang_id', clrNum($langId));
        }
        if (!empty($q)) {
            $this->builder->like('title', cleanStr($q));
        }
    }

    //get all posts count
    public function getAllPostsCount()
    {
        return $this->builder->countAllResults();
    }

    //get post count
    public function getPostCount()
    {
        $this->builQuery();
        return $this->builder->countAllResults();
    }

    //get paginated posts
    public function getPostsPaginated($perPage, $offset)
    {
        $this->builQuery();
        return $this->builder->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get post count by category
    public function getPostCountByCategory($categoryId)
    {
        return $this->builder->where('category_id', clrNum($categoryId))->countAllResults();
    }

    //get paginated category posts
    public function getCategoryPostsPaginated($categoryId, $perPage, $offset)
    {
        $this->builQuery();
        return $this->builder->where('category_id', clrNum($categoryId))->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get tag posts count
    public function getTagPostsCount($tagSlug)
    {
        $this->builQuery();
        return $this->builder->join('blog_tags', 'blog_posts.id = blog_tags.post_id ')->where('blog_tags.tag_slug', cleanStr($tagSlug))->countAllResults();
    }

    //get paginated tag posts
    public function getTagPostsPaginated($tagSlug, $perPage, $offset)
    {
        $this->builQuery();
        return $this->builder->join('blog_tags', 'blog_posts.id = blog_tags.post_id ')->where('blog_tags.tag_slug', cleanStr($tagSlug))->limit($perPage, $offset)->get()->getResult();
    }

    //get related posts
    public function getRelatedPosts($categoryId, $postId)
    {
        $this->builQuery();
        return $this->builder->where('blog_posts.category_id', clrNum($categoryId))->where('blog_posts.id != ', clrNum($postId))->orderBy('RAND()')->get(3)->getResult();
    }

    //get sitemap posts
    public function getSitemapPosts()
    {
        return $this->builder->join('blog_categories', 'blog_posts.category_id = blog_categories.id')->select('blog_posts.*, blog_categories.slug AS category_slug')->get()->getResult();
    }

    //delete post image
    public function deletePostImage($id)
    {
        $image = $this->getPost($id);
        if (!empty($image)) {
            $data['image_default'] = '';
            $data['image_small'] = '';
            $data['storage'] = '';
        }
        $this->builder->where('id', clrNum($id))->update($data);
    }

    //delete post
    public function deletePost($id)
    {
        $post = $this->getPost($id);
        if (!empty($post)) {
            $this->deleteTags($post->id);
            return $this->builder->where('id', $post->id)->delete();
        }
        return false;
    }

    /*
     * --------------------------------------------------------------------
     * Categories
     * --------------------------------------------------------------------
     */

    //input values
    public function inputValuesCategory()
    {
        return [
            'lang_id' => inputPost('lang_id'),
            'name' => inputPost('name'),
            'slug' => inputPost('slug'),
            'description' => inputPost('description'),
            'keywords' => inputPost('keywords'),
            'category_order' => inputPost('category_order')
        ];
    }

    //add category
    public function addCategory()
    {
        $data = $this->inputValuesCategory();
        $data['slug'] = generateSlug($data['slug'], $data['name']);
        if ($this->builderCategories->insert($data)) {
            $lastId = $this->db->insertID();
            $this->updateCategorySlug($lastId);
            return true;
        }
        return false;
    }

    //edit category
    public function editCategory($id)
    {
        $category = $this->getCategory($id);
        if (!empty($category)) {
            $data = $this->inputValuesCategory();
            $data['slug'] = generateSlug($data['slug'], $data['name']);
            if ($this->builderCategories->where('id', $category->id)->update($data)) {
                $this->updateCategorySlug($category->id);
                return true;
            }
            return false;
        }
    }

    //get category
    public function getCategory($id)
    {
        return $this->builderCategories->where('id', clrNum($id))->get()->getRow();
    }

    //get categories
    public function getCategories()
    {
        return $this->builderCategories->orderBy('category_order')->get()->getResult();
    }

    //get categories by lang
    public function getCategoriesByLang($langId)
    {
        return $this->builderCategories->where('lang_id', clrNum($langId))->orderBy('category_order')->get()->getResult();
    }

    //update category slug
    public function updateCategorySlug($id)
    {
        $category = $this->getCategory($id);
        if (!empty($category)) {
            if (empty($category->slug) || $category->slug == '-') {
                $data = ['slug' => $category->id];
                $this->builderCategories->where('id', $id)->update($data);
            } else {
                $numRows = $this->builderCategories->where('slug', strSlug($category->slug))->where('id !=', $category->id)->countAllResults();
                if ($numRows > 0) {
                    $data = ['slug' => $category->slug . '-' . $category->id];
                    $this->builderCategories->where('id', $id)->update($data);
                }
            }
        }
    }

    //get category by slug
    public function getCategoryBySlug($slug)
    {
        return $this->builderCategories->where('slug', $slug)->get()->getRow();
    }

    //delete category
    public function deleteCategory($id)
    {
        $category = $this->getCategory($id);
        if (!empty($category)) {
            return $this->builderCategories->where('id', $category->id)->delete();
        }
        return false;
    }

    /*
     * --------------------------------------------------------------------
     * Tags
     * --------------------------------------------------------------------
     */

    //add post tags
    public function addTags($postId)
    {
        $tags = inputPost('tags');
        $tagsArray = explode(",", $tags);
        if (!empty($tagsArray)) {
            foreach ($tagsArray as $tag) {
                $tag = trim($tag);
                if (!empty($tag) && strlen($tag) > 1) {
                    $data = [
                        'post_id' => $postId,
                        'tag' => $tag,
                        'tag_slug' => strSlug($tag)
                    ];
                    if (empty($data['tag_slug']) || $data['tag_slug'] == '-') {
                        $data['tag_slug'] = 'tag-' . uniqid();
                    }
                    $this->builderTags->insert($data);
                }
            }
        }
    }

    //get random post tags
    public function getRandomPostTags()
    {
        return $this->builderTags->join('blog_posts', 'blog_posts.id = blog_tags.post_id')->select('blog_tags.tag_slug, blog_tags.tag')->groupBy('tag_slug, blog_tags.tag')
            ->where('blog_posts.lang_id', selectedLangId())->orderBy('rand()')->get(10)->getResult();
    }

    //get tags
    public function getTags()
    {
        return $this->builderTags->join('blog_posts', 'blog_posts.id = blog_tags.post_id')->select('blog_tags.*, blog_posts.lang_id')->distinct('blog_tags.tag_slug')->get()->getResult();
    }

    //get post tag
    public function getPostTag($tagSlug)
    {
        return $this->builderTags->where('blog_tags.tag_slug', cleanStr($tagSlug))->get()->getRow();
    }

    //get posts tags
    public function getPostTags($postId)
    {
        return $this->builderTags->where('post_id', clrNum($postId))->get()->getResult();
    }

    //delete tags
    public function deleteTags($postId)
    {
        $tags = $this->getPostTags($postId);
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $this->builderTags->where('id', $tag->id)->delete();
            }
        }
    }

    /*
     * --------------------------------------------------------------------
     * Comments
     * --------------------------------------------------------------------
     */

    //add comment
    public function addComment()
    {
        $data = [
            'post_id' => inputPost('post_id'),
            'user_id' => 0,
            'name' => inputPost('name'),
            'email' => inputPost('email'),
            'comment' => inputPost('comment'),
            'status' => 0,
            'ip_address' => 0,
            'created_at' => date("Y-m-d H:i:s")
        ];
        if ($this->generalSettings->comment_approval_system != 1) {
            $data['status'] = 1;
        }
        if (authCheck()) {
            $data['user_id'] = user()->id;
            $data['name'] = getUsername(user());
            $data['email'] = user()->email;
        } else {
            if (empty($data['name']) || empty($data['email'])) {
                return false;
            }
        }
        if (empty($data['name'])) {
            $data['name'] = '';
        }
        if (empty($data['email'])) {
            $data['email'] = '';
        }
        $ip = getIPAddress();
        if (!empty($ip)) {
            $data['ip_address'] = $ip;
        }
        $data['post_id'] = clrNum($data['post_id']);
        if (!empty($data['post_id']) && !empty($data['comment'])) {
            $this->builderComments->insert($data);
        }
    }

    //pending comments
    public function getPendingComments()
    {
        return $this->builderComments->where('status', 0)->orderBy('created_at DESC')->get()->getResult();
    }

    //approved comments
    public function getApprovedComments()
    {
        return $this->builderComments->where('status', 1)->orderBy('created_at DESC')->get()->getResult();
    }

    //comments
    public function getCommentsByPostId($postId, $limit)
    {
        return $this->builderComments->where('post_id', clrNum($postId))->where('status', 1)->orderBy('created_at DESC')->get(clrNum($limit))->getResult();
    }

    //comment
    public function getComment($id)
    {
        return $this->builderComments->where('id', clrNum($id))->get()->getRow();
    }

    //post comment count
    public function getCommentCount($postId)
    {
        return $this->builderComments->where('post_id', clrNum($postId))->countAllResults();
    }

    //approve comment
    public function approveComment($id)
    {
        $comment = $this->getComment($id);
        if (!empty($comment)) {
            return $this->builderComments->where('id', $comment->id)->update(['status' => 1]);
        }
        return false;
    }

    //approve multi comments
    public function approveComments($commentIds)
    {
        if (!empty($commentIds)) {
            foreach ($commentIds as $id) {
                $this->approveComment($id);
            }
        }
    }

    //delete comment
    public function deleteComment($id)
    {
        $comment = $this->getComment($id);
        if (!empty($comment)) {
            return $this->builderComments->where('id', $comment->id)->delete();
        }
        return false;
    }

    //delete multi comments
    public function deleteComments($commentIds)
    {
        if (!empty($commentIds)) {
            foreach ($commentIds as $id) {
                $this->builderComments->where('id', clrNum($id))->delete();
            }
        }
    }
}