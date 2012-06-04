<?php

namespace JawHare\Storage;

/**
 * Storage for system settings 
 */
abstract class SettingsStorage extends DatabaseStorage
{
	/**
	 * Saves the settings into storage
	 * @param array $data Associative array of $var => $value pairs
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future)
	 */
	abstract public function save_settings($data);
	
	/**
	 * Delete settings from storage
	 * @param array $variables Numeric array of variable names to delete
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future)
	 */
	abstract public function delete_settings($variables);
	
	/**
	 * Load the settings out of storage
	 * @return array An associative array of $var=>$value pairs
	 */
	abstract public function load_settings();
}