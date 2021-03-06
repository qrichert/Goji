<script>

	/* <CONSTANTS> */

	const PAGE = '<?= $this->m_app->getRouter()->getCurrentPage(); ?>';

	// const DARK_MODE = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

	// const TOUCH_DEVICE = 'ontouchstart' in window ? true : false;

	// let SCREEN_WIDTH = window.innerWidth;
	// let SCREEN_HEIGHT = window.innerHeight;
	//
	// window.addEventListener('resize', function() {
	// 	SCREEN_WIDTH = window.innerWidth;
	// 	SCREEN_HEIGHT = window.innerHeight;
	// }, false);

	/* <HELPERS> */

	/**
	 * jQuery-like selectors.
	 * $() selects one element (= document.querySelector)
	 * $$() selects multiple elements (= document.querySelectorAll)
	 */
	let $ = selector => document.querySelector(selector);
	let $$ = selector => document.querySelectorAll(selector);

	/**
	 * element.addEventListener() helper
	 *
	 * Normal:
	 *
	 * ```js
	 * document.addEventListener('click', e => {
	 *     console.log(e);
	 *     $('body').style.opacity = '0.5';
	 * }, false);
	 * ```
	 *
	 * With connect():
	 *
	 * ```js
	 * connect(document, 'click', e => {
	 *     console.log(e);
	 *     $('body').style.opacity = '0.5';
	 * });
	 * ```
	 *
	 * @param {Node|Element} element (target)
	 * @param {String} signal (event)
	 * @param {CallableFunction} slot (listener)
	 * @param {Boolean} useCapture (optional, default = false)
	 * @return VoidFunction
	 */
	let connect = (element, signal, slot, useCapture = false) => {
		element.addEventListener(signal, e => slot(e), useCapture);
	};

	/* <FUNCTIONS> */

	let zip = (array1, array2) => {
		let zippedArray = [];
		let lengthArray1 = array1.length;
		let lengthArray2 = array2.length;

		if (lengthArray1 !== lengthArray2)
			throw new RangeError("Arrays must have matching lengths.");

		for (let i = 0; i < lengthArray1; i++) {
			zippedArray.push([array1[i], array2[i]]);
		}

		return zippedArray;
	};

	/**
	 * Sometimes you want to deactivate CSS transitions for one move,
	 * you can do that with el.style.transition = 'none'; The problem
	 * is when you do the opposite (e.g.: el.style.transition = null;)
	 * the animation will trigger automatically. To bypass this behaviour
	 * you need to provoke a reflow (flush) of the CSS before deactivation
	 * to cancel any pending animation. This function does exactly that.
	 *
	 * @param {Node|Element} el
	 * @return VoidFunction
	 */
	let flushCSSElement = el => {
		el.offsetHeight;
	};

</script>

<?php
$template->linkFiles([
	'js/lib/Goji/Polyfills.min.js',
	'js/lib/Goji/SimpleRequest.class.min.js'
]);

if ($this->m_app->getAppMode() === \Goji\Core\App::DEBUG
	&& $this->m_app->getRouter()->getCurrentPage() !== 'password-wall') {
		$template->linkFiles('js/lib/Goji/WindowSizeDisplay.min.js');
}
