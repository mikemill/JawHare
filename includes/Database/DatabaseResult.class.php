<?php

namespace JawHare\Database;

interface DatabaseResult
{
	public function numrows();
	public function assoc();
	public function row();
	public function affected_rows();
	public function insert_id();
	public function seek($row);
}
