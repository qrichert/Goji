<?php

namespace App\Controller;

use App\Model\ContactForm;
use Goji\Blueprints\CachedControllerAbstract;
use Goji\Rendering\InPageContentEdit;
use Goji\Rendering\SimpleTemplate;
use Goji\Translation\Translator;

class ContactController extends CachedControllerAbstract {

	public function render(): void {

		// Translation
		$tr = new Translator($this->m_app);
			$tr->loadTranslationResource('%{LOCALE}.tr.xml');

		// Form
		$form = new ContactForm($tr);

		// In-Page Content Edit
		$inPageContentEdit = new InPageContentEdit($this->m_app, $this->m_app->getLanguages()->getCurrentCountryCode());

		// Template
		$template = new SimpleTemplate($tr->_('CONTACT_PAGE_TITLE') . ' - ' . $this->m_app->getSiteName(),
									   $tr->_('CONTACT_PAGE_DESCRIPTION'),
									   SimpleTemplate::ROBOTS_NOINDEX_NOFOLLOW);

		$template->startBuffer();

		// Getting the view (into buffer)
		require_once $template->getView('App/ContactView');

		// Now the view is accessible as string w/ $template->getPageContent()
		$template->saveBuffer();

		// Inside the template file we call $template to put things in place.
		require_once $template->getTemplate('page/main');
	}
}
