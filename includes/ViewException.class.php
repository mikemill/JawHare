<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * Exception class for view problems 
 */
class ViewException extends \Exception
{
	protected $view = '';
	protected $problem = '';

	public function __construct($problem, $view)
	{
		$msg = 'Unknown problem with view ' . $view;

		switch ($problem)
		{
			case '404':
				$msg = "Could not load the view $view";
				break;
		}

		$this->view = $view;
		$this->problem = $problem;
		$this->message = $msg;
	}
}