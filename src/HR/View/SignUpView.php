<main class="centered">
	<h1 class="hidden" aria-hidden="true"><?= $tr->_('SIGN_UP_MAIN_TITLE'); ?></h1>

	<section class="centered no-padding h">

		<img src="<?= $template->rsc('img/goji__berries.svg'); ?>" alt="" class="form__logo" aria-hidden="true">

		<?php $form->render(); ?>

		<div>
			<p>
				<?= $tr->_('SIGN_UP_ALREADY_ACCOUNT'); ?>
				<a href="<?= $this->m_app->getRouter()->getLinkForPage('login'); ?>">
					<?= $tr->_('SIGN_UP_LOG_IN'); ?>
				</a>
			</p>
		</div>

	</section>
</main>

<?php
$template->linkFiles([
	'js/lib/Goji/Form.class.min.js'
]);
?>
<script>
	(function() {

		let form = document.querySelector('#sign-up__form');
		let formSuccess = form.querySelector('p.form__success');
		let formError = form.querySelector('p.form__error');

		let success = response => {

			form.reset();
			formError.textContent = '';

			if (response !== null
				&& typeof response.redirect_to !== 'undefined'
				&& response.redirect_to !== null
				&& response.redirect_to !== '') {

				location.href = response.redirect_to;

			} else if (response !== null
			           && typeof response.message !== 'undefined'
			           && response.message !== null) {

				formSuccess.innerHTML = response.message;
			}
		};

		let error = response => {

			formSuccess.textContent = '';

			if (response !== null
			    && typeof response.message !== 'undefined'
			    && response.message !== null) {

				formError.innerHTML = response.message;
			}
		};

		new Form(form,
				 success,
				 error,
				 form.querySelector('button.loader'),
				 form.querySelector('.progress-bar')
		);

	})();
</script>
