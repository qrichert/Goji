<?php

namespace Blog\Controller;

use Blog\Blueprint\BlogTrait;
use Blog\Model\BlogManager;
use Blog\Model\BlogPostManager;
use Blog\Model\BlogSearchForm;
use Goji\Core\App;
use Goji\Core\HttpResponse;
use Goji\Form\Form;
use Goji\Translation\Translator;

class XhrBlogSearchController extends BlogControllerAbstract {

	use BlogTrait;

	public function __construct(App $app) {

		parent::__construct($app);

		// Emulate XhrControllerAbstract
		HttpResponse::setRobotsHeader(HttpResponse::ROBOTS_NOINDEX);

		if (!$this->m_app->getRequestHandler()->isAjaxRequest())
			$this->m_app->getRouter()->redirectTo($this->m_app->getRouter()->getLinkForPage('home'));
	}

	private function treatForm(Form $form): void {

		$tr = $this->m_app->getTranslator();

		$detail = [];

		if (!$form->isValid($detail)) {

			HttpResponse::JSON([
				'detail' => $detail,
			], false);
		}

		$formQuery = $form->getInputByName('blog-search[query]')->getValue();

		$blogManager = new BlogManager($this->m_app);

		$categories = $blogManager->getCategories();
		$categoriesSelected = [];

		foreach ($categories as $category) {
			$categoryId = $category['id'];
			if ($form->getInputByName("blog-search[category][$categoryId]")->getValue())
				$categoriesSelected[] = $categoryId;
		}

		$blogPostManager = new BlogPostManager($this);
		$blogPosts = $blogPostManager->getBlogPostsForQuery(
													$formQuery,
													$categoriesSelected,
													0,
		                                            -1,
		                                            $this->m_app->getLanguages()->getCurrentCountryCode(),
		                                            [self::class, 'renderCleanAndCut']);
													// Can add [190] to limit at 190 chars for example, see renderCLeanAndCut function
													// /!\ Use same value as in BlogController !

		HttpResponse::JSON([
			'message' => $formQuery,
			'posts' => $blogPosts
		], true);
	}

	public function render(): void {

		$tr = new Translator($this->m_app);
		$tr->loadTranslationResource('%{LOCALE}.tr.xml');

		$form = new BlogSearchForm($this->m_app);
		$form->hydrate();

		$this->treatForm($form);
	}
}
