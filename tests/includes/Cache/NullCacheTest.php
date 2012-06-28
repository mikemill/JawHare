<?php

namespace JawHare;

class NullCacheTest extends \PHPUnit_Framework_TestCase
{
	protected $obj;

	protected function setUp()
	{
		global $ini;
		$this->obj = new Cache\NullCache();
	}

	public function testIO()
	{
		$ret = $this->obj->set('foo', 5, 100);
		$this->assertFalse($ret);

		$ret = $this->obj->get('foo');
		$this->assertFalse($ret);

		$ret = $this->obj->add('foo', 3, 100);
		// Should fail since add doesn't replace existing values
		$this->assertFalse($ret);

		$ret = $this->obj->replace('foo', 6, 100);
		$this->assertFalse($ret);

		$ret = $this->obj->delete('foo');
		$this->assertFalse($ret);
	}
}
