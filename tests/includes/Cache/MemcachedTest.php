<?php

namespace JawHare;

class CacheMemcachedTest extends \PHPUnit_Framework_TestCase
{
	protected $obj;

	protected function setUp()
	{
		global $ini;
		if (!class_exists('Memcached', false))
			$this->markTestSkipped('Memcached doesn\'t exist');

		if (empty($ini['cache']['Memcached']))
			$this->markTestSkipped('No Memcached configuration');

		$this->obj = new Cache\CacheMemcached($ini['cache']['Memcached']);
	}

	public function testIO()
	{
		$ret = $this->obj->set('foo', 5, 100);
		$this->assertTrue($ret);

		$ret = $this->obj->get('foo');
		$this->assertEquals($ret, 5);

		$ret = $this->obj->add('foo', 3, 100);
		// Should fail since add doesn't replace existing values
		$this->assertFalse($ret);
		$this->assertEquals($this->obj->get('foo'), 5);

		$ret = $this->obj->replace('foo', 6, 100);
		$this->assertTrue($ret);
		$this->assertEquals($this->obj->get('foo'), 6);

		$ret = $this->obj->delete('foo');
		$this->assertTrue($ret);

		$ret = $this->obj->delete('foo');
		$this->assertFalse($ret);

		$ret = $this->obj->add('foo', 3, 100);
		// Should fail since add doesn't replace existing values
		$this->assertTrue($ret);
		$this->assertEquals($this->obj->get('foo'), 3);
	}
}
