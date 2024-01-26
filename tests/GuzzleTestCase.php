<?php


namespace Tests;

use QTEREST\Vendor\GuzzleHttp\Client;
use QTEREST\Vendor\GuzzleHttp\ClientInterface;
use QTEREST\Vendor\GuzzleHttp\Handler\MockHandler;
use QTEREST\Vendor\GuzzleHttp\HandlerStack;
use QTEREST\Vendor\PHPUnit\Framework\TestCase;

abstract class GuzzleTestCase extends TestCase {


	public function makeClient( array $responses ): ClientInterface {

		$mock = new MockHandler( $responses );

		$handlerStack = HandlerStack::create( $mock );

		return new Client(
			array(
				'handler' => $handlerStack,
			)
		);
	}
}
