<?php

namespace Admin\Controller;

use Admin\Model\AnalyticsForm;
use Goji\Blueprints\ControllerAbstract;
use Goji\Rendering\SimpleTemplate;
use Goji\Translation\Translator;

class AnalyticsController extends ControllerAbstract {

	public function render(): void {

		$tr = new Translator($this->m_app);
			$tr->loadTranslationResource('%{LOCALE}.tr.xml');

		$analyticsForm = new AnalyticsForm($tr, $this->m_app);

		$template = new SimpleTemplate($tr->_('ANALYTICS_PAGE_TITLE') . ' - ' . $this->m_app->getSiteName(),
		                               $tr->_('ANALYTICS_PAGE_DESCRIPTION'),
		                               SimpleTemplate::ROBOTS_NOINDEX_NOFOLLOW);
			$template->addSpecial('is-focus-page', true);

		$template->startBuffer();

		require_once $template->getView('Admin/AnalyticsView');

		$template->saveBuffer();

		require_once $template->getTemplate('page/main');
	}
}
