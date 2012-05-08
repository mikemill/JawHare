<?php

namespace JawHare;

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