<?php

	namespace App\Model;

	use Goji\Form\Form;
	use Goji\Form\InputButtonElement;
	use Goji\Form\InputCustom;
	use Goji\Form\InputLabel;
	use Goji\Form\InputTextEmail;
	use Goji\Form\InputTextPassword;
	use Goji\Translation\Translator;

	class LoginForm extends Form {

		function __construct(Translator $tr) {

			parent::__construct();

			$sanitizeEmail = function($email) {
				$email = mb_strtolower($email);
				return filter_var($email, FILTER_SANITIZE_EMAIL);
			};

			$this->setAttribute('class', 'form__login');

				$this->addInput(new InputLabel())
					 ->setAttribute('for', 'login__email')
					 ->setAttribute('textContent', $tr->_('LOGIN_FORM_EMAIL'));
				$this->addInput(new InputTextEmail(null, false, $sanitizeEmail))
					 ->setAttribute('name', 'login[email]')
					 ->setAttribute('id', 'login__email')
					 ->setAttribute('placeholder', $tr->_('LOGIN_FORM_EMAIL_PLACEHOLDER'))
					 ->setAttribute('required');
				$this->addInput(new InputLabel())
					 ->setAttribute('for', 'login__password')
					 ->setAttribute('textContent', $tr->_('LOGIN_FORM_PASSWORD'))
					 ->setSideInfo('a', ['href' => '#'], $tr->_('LOGIN_FORGOT_PASSWORD'));
				$this->addInput(new InputTextPassword())
					 ->setAttribute('name', 'login[password]')
					 ->setAttribute('id', 'login__password')
					 ->setAttribute('placeholder', $tr->_('LOGIN_FUN_MESSAGE', mt_rand(1, 3)))
					 ->setAttribute('required');
				$this->addInput(new InputCustom('<div class="progress-bar"><div class="progress"></div></div>'));
				$this->addInput(new InputButtonElement())
					 ->setAttribute('class', 'highlight loader')
					 ->setAttribute('textContent', $tr->_('LOGIN_FORM_LOG_IN_BUTTON'));
				$this->addInput(new InputCustom('<p class="form__error"></p>'));
		}
	}