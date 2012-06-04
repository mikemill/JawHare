<?php

namespace JawHare\Storage;

class SettingsStorageMySQL extends SettingsStorage
{
	public function save_settings($data)
	{
		$rows = array();

		foreach ($data AS $var => $value)
			$rows[] = array($var, $value);

		return $this->db->insert('settings', array(
			'variable' => 'string',
			'value' => 'string',
		), $rows, 'replace');
	}

	public function delete_settings($variables)
	{
		return $this->db->query('
			DELETE FROM settings
			WHERE variable IN ({array_string:variables})',
			array(
				'variables' => $variables,
			),
			'write'
		);
	}

	public function load_settings()
	{
		$results = $this->db->query('
			SELECT variable, value
			FROM settings');

		$ret = array();

		while ($row = $results->assoc())
			$ret[$row['variable']] = $row['value'];

		return $ret;
	}
}