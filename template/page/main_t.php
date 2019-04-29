<!DOCTYPE html>
<html lang="<?= $this->m_app->getLanguages()->getCurrentHyphenLocale(); ?>">
	<head>
		<!-- TODO: Add base tag so we can use urls with slashes /. Use App->getRequestHandler()->getRootFolder();
			Does this solve CSS relative path problem w/ SimpleMinifierCSS() ??
			better not because the links are prepended by /goji/public, and it's good with a link to / (would be empty)
			OR instead of prepending root folder, we could just prepend a slash if empty lol
			<base href="/goji/public/">
		-->
		<!-- Analytics -->
		<?php
			if ($this->m_app->getAppMode() !== \Goji\Core\App::DEBUG)
				require_once '../template/page/include/tracking_v.inc.php';
		?>

		<?php require_once '../template/page/include/head_v.inc.php'; ?>

		<!-- SEO -->
		<title><?= $template->getPageTitle(); ?></title>
		<meta name="description" content="<?= $template->getPageDescription(); ?>">
		<?= $template->getRobotsBehaviour(); ?>
		<link rel="canonical" href="<?= $this->m_app->getRouter()->getLinkForPage(null, null, true); ?>">
		<?php

			foreach ($this->m_app->getLanguages()->getSupportedLocales() as $locale) {

				echo '<link rel="alternate" hreflang="'
				     . $this->m_app->getLanguages()->hyphenateLocale($locale)
				     . '" href="' . $this->m_app->getRouter()->getLinkForPage(null, $locale, true)
				     . '">' . PHP_EOL;
			}

			echo '<link rel="alternate" hreflang="x-default" href="'
			     . $this->m_app->getRouter()->getLinkForPage(null, $this->m_app->getLanguages()->getFallbackLocale(), true)
			     . '">';
		?>

		<!-- Style -->
		<?php

			\Goji\Toolkit\SwissKnife::linkFiles('css', array(
				'css/main.css',
				'css/responsive.css'
			));

		?>

		<!-- Social -->
		<meta property="og:title" content="<?= $template->getPageTitle(); ?>">
		<meta property="og:description" content="<?= $template->getPageDescription(); ?>">
		<?php require_once '../template/page/include/opengraph_v.inc.php'; ?>

		<!-- Scripts -->
		<?php require_once '../template/page/include/head-javascript_v.inc.php'; ?>
	</head>
	<body id="<?= $this->m_app->getRouter()->getCurrentPage(); ?>">
		<?php require_once '../template/page/include/body_v.inc.php'; ?>
		<?php require_once '../template/page/include/header_v.inc.php'; ?>

		<?= $template->getPageContent(); ?>

		<?php require_once '../template/page/include/footer_v.inc.php'; ?>

		<?php require_once '../template/page/include/bottom-javascript_v.inc.php'; ?>
	</body>
</html>
