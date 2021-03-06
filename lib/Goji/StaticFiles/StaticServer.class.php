<?php

namespace Goji\StaticFiles;

use Exception;
use Goji\Core\ConfigurationLoader;

/**
 * Class StaticServer
 *
 * @package Goji\StaticFiles
 */
class StaticServer {

	/* <ATTRIBUTES> */

	private $m_allowPlaceholderImages;
	private $m_linkedFilesMode;
	private $m_requestType;
	private $m_requestFileURI;
	private $m_fileType;
	private $m_files;

	/* <CONSTANTS> */

	const CONFIG_FILE = ROOT_PATH . '/config/templating.json5';

	const REQUEST_RESOURCE = 'request-resource'; // CSS, JS
	const REQUEST_IMAGE_PLACEHOLDER = 'request-image-placeholder';

	const IMAGE_PLACEHOLDER_FILE = 'img/placeholder';

	const NORMAL = 'normal';
	const MERGED = 'merged';

	const CSS = 'css';
	const JAVASCRIPT = 'js';
	const IMAGE_PLACEHOLDER = 'image-placeholder';

	const SUPPORTED_FILE_TYPES = [
		self::CSS, self::JAVASCRIPT,
		self::IMAGE_PLACEHOLDER
	];

	const E_REQUEST_IS_EMPTY = 0;
	const E_FILE_NOT_FOUND = 1;
	const E_FILE_TYPE_NOT_SUPPORTED = 2;

	public function __construct(string $configFile = self::CONFIG_FILE) {

		// Config
		try {

			$config = ConfigurationLoader::loadFileToArray($configFile);

			$this->m_allowPlaceholderImages = !empty($config['allow_placeholder_images'])
			                                  && $config['allow_placeholder_images'] === true;

			if (!empty($config['merge_linked_files'])
			    && $config['merge_linked_files'] === true)
					$this->m_linkedFilesMode = self::MERGED;
			else
				$this->m_linkedFilesMode = self::NORMAL;

		} catch (Exception $e) {

			$this->m_allowPlaceholderImages = false;
			$this->m_linkedFilesMode = self::NORMAL;
		}

		$this->m_requestType = self::REQUEST_RESOURCE; // default

		// Request file URI
		$this->m_requestFileURI = $_SERVER['REQUEST_URI'];

			$this->m_requestFileURI = rawurldecode($this->m_requestFileURI);

			// We only want the page, not the query string
			// /home?q=query -> /home
			$pos = mb_strpos($this->m_requestFileURI, '?');

			if ($pos !== false)
				$this->m_requestFileURI = mb_substr($this->m_requestFileURI, 0, $pos);

			if (empty($this->m_requestFileURI))
				throw new Exception("Request is empty", self::E_REQUEST_IS_EMPTY);

		// Extract files from request
		$this->m_files = explode('|', $this->m_requestFileURI); // explode() always returns an array

			$webRoot = WEBROOT . '/';
			$webRootLength = mb_strlen($webRoot);

			// Remove WEBROOT & Quick validity check
			// We remove the WEBROOT because we are already in it (/public/static.php)
			foreach ($this->m_files as &$f) {

				if (mb_substr($f, 0, $webRootLength) == $webRoot)
					$f = mb_substr($f, $webRootLength);

				if ($f == self::IMAGE_PLACEHOLDER_FILE)
					$this->m_requestType = self::REQUEST_IMAGE_PLACEHOLDER;

				if ($this->m_requestType == self::REQUEST_RESOURCE && !is_file($f)) {
					// Maybe it doesn't find it because there is a file version in the name (for browser cache).
					// Like 'css/responsive.v1558194608.css' -> 'css/responsive.css'
					// Let's try & remove the v1558194608 part
					$f = preg_replace('#\.v[0-9]+(\.[^.]+)$#i', '$1', $f);

					// If it still doesn't exist
					if (!is_file($f))
						throw new Exception("File not found: '$f'", self::E_FILE_NOT_FOUND);
				}
			}
			unset($f);

		// File type
		$this->m_fileType = null;

		// Image Placeholder
		if ($this->m_requestType == self::REQUEST_IMAGE_PLACEHOLDER) {
			$this->m_fileType = self::IMAGE_PLACEHOLDER;
		// CSS, JS: File extension = file type
		} else {
			$this->m_fileType = pathinfo($this->m_files[0], PATHINFO_EXTENSION);
				$this->m_fileType = mb_strtolower($this->m_fileType);
		}

		if (!in_array($this->m_fileType, self::SUPPORTED_FILE_TYPES))
			throw new Exception("File type not supported: {$this->m_fileType}", self::E_FILE_TYPE_NOT_SUPPORTED);
	}

	/**
	 * @return string|array|null
	 */
	public function getFiles() {
		return $this->m_files;
	}

	/**
	 * Starts routing
	 */
	public function exec(): void {

		// File not found
		if (empty($this->m_fileType) || $this->m_files === null)
			$this->fileNotFound();

		// If image placeholders are deactivated, return 404
		if ($this->m_fileType == self::IMAGE_PLACEHOLDER
			&& !$this->m_allowPlaceholderImages)
				$this->fileNotFound();

		// Default file rendering
		$renderer = null;

		switch ($this->m_fileType) {

			case self::CSS:
				$renderer = new FileRendererCSS($this);
				break;

			case self::JAVASCRIPT:
				$renderer = new FileRendererJS($this);
				break;

			case self::IMAGE_PLACEHOLDER:
				$renderer = new FileRendererImagePlaceholder($this);
				break;
		}

		if ($renderer === null)
			$this->fileNotFound();

		if ($this->m_linkedFilesMode == self::MERGED)
			$renderer->renderMerged();
		else
			$renderer->renderFlat();
	}

	/**
	 * Sends 404 and exit;s
	 */
	public function fileNotFound(): void {
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
		exit;
	}
}
