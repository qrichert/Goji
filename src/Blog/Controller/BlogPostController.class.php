<?php

namespace Blog\Controller;

use Blog\Blueprint\BlogTrait;
use Blog\Model\BlogPostManager;
use Goji\Core\App;
use Goji\Rendering\SimpleTemplate;
use Goji\Toolkit\SimpleCache;
use Goji\Toolkit\SwissKnife;
use Goji\Translation\Translator;

class BlogPostController extends BlogControllerAbstract {

	use BlogTrait;

	/* <ATTRIBUTES> */

	private $m_permalink;

	public function __construct(App $app) {

		parent::__construct($app);

		$this->m_permalink = $this->m_app->getRequestHandler()->getRequestParameters()[1] ?? null; // 0 = full match

		if (empty($this->m_permalink))
			$this->m_app->getRouter()->redirectTo($this->m_app->getRouter()->getLinkForPage('blog'));

		$this->m_cacheId = $this->m_cacheId . '-' . SimpleCache::cacheIDFromString(
			$this->m_permalink ?? ''
		);

		// Bad ID handled in BlogPostManager::read(); -> 404

		$this->activateCacheIfRolePermits();
	}

	public function render(): void {

		$tr = new Translator($this->m_app);
			$tr->loadTranslationResource('%{LOCALE}.tr.xml');

		$blogPostManager = new BlogPostManager($this);

		$blogPost = $blogPostManager->read($this->m_permalink, true);

		if ($blogPost['hidden']) {
			if (!$this->m_app->getUser()->isLoggedIn()
		        || !$this->m_app->getMemberManager()->memberIs('editor')) {

				$this->errorBlogPostDoesNotExist();
			}
		}

		$blogPost['post'] = self::renderAsHTML($blogPost['post']); // To HTML

		$blogPost['previous'] = $blogPostManager->getPreviousBlogPost($blogPost['creation_date']['full'], $this->m_app->getLanguages()->getCurrentCountryCode());

			if (!empty($blogPost['previous']['title']))
				$blogPost['previous']['title'] = SwissKnife::ceil_str($blogPost['previous']['title'], 70, '...'); // 60 max. anyway in SEO guidelines

		$blogPost['next'] = $blogPostManager->getNextBlogPost($blogPost['creation_date']['full'], $this->m_app->getLanguages()->getCurrentCountryCode());

			if (!empty($blogPost['next']['title']))
				$blogPost['next']['title'] = SwissKnife::ceil_str($blogPost['next']['title'], 70, '...'); // 60 max. anyway in SEO guidelines


		$template = new SimpleTemplate($blogPost['title'] . ' - ' . $this->m_app->getSiteName(),
		                               $blogPost['description']);

		$template->startBuffer();

		// Getting the view (into buffer)
		require_once $template->getView('Blog/BlogPostView');

		// Now the view is accessible as string w/ $template->getPageContent()
		$template->saveBuffer();

		// Inside the template file we call $template to put things in place.
		require_once $template->getTemplate('page/main');
	}
}
