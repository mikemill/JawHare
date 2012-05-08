<?php
class Session
{
	protected $db = null;
	protected $started = false;
	protected $data = null;

	static protected $instance = null;

	public function __construct()
	{
		// We want our own DB instance
		$this->db = Database::instance();

		if (self::$instance === null)
			self::$instance = $this;

		session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc'));

		// Did they pass a session cookie?
		if (isset($_COOKIE['PHPSESSID']))
			$this->start(); // No cookie == not logged in

	}

	public function started()
	{
		return $this->started;
	}

	public function start()
	{
		if (!$this->started)
		{
			session_start();

			$this->data = &$_SESSION;
			$this->started = true;
		}
	}

	public function __invoke($var, $val = null, $delete = false, $setnull = false, $unsetret = null)
	{
		if ($val === null)
		{
			if ($delete)
				unset($this->data[$var]);
			elseif ($setnull)
				$this->data[$var] = null;
			elseif (isset($this->data[$var]))
				return $this->data[$var];
			else
				return $unsetret;
		}
		else
		{
			$this->data[$var] = $val;
		}

		return $this;
	}

	public function close ()
	{
		$this->db = null;

		return true;
	}

	public function destroy ($session_id)
	{
		$this->db->query("DELETE FROM sessions WHERE session_id {string:session_id}", array('session_id' => $session_id));

		return true;
	}

	public function gc ($maxlifetime)
	{
		$maxlifetime = (string) $maxlifetime;
		$this->db->query("DELETE FROM sessions WHERE last_modified <= DATE_SUB(NOW(), INTERVAL {int:lifetime} SECOND)", array('lifetime' => $maxlifetime));
		return true;
	}

	public function open ($save_path, $session_id)
	{
		// Protect against weird ass session ids
		if (strtolower(session_id()) == "deleted")
		{
			$oldsessionid = session_id();
			session_regenerate_id(true);

			$newsessionid = session_id();
		}

		return true;
	}
	public function read ($session_id)
	{
		$result = $this->db->query("
			SELECT data
			FROM sessions
			WHERE session_id = {string:session_id}",
			array(
				'session_id' => $session_id,
			)
		);
		
		if (!$result->numrows())
			return false;

		$row = $result->row();
		
		return $row[0];
	}

	public function write ($session_id , $session_data)
	{
		global $db_conn;
		$this->db->query("REPLACE INTO sessions (session_id, data) VALUES ({string:session_id}, {string:data})", array('session_id' => $session_id, 'data' => $session_data));

		return true;
	}

	static public function instance($autostart = false)
	{
		if (self::$instance === null)
			new self();
		
		$sess = self::$instance;

		if ($autostart && !$sess->started())
			$sess->start();

		return $sess;
	}
}

function Session($var = null, $val = null)
{
	$sess = Session::instance();

	if ($var === null)
		return $sess;
	else
		return $sess($var, $val);
}