<?php

namespace App\Controllers;

class RssController extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        if ($this->generalSettings->rss_system != 1) {
            redirectToUrl(langBaseUrl());
        }
        helper('xml');
    } 

	/**
	 * Rss Page
	 */
	public function rssFeeds()
	{
		$data['title'] = trans("rss_feeds");
		$data['description'] = trans("rss_feeds") . ' - ' . $this->baseVars->appName;
		$data['keywords'] = trans("rss_feeds") . ',' . $this->baseVars->appName;

		echo view('partials/_header', $data);
		echo view('rss/rss_feeds', $data);
		echo view('partials/_footer');
	}

	/**
	 * Rss Latest Products
	 */
	public function latestProducts()
	{
		$data['feedName'] = $this->baseVars->appName . ' ' . trans("rss_feeds") . ' - ' . trans("latest_products");
		$data['encoding'] = 'utf-8';
		$data['feedUrl'] = langBaseUrl() . '/rss/' . getRoute("latest_products");
		$data['pageDescription'] = $this->baseVars->appName . ' ' . trans("rss_feeds") . ' - ' . trans("latest_products");
		$data['pageLanguage'] = $this->activeLang->short_form;
		$data['creatorEmail'] = '';
		$data['products'] = $this->productModel->getProducts(30);

		header("Content-Type: application/rss+xml; charset=utf-8");
		echo view('rss/rss', $data);
	}

	/**
	 * Rss Featured Products
	 */
	public function featuredProducts()
	{
		$data['feedName'] = $this->baseVars->appName . ' ' . trans("rss_feeds") . ' - ' . trans("featured_products");
		$data['encoding'] = 'utf-8';
		$data['feedUrl'] = langBaseUrl() . 'rss/' . getRoute("featured_products");
		$data['pageDescription'] = $this->baseVars->appName . ' ' . trans("rss_feeds") . ' - ' . trans("featured_products");
		$data['pageLanguage'] = $this->activeLang->short_form;
		$data['creatorEmail'] = '';
		$data['products'] = $this->productModel->getPromotedProducts();

		header("Content-Type: application/rss+xml; charset=utf-8");
		echo view('rss/rss', $data);
	}

	/**
	 * Rss By Category
	 */
	public function rssByCategory($slug)
	{
		$category = $this->categoryModel->getCategoryBySlug($slug);
		if (empty($category)) {
            return redirect()->to(generateUrl('rss_feeds'));
		}
		$data['products'] = $this->productModel->getRssProductsByCategory($category->id);
		$data['feedName'] = $this->baseVars->appName . ' ' . trans("rss_feeds") . ' - ' . $category->name;
		$data['encoding'] = 'utf-8';
		$data['feedUrl'] = langBaseUrl() . '/rss/' . getRoute("category", true) . $slug;
		$data['pageDescription'] = $this->baseVars->appName . ' ' . trans("rss_feeds") . ' - ' . $category->name;
		$data['pageLanguage'] = $this->activeLang->short_form;
		$data['creatorEmail'] = '';

		header("Content-Type: application/rss+xml; charset=utf-8");
		echo view('rss/rss', $data);
	}

	/**
	 * Rss By Seller
	 */
	public function rssBySeller($slug)
	{
		$user = $this->authModel->getUserBySlug($slug);
		if (empty($user)) {
            return redirect()->to(generateUrl('rss_feeds'));
		}
		if ($user->show_rss_feeds != 1) {
            return redirect()->to(generateProfileUrl($slug));
		}
		$data['products'] = $this->productModel->getRssProductsByUser($user->id);
		$data['feedName'] = $this->baseVars->appName . ' ' . trans("rss_feeds") . ' - ' . getUsername($user);
		$data['encoding'] = 'utf-8';
		$data['feedUrl'] = langBaseUrl() . '/rss/' . getRoute("seller", true) . $slug;
		$data['pageDescription'] = $this->baseVars->appName . ' ' . trans("rss_feeds") . ' - ' . getUsername($user);
		$data['pageLanguage'] = $this->activeLang->short_form;
		$data['creatorEmail'] = '';

		header("Content-Type: application/rss+xml; charset=utf-8");
		echo view('rss/rss', $data);
	}
}
