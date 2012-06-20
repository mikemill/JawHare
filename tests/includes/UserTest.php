<?php

namespace JawHare;

class UserTest extends \PHPUnit_Framework_TestCase
{
	protected $user;
	protected $user_data = array(
		'username' => 'PHPUnitTest',
		'fullname' => 'PHP Unit Testing',
		'email' => 'phpunit@jawhare.org',
		'salt' => 'NaCl',
		'admin' => true,
		'password' => 'BillyJoeBob',
	);

	protected $user_settings = array(
		'Foo' => 'bar',
		'test' => 'nope',
		'why?' => 'because'
	);

	static public function setUpBeforeClass()
	{
		global $ini;

		// User object needs the database
		$db = Database($ini['database']);
		// Clear out the user table
		$db->query('DELETE FROM users');
	}

	public function setUp()
	{
		$this->user = new User();
	}

	public function testSetterGetter()
	{
		$user = $this->user;

		$user->username($this->user_data['username'])
			->fullname($this->user_data['fullname'])
			->email($this->user_data['email'])
			->salt($this->user_data['salt'])
			->admin($this->user_data['username'])
			->passwd($this->user_data['password']);

		foreach ($this->user_settings AS $var => $val)
			$user->settings($var, $val);

		$this->assertEquals($user->username(), $this->user_data['username']);
		$this->assertEquals($user->fullname(), $this->user_data['fullname']);
		$this->assertEquals($user->email(), $this->user_data['email']);
		$this->assertEquals($user->salt(), $this->user_data['salt']);
		$this->assertEquals($user->admin(), $this->user_data['admin']);
		$this->assertTrue($user->validatepw($this->user_data['password']));

		foreach ($this->user_settings AS $var => $val)
			$this->assertEquals($user->settings($var), $val, $var);

	}

	/**
	 * @depends testSetterGetter
	 */
	public function testSave()
	{
		$user = $this->user;
		$user->username($this->user_data['username'])
			->fullname($this->user_data['fullname'])
			->email($this->user_data['email'])
			->salt($this->user_data['salt'])
			->admin($this->user_data['username'])
			->passwd($this->user_data['password']);

		foreach ($this->user_settings AS $var => $val)
			$user->settings($var, $val);
		
		$user->save();

		$this->assertNotEmpty($user->id());
	}

	/**
	 * @expectedException \JawHare\InvalidUserException
	 */
	public function testBadSave()
	{
		$this->user->fullname($this->user_data['fullname'])
			->email($this->user_data['email'])
			->salt($this->user_data['salt'])
			->admin($this->user_data['username'])
			->passwd($this->user_data['password'])
			->save();

	}

	/**
	 * @depends testSave
	 */
	public function testLoad()
	{
		$user = $this->user->load($this->user_data['username']);

		$this->assertNotEquals($user->id(), 0);
		$this->assertNotNull($user->id());

		foreach($this->user_data AS $variable => $value)
		{
			if ($variable == 'password')
				continue;
			$this->assertEquals($user->$variable(), $value, $variable . ', User: ' . $user->$variable() . ', Value: ' . $value);
		}

		$this->assertTrue($user->validatepw($this->user_data['password']));
	}

	/**
	 * @depends testLoad
	 */
	public function testUpdate()
	{
		$user = $this->user->load($this->user_data['username']);
		$user->salt('Pringles')->settings('testing', 'food')->save();

		$user2 = new User($user->id());

		foreach($this->user_data AS $variable => $value)
		{
			$this->assertEquals($user->$variable(), $user2->$variable(), $variable . ', User: ' . $user->$variable() . ', User2: ' . $user2->$variable());
		}

		foreach($user->settings() AS $variable => $value)
		{
			$this->assertEquals($value, $user2->settings($variable), $variable . ', User: ' . $value . ', User2: ' . $user2->settings($variable));
		}

		$this->assertEquals('food', $user2->settings('testing'));
		$user2->delete_settings('testing')->save();

		$this->assertNull($user2->settings('testing'));

		$user3 = new User($user->id());

		$this->assertNull($user3->settings('testing'));
	}
	
	

	/**
	 * The add_group and get_groups functions will be tested in the Groups test class.
	 */
}
