<?php


namespace QTEREST\Utils;

class SanitizeParams {

	/**
	 * This function is a replica of wp_strip_all_tags()
	 *
	 * @param $string
	 * @param bool   $remove_breaks
	 * @return string
	 */
	protected static function stripAllTags( $string, $remove_breaks = false ) {
		$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$string = strip_tags( $string );

		if ( $remove_breaks ) {
			$string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
		}

		return trim( $string );
	}

	/**
	 * This function strips alla tags recursively
	 *
	 * @param array $params
	 * @return array
	 */
	public static function sanitizeParams( array $params ): array {
		foreach ( $params as $key => $param ) {
			if ( is_array( $param ) ) {
				$params[ $key ] = self::sanitizeParams( $param );
				continue;
			}

			$params[ $key ] = self::stripAllTags( $param );
		}

		return $params;
	}
}
