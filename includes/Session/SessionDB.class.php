<?php
namespace JawHare\Session;

/**
 * Store the session in the database 
 */
class SessionDB extends Session
{
	/**
	 * The database object
	 * @var \JawHare\Database\Database
	 */
	protected $db = null;

	public function __construct()
	{
		// We want our own DB instance
		$this->db = \JawHare\Database();

		session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc'));

		// Did they pass a session cookie?
		if (isset($_COOKIE['PHPSESSID']))
			$this->start();
	}

	protected function close ()
	{
		$this->db = null;

		return true;
	}

	protected function destroy ($session_id)
	{
		$this->db->query("DELETE FROM sessions WHERE session_id {string:session_id}", array('session_id' => $session_id));

		return true;
	}

	protected function gc ($maxlifetime)
	{
		$maxlifetime = (string) $maxlifetime;
		$this->db->query("DELETE FROM sessions WHERE last_modified <= DATE_SUB(NOW(), INTERVAL {int:lifetime} SECOND)", array('lifetime' => $maxlifetime));
		return true;
	}

	protected function open ($save_path, $session_id)
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
	protected function read ($session_id)
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

	protected function write ($session_id , $session_data)
	{
		$this->db->query("REPLACE INTO sessions (session_id, data) VALUES ({string:session_id}, {string:data})", array('session_id' => $session_id, 'data' => $session_data));

		return true;
	}
}
