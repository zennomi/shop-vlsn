<?php namespace App\Models;

use CodeIgniter\Model;
use Config\Globals;

class SitemapModel extends BaseModel
{
    protected $urls;
    protected $langArray;
    protected $productSettings;

    public function __construct()
    {
        parent::__construct();
        $this->urls = array();
        $this->langArray = array();
        if (!empty(Globals::$languages)) {
            foreach (Globals::$languages as $lang) {
                $this->langArray[$lang->id] = $lang->short_form;
            }
        }
        $this->productSettings = Globals::$productSettings;
    }

    //update sitemap settings
    public function updateSitemapSettings()
    {
        $data = [
            'sitemap_frequency' => inputPost('frequency'),
            'sitemap_last_modification' => inputPost('last_modification'),
            'sitemap_priority' => inputPost('priority')
        ];
        $this->db->table('product_settings')->where('id', 1)->update($data);
        $this->productSettings = $this->db->table('product_settings')->get()->getRow();
    }

    //add static page urls
    public function addStaticURLs()
    {
        $this->addToMap(base_url(), 1);
    }

    //add page urls
    public function addPageURLs()
    {
        $model = new PageModel();
        $pages = $model->getSitemapPages();
        if (!empty($pages)) {
            foreach ($pages as $page) {
                if (empty($page->link)) {
                    $baseURL = $this->getBaseURL($page->lang_id);
                    if (!empty($baseURL)) {
                        $this->addToMap($baseURL . $page->slug, 0.8);
                    }
                }
            }
        }
    }

    //add category urls
    public function addCategoryURLs()
    {
        $model = new CategoryModel();
        $categories = $model->getSitemapCategories();
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $this->addToMap(generateCategoryUrl($category), 0.8);
            }
        }
    }

    //add product urls
    public function addProductURLs()
    {
        $model = new ProductModel();
        $products = $model->getSitemapProducts();
        if (!empty($products)) {
            foreach ($products as $product) {
                $this->addToMap(generateProductUrl($product), 0.8);
            }
        }
    }

    //add blog category urls
    public function addBlogCategoryURLs()
    {
        $model = new BlogModel();
        $categories = $model->getCategories();
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $baseURL = $this->getBaseURL($category->lang_id);
                $this->addToMap($baseURL . getRoute('blog') . '/' . $category->slug, 0.8);
            }
        }
    }

    //add blog post urls
    public function addBlogPostURLs()
    {
        $model = new BlogModel();
        $posts = $model->getSitemapPosts();
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $baseURL = $this->getBaseURL($post->lang_id);
                $this->addToMap($baseURL . getRoute('blog') . '/' . $post->category_slug . '/' . $post->slug, 0.8);
            }
        }
    }

    //add blog tag urls
    public function addBlogTagURLs()
    {
        $model = new BlogModel();
        $tags = $model->getTags();
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $baseURL = $this->getBaseURL($tag->lang_id);
                $this->addToMap($baseURL . getRoute('blog') . '/' . getRoute('tag') . '/' . $tag->tag_slug, 0.8);
            }
        }
    }

    //get base URL by language
    public function getBaseURL($langId)
    {
        if ($langId == $this->generalSettings->site_lang || empty($this->langArray[$langId])) {
            return base_url() . '/';
        }
        return base_url($this->langArray[$langId]) . '/';
    }

    //add sitemap item
    public function addToMap($loc, $priorityValue = NULL)
    {
        $item = new \stdClass();
        $item->loc = $loc;
        $item->lastMod = $this->productSettings->sitemap_last_modification;
        $item->lastModTime = NULL;
        $item->changeFreq = $this->productSettings->sitemap_frequency;
        $item->priority = $this->productSettings->sitemap_priority;
        $item->priorityValue = $priorityValue;
        $this->urls[] = $item;
        return true;
    }

    //generate sitemape
    public function generateSitemap()
    {
        $this->addStaticURLs();
        $this->addPageURLs();
        $this->addCategoryURLs();
        $this->addProductURLs();
        $this->addBlogCategoryURLs();
        $this->addBlogPostURLs();
        $this->addBlogTagURLs();
        if (countItems($this->urls) > 49000) {
            $arrayURLs = array_chunk($this->urls, 49000);
            $i = 0;
            if (!empty($arrayURLs)) {
                foreach ($arrayURLs as $arrayURL) {
                    $fullPath = FCPATH . 'sitemap.xml';
                    if ($i != 0) {
                        $fullPath = FCPATH . 'sitemap-' . $i . '.xml';
                    }
                    $this->exportSitemap($fullPath, $arrayURL);
                    $i++;
                }
            }
        } else {
            $fullPath = FCPATH . 'sitemap.xml';
            $this->exportSitemap($fullPath, $this->urls);
        }
    }

    //export sitemap
    public function exportSitemap($fullPath, $array)
    {
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        foreach ($array as $url) {
            $child = $xml->addChild('url');
            $urlLoc = '';
            if (!empty($url->loc)) {
                $urlLoc = htmlspecialchars(strtolower($url->loc));
            }
            $child->addChild('loc', $urlLoc);
            if (isset($url->lastMod) && $url->lastMod != 'none') {
                if ($url->lastMod == 'server_response') {
                    $child->addChild('lastmod', date('Y-m-d'));
                } else {
                    $child->addChild('lastmod', $url->lastModTime);
                }
            }
            if (isset($url->changeFreq) && $url->changeFreq != 'none') {
                $child->addChild('changefreq', $url->changeFreq);
            }
            if (isset($url->priority) && $url->priority != 'none') {
                $child->addChild('priority', $url->priorityValue);
            }
        }
        $xml->saveXML($fullPath);
    }
}
