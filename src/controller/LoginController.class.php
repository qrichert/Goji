<?php

	namespace App\Controller;

	use Goji\Core\App;
	use Goji\Core\HttpResponse;
	use Goji\Blueprints\ControllerInterface;
	use Goji\Translation\Translator;
	use Goji\Toolkit\SimpleMetrics;
	use Goji\Toolkit\SimpleTemplate;

	class LoginController implements ControllerInterface {

		/* <ATTRIBUTES> */

		private $m_app;

		public function __construct(App $app) {
			$this->m_app = $app;
		}

		private function loginRequest(array $login): void {

			if (!empty($login['email'])
			    && !empty($login['password'])) {

				$this->m_app->getUser()->logIn(1);

				HttpResponse::JSON(array(
					'email' => $login['email'],
					'redirect_to' => $this->m_app->getAuthentication()->getRedirectToOnLogInSuccess()
				), true); // email, redirect_to, add status = SUCCESS

			} else {

				HttpResponse::JSON(null, false); // No data, add status = ERROR
			}
		}

		public function render() {

			if (!empty($_POST['login']))
				$this->loginRequest($_POST['login']);

			SimpleMetrics::addPageView($this->m_app->getRouter()->getCurrentPage());

			$tr = new Translator($this->m_app);
			$tr->loadTranslationResource('%{LOCALE}.tr.xml');

			$template = new SimpleTemplate($tr->_('LOGIN_PAGE_TITLE'),
			                               $tr->_('LOGIN_PAGE_DESCRIPTION'),
										   SimpleTemplate::ROBOTS_NOINDEX_NOFOLLOW);

			$template->startBuffer();

			require_once '../src/view/login_v.php';

			$template->saveBuffer();

			require_once '../template/page/main_t.php';
		}
	}
