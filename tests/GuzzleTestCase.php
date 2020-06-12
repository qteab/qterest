<?php


namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;

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
