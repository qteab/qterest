<?php

/**
 * This file contains settings for qterest;
 */

namespace QTEREST;

use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {

	const MailChimp = 'mailchimp';
	const Search    = 'search';
	const Contact   = 'contact';

	/**
	 * Available settings
	 */
	const Settings = array(
		self::Search,
		self::Contact,
		self::MailChimp,
	);

	/**
	 * @var array
	 */
	private static $defaults = array(
		self::Search    => false,
		self::Contact   => true,
		self::MailChimp => false,
	);

	/**
	 * @param $settings
	 * @return bool
	 */
	private static function verifySettings( $settings ): bool {
		if ( ! $settings ) {
			return false;
		}

		foreach ( self::Settings as $setting ) {
			if ( ! key_exists( $setting, $settings ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public static function getSettings(): array {

		global $qterest_settings;

		if ( ! $qterest_settings ) {
			$qterest_settings = apply_filters( 'qterest_settings', self::$defaults );
		}

		if ( ! self::verifySettings( $qterest_settings ) ) {
			throw new Exception( 'Missing settings or invalid format!' );
		}

		return $qterest_settings;
	}

	/**
	 * @param string $setting
	 * @return mixed
	 * @throws Exception
	 */
	public static function isEnabled( string $setting ): bool {

		if ( ! in_array( $setting, self::Settings ) ) {
			throw new Exception( "Can't find setting {$setting}" );
		}

		return self::getSettings()[ $setting ];
	}
}
