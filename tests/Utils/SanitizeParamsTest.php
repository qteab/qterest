<?php


namespace Tests\Utils;

use PHPUnit\Framework\TestCase;
use QTEREST\Utils\SanitizeParams;

class SanitizeParamsTest extends TestCase {

	public function paramsProvider() {
		return array(
			array(
				array(
					'email'   => 'hello@example.com',
					'message' => '<h1>Hello!</h1><script>document.body.innerHTML = ""</script>',
				),
				array(
					'email'   => 'hello@example.com',
					'message' => 'Hello!',
				),
			),
			array(
				array(
					'some_param' => '<style>body { background-color: black; }</style>',
				),
				array(
					'some_param' => '',
				),
			),
			array(
				array(
					'some_other_param' => array(
						'some_sub_param' => '<h2>Subtitle</h2>',
					),
				),
				array(
					'some_other_param' => array(
						'some_sub_param' => 'Subtitle',
					),
				),
			),
		);
	}

	/**
	 * @dataProvider paramsProvider
	 * @param $params
	 * @param $expected
	 */
	public function testSanitizeParams( $params, $expected ) {
		$sanitizedParams = SanitizeParams::sanitizeParams( $params );

		$this->assertEquals( $expected, $sanitizedParams );
	}
}
