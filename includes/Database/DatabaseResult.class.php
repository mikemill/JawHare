<?php

namespace JawHare\Database;

/**
 * The interface for Database Results 
 */
interface DatabaseResult
{
	/**
	 * Return the number of rows in this result set
	 * @return int
	 */
	public function numrows();
	
	/**
	 * Get the next row in the reslt set as an associative array
	 * @return array|false Return false if no more rows
	 */
	public function assoc();

	/**
	 * Get the next row in the reslt set as a numeric array
	 * @return array|false Return false if no more rows
	 */
	public function row();
	
	/**
	 * @return int The number of rows affected by the operation 
	 */
	public function affected_rows();
	
	/**
	 * @return int The id of the auto increment row for the operation 
	 */
	public function insert_id();
	
	/**
	 * Change the pointer in the result set to point to a particular row
	 * @param int $row
	 * @return boolean
	 */
	public function seek($row);
}
