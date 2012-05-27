<?php

namespace JawHare\Storage;

abstract class SettingsStorage extends DatabaseStorage
{
	protected $columns = array(
		'variable' => 'string',
		'value' => 'string',
	);

	protected $table = 'settings';

	abstract public function save_settings($data);
	abstract public function delete_settings($variables);
}