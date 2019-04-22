<?php

	namespace Goji\Core;

	/**
	 * Class App
	 *
	 * @package Goji\Core
	 */
	class App {

		/* <ATTRIBUTES> */

		private $m_siteUrl;
		private $m_siteName;
		private $m_siteDomainName;
		private $m_siteFullDomain;
		private $m_cookiesPrefix;

		private $m_isLocalEnvironment;
		private $m_appMode;

		/* <CONSTANTS> */

		const DEBUG = 'debug';
		const RELEASE = 'release';

		public function __construct() {

			$this->m_siteUrl = '';
			$this->m_siteName = '';
			$this->m_siteDomainName = '';
			$this->m_siteFullDomain = '';
			$this->m_cookiesPrefix = '';

			$this->m_isLocalEnvironment = false;
			$this->m_appMode = self::DEBUG;
		}

		/**
		 * @return string
		 */
		public function getSiteURL() {
			return $this->m_siteUrl;
		}

		/**
		 * @param string $siteUrl
		 */
		public function setSiteURL($siteUrl) {
			$this->m_siteUrl = $siteUrl;
		}

		/**
		 * @return string
		 */
		public function getSiteName() {
			return $this->m_siteName;
		}

		/**
		 * @param string $siteName
		 */
		public function setSiteName($siteName) {
			$this->m_siteName = $siteName;
		}

		/**
		 * domain.com
		 *
		 * @return string
		 */
		public function getSiteDomainName() {
			return $this->m_siteDomainName;
		}

		/**
		 * domain.com
		 *
		 * @param string $siteDomainName
		 */
		public function setSiteDomainName($siteDomainName) {
			$this->m_siteDomainName = $siteDomainName;
		}

		/**
		 * www.domain.com
		 *
		 * @return string
		 */
		public function getSiteFullDomain() {
			return $this->m_siteFullDomain;
		}

		/**
		 * www.domain.com
		 *
		 * @param string $siteFullDomain
		 */
		public function setSiteFullDomain($siteFullDomain) {
			$this->m_siteFullDomain = $siteFullDomain;
		}

		/**
		 * @return string
		 */
		public function getCookiesPrefix() {
			return $this->m_cookiesPrefix;
		}

		/**
		 * @param string $cookiesPrefix
		 */
		public function setCookiesPrefix($cookiesPrefix) {
			$this->m_cookiesPrefix = $cookiesPrefix;
		}

		/**
		 * @return bool
		 */
		public function getIsLocalEnvironment() {
			return $this->m_isLocalEnvironment;
		}

		/**
		 * @param bool $isLocalEnvironment
		 */
		public function setIsLocalEnvironment($isLocalEnvironment) {

			if (is_bool($isLocalEnvironment))
				$this->m_isLocalEnvironment = $isLocalEnvironment;
		}

		/**
		 * @return string
		 */
		public function getAppMode() {
			return $this->m_appMode;
		}

		/**
		 * @param \Goji\Core\App::APP_MODE $applicationMode
		 */
		public function setAppMode($appMode) {

			if ($appMode == self::DEBUG
				|| $appMode == self::RELEASE) {

				$this->m_appMode = $appMode;
			}
		}
	}
