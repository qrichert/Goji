<?php

	namespace App\Controller;

	use App\Model\LoginForm;
	use Goji\Blueprints\HttpMethodInterface;
	use Goji\Core\App;
	use Goji\Core\HttpResponse;
	use Goji\Blueprints\ControllerAbstract;
	use Goji\Form\Form;
	use Goji\Translation\Translator;
	use Goji\Toolkit\SimpleMetrics;
	use Goji\Toolkit\SimpleTemplate;

	class LoginController extends ControllerAbstract {

		/* <ATTRIBUTES> */

		private $m_app;

		public function __construct(App $app) {
			$this->m_app = $app;
		}

		private function treatForm(Translator $tr, Form &$form): bool {

			$detail = [];
			$isValid = $form->isValid($detail);

			if ($isValid) { // Form is valid, in the sense that required info is there, email is an email, etc.

				// Verify validity here

				$this->m_app->getUser()->logIn(1);

				// If AJAX, return JSON (SUCCESS)
				if ($this->m_app->getRequestHandler()->isAjaxRequest()) {

					HttpResponse::JSON([
						'email' => $form->getInputByName('login[email]')->getValue(),
						'redirect_to' => $this->m_app->getAuthentication()->getRedirectToOnLogInSuccess()
					], true); // email, redirect_to, add status = SUCCESS

				} else {
					// Clean the form
					$form = new LoginForm($tr);
				}

				return true;
			}

			// If AJAX, return JSON (ERROR)
			if ($this->m_app->getRequestHandler()->isAjaxRequest()) {

				HttpResponse::JSON([
					'detail' => $detail,
					'message' => $tr->_('LOGIN_WRONG_USERNAME_OR_PASSWORD')
				], false);
			}

			return false;
		}

		public function render() {

			SimpleMetrics::addPageView($this->m_app->getRouter()->getCurrentPage());

			$tr = new Translator($this->m_app);
				$tr->loadTranslationResource('%{LOCALE}.tr.xml');

			$form = new LoginForm($tr);

			$formSentSuccess = null;

			if ($this->m_app->getRequestHandler()->getRequestMethod() == HttpMethodInterface::HTTP_POST) {

				$form->hydrate();
				$formSentSuccess = $this->treatForm($tr, $form);
			}

			$template = new SimpleTemplate($tr->_('LOGIN_PAGE_TITLE'),
			                               $tr->_('LOGIN_PAGE_DESCRIPTION'),
										   SimpleTemplate::ROBOTS_NOINDEX_NOFOLLOW);

			$template->startBuffer();

			require_once $template->getView('LoginView');

			$template->saveBuffer();

			require_once $template->getTemplate('page/main');
		}
	}
