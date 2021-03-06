<?php

namespace Goji\Parsing;

/**
 * Class JSON5
 *
 * @package Goji\Parsing
 */
class JSON5 {

	/**
	 * Converts JSON5 string to regular JSON.
	 *
	 * Doesn't preserve aspect.
	 *
	 * @param string $json5
	 * @return string
	 */
	public static function toJSON(string $json5): string {

		// Remove comments first (safely)

		// Multiline comments first
		$json5 = Parser::removeMultiLineCStyleComments($json5);

		// Single-line comments next
		$json5 = Parser::removeSingleLineCStyleComments($json5);

		// Backup values within single or double quotes
		preg_match_all(RegexPatterns::quotedStrings(), $json5, $hit, PREG_PATTERN_ORDER);

		$hitCount = count($hit[1]);
		for ($i = 0; $i < $hitCount; $i++) {
			$json5 = str_replace($hit[1][$i], '##########' . $i . '##########', $json5);
		}

		// Remove white-space around ':' (not selectors ! only those followed by a space)
		$json5 = preg_replace('#[\s\r\n\t]*:[\s\r\n\t]+([^\s\r\n\t])#ims', ':$1', $json5);
		// Remove white-space around ','
		$json5 = preg_replace('#[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])#ims', ',$1', $json5);
		// Remove white-space around '{'
		$json5 = preg_replace('#[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])#ims', '{$1', $json5);
		// Remove white-space around '}'
		$json5 = preg_replace('#[\s\r\n\t]*\}[\s\r\n\t]*?([^\s\r\n\t])#ims', '}$1', $json5);
		// Remove white-space around '['
		$json5 = preg_replace('#[\s\r\n\t]*\[[\s\r\n\t]*?([^\s\r\n\t])#ims', '[$1', $json5);
		// Remove white-space around ']'
		$json5 = preg_replace('#[\s\r\n\t]*\][\s\r\n\t]*?([^\s\r\n\t])#ims', ']$1', $json5);
		// Remove redundant white-space
		$json5 = preg_replace('#\p{Zs}+#ims', ' ', $json5);
		// Remove new lines
		$json5 = str_replace(["\r\n", "\r", "\n", PHP_EOL], '', $json5);

		// Now we make strings (quotes values) comply by:
		// 1. Replacing single quotes with double quotes
		for ($i = 0; $i < $hitCount; $i++) {

			// Current quotes
			$quoteType = mb_substr($hit[1][$i], 0, 1);

			// We don't want single quotes, double quotes are fine
			if ($quoteType == "'") {

				// Remove first and last character (i.e. single quotes)
				$hit[1][$i] = mb_substr($hit[1][$i], 1, -1);

				// Escape unsecaped double quotes
				$hit[1][$i] = preg_replace(RegexPatterns::unescapedDoubleQuotes(), '\"', $hit[1][$i]);

				// We don't need to escape single quotes anymore
				$hit[1][$i] = str_replace("\'", "'", $hit[1][$i]);

				// Replace them with double quotes
				$hit[1][$i] = '"' . $hit[1][$i] . '"';
			}

			// 2. Remove escaped new lines \\n
			$hit[1][$i] = preg_replace(RegexPatterns::escapedNewLines(), '\\n', $hit[1][$i]);
			// 3. Remove forbidden characters from strings
			$hit[1][$i] = preg_replace('#(\r\n|\n|\r|\t)#', ' ', $hit[1][$i]);
		}

		// Remove trailing commas ['hello', 'world',] -> ['hello', 'world']
		$json5 = preg_replace('#,[\s\r\n\t]*(\}|\]|\))#ims', '$1', $json5);
		// Remove leading decimal point (add 0)
		$json5 = preg_replace('#(\D)(\.\d+)#', '${1}0$2', $json5);
		// Remove trailing decimal point (add 0)
		$json5 = preg_replace('#(\d)\.(\D)#', '$1$2', $json5);
		// Remove + sign: +1337 -> 1337
		$json5 = preg_replace('#\+(\d)#', '$1', $json5);
		// Replace hexadecimal numbers y decimal numbers
		$json5 = preg_replace_callback(RegexPatterns::hexadecimalNumber(), function($matches) {
			return hexdec($matches[0]);
		}, $json5);

		// Add quotes around Identifier Names (i.e. keys)
		// In JSON it can be anything (ECMA-404) it can be anything that goes into double quotes
		// But in JSON5 (ECMAScript 5.1) it can be anything that could be a JavaScript variable name
		// which is, pretty much any character that isn't white space or a symbol that has meaning
		// like +-/\*:.;(), etc.
		// It can't start with a number (but can contain numbers)
		// It is necessarily preceded by { or ,
		$symbols = <<<'EOT'
			#+-/\*:.;,()[]{}§€£~"'<>=!?@¨^
			EOT;
			$symbols = str_replace(['#', ']'], ['\#', '\]'], $symbols);

		$re = '#';
			$re .= '(\{|,|\[)(?:[\s\r\n\t\p{Zs}]|\r\n)*'; // Any { or , or [ - followed by white-space?
			$re .= '([^' . $symbols . '\s\r\n\t\p{Zs}\d' . ']'; // Followed by a character !symbols !white-space !number
			$re .= '[^' . $symbols . '\s\r\n\t\p{Zs}' . ']*)'; // Followed by anything !symbols !white-space
			$re .= '[\s\r\n\t\p{Zs}]*:'; // Followed by white-space? and colon :
			$re .= '#';

		$json5 = preg_replace($re, '$1"$2":', $json5); // { or , or [ + " + key + "

		// Restore backupped values within single or double quotes
		for ($i = 0; $i < $hitCount; $i++) {
			$json5 = str_replace('##########' . $i . '##########', $hit[1][$i], $json5);
		}

		return $json5;
	}

	/**
	 * JSON5 to array.
	 *
	 * @param string $json5
	 * @param bool $assoc (optional) default = false Return array instead of stdClass object
	 * @return array|\stdClass|null
	 */
	public static function decode(string $json5, bool $assoc = false) {
		return json_decode(self::toJSON($json5), $assoc);
	}
}
