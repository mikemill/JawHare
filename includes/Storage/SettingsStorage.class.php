<?php

namespace JawHare\Storage;

abstract class SettingsStorage extends DatabaseStorage
{
	abstract public function save_settings($data);
	abstract public function delete_settings($variables);
}