<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * A collection is a set of objects that are created for a DatabaseResult set.
 * Its purpose is to allow you to select many of something but not create the objects for them until you are ready to work on it. 
 */
class Collection
{
	/**
	 * The result set
	 * @var \JawHare\Database\DatabaseResult
	 */
	protected $result = null;
	
	/**
	 * The class to create from the results
	 * @var string
	 */
	protected $classtype = null;
	
	/**
	 * The maximum index+1 (zero based) for the set
	 * @var int
	 */
	protected $maxindex = 0;

	/**
	 *
	 * @param \JawHare\Database\DatabaseResult $result
	 * @param string $classtype 
	 */
	public function __construct($result, $classtype)
	{
		$this->result = $result;
		$this->classtype = $classtype;
		$this->maxindex = $this->result->numrows();
	}

	/**
	 * The number of items in the result set
	 * @return type 
	 */
	public function num()
	{
		return $this->maxindex;
	}

	/**
	 * Get the next object from the results
	 * @return false|Classtype obj Returns false if there are no more results
	 */
	public function next()
	{
		$array = $this->result->assoc();

		if ($array === false)
			return false;

		return new $this->classtype($array);
	}

	/**
	 * Get the object in the index position of the result sets.
	 * @param int $index Must be 0 <= $index < $maxindex
	 * @return false|Classtype obj Returns false if index is outside of the range or if the result set was unable to see to that position.
	 */
	public function get($index)
	{
		if ($index < 0 || $index >= $this->maxindex)
			return false;

		$ret = $this->result->seek($index);

		if ($ret === false)
			return false;

		return $this->next();
	}
}