<?php

	namespace Goji\Blueprints;

	use Goji\Core\App;

	/**
	 * Interface ControllerInterface
	 *
	 * @package Goji\Blueprints
	 */
	interface ControllerInterface extends HttpStatusInterface {

		public function __construct(App $app);
		public function render();
		public function getApp();
		public function getCacheId(string $append = null): string;
	}
