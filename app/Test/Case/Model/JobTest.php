<?php
App::uses('Job', 'Model');

/**
 * Job Test Case
 *
 */
class JobTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.job',
		'app.user',
		'app.card',
		'app.customer',
		'app.order',
		'app.project',
		'app.event'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Job = ClassRegistry::init('Job');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Job);

		parent::tearDown();
	}

}
