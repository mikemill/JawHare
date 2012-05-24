<?php

namespace JawHare;

class EventTest extends \PHPUnit_Framework_TestCase
{
	protected $object;

	protected function setUp()
	{
		$this->object = new Event('anEvent');
	}

	public function testEvent() {
		$this->assertEquals($this->object->event(), 'anEvent');
	}

	public function testPropagate()
	{
		$anotherObject = $this->object->propagate(true);
		$this->assertEquals($this->object->propagate(), true);
		$this->assertEquals($anotherObject, $this->object);

	}

	public function testReturndata()
	{
		$this->object->returndata('to return');
		$this->assertEquals($this->object->returndata(), 'to return');
	}
}