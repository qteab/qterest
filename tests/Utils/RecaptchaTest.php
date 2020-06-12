<?php


namespace Tests\Utils;

use GuzzleHttp\Psr7\Response;
use QTEREST\Utils\Recaptcha;
use Tests\GuzzleTestCase;

class RecaptchaTest extends GuzzleTestCase {


	public function validateResponseProvider() {
		return array(
			array(
				$this->makeClient(
					array(
						new Response(
							200,
							array(),
							json_encode(
								array(
									'success' => true,
								)
							)
						),
					)
				),
				true,
			),
			array(
				$this->makeClient(
					array(
						new Response(
							400,
							array(),
							json_encode(
								array(
									'success' => false,
								)
							)
						),
					)
				),
				false,
			),
			array(
				$this->makeClient(
					array(
						new Response(
							200,
							array(),
							json_encode(
								array(
									'success' => false,
								)
							)
						),
					)
				),
				false,
			),
		);
	}


	/**
	 * @param $client
	 * @param $expected
	 * @dataProvider validateResponseProvider
	 */
	public function testValidateResponse( $client, $expected ) {
		$recaptcha = new Recaptcha( 'aaaaaaaaaaaaaaa', $client );

		$this->assertEquals( $expected, $recaptcha->validateResponse( '242353464576587697809980' ) );
	}
}
