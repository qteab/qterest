<?php


namespace QTEREST\Utils;

use GuzzleHttp\ClientInterface;

class Recaptcha {

	/**
	 * @var string
	 */
	private $secret;

	/**
	 * @var ClientInterface
	 */
	private $guzzle;

	/**
	 * Recaptcha constructor.
	 *
	 * @param string          $secret
	 * @param ClientInterface $guzzle
	 */
	public function __construct( string $secret, ClientInterface $guzzle ) {
		$this->secret = $secret;
		$this->guzzle = $guzzle;
	}

	/**
	 * @param string          $secret
	 * @param ClientInterface $guzzel
	 * @return $this
	 */
	public static function make( string $secret, ClientInterface $guzzel ): self {
		return new self( $secret, $guzzel );
	}

	/**
	 * @param string $response
	 * @return bool
	 */
	public function validateResponse( string $response ) {
		$response = $this->guzzle->request(
			'POST',
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'json'       => array(
					'secret'   => $this->secret,
					'response' => $response,
				),
				'exceptions' => false,
			)
		);

		if ( $response->getStatusCode() !== 200 ) {
			return false;
		}

		$body = json_decode( $response->getBody()->getContents() );

		return $body->success;
	}
}
