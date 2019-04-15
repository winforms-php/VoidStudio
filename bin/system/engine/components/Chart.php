<?php

namespace VoidEngine;

class Chart extends Control
{
	public $class 	  = 'System.Windows.Forms.DataVisualization.Charting.Chart';
	public $namespace = 'System.Windows.Forms.DataVisualization';
}

class Annotation extends Control
{
	public $class 	  = 'System.Windows.Forms.DataVisualization.Charting.Annotation';
	public $namespace = 'System.Windows.Forms.DataVisualization';

	public function __construct ()
	{
		parent::__construct (null);
	}
}

class ChartArea extends Control
{
	public $class 	  = 'System.Windows.Forms.DataVisualization.Charting.ChartArea';
	public $namespace = 'System.Windows.Forms.DataVisualization';

	public function __construct ()
	{
		parent::__construct (null);
	}
}

class Legend extends Control
{
	public $class 	  = 'System.Windows.Forms.DataVisualization.Charting.Legend';
	public $namespace = 'System.Windows.Forms.DataVisualization';

	public function __construct ()
	{
		parent::__construct (null);
	}
}

class Series extends Control
{
	public $class 	  = 'System.Windows.Forms.DataVisualization.Charting.Series';
	public $namespace = 'System.Windows.Forms.DataVisualization';

	public function __construct ()
	{
		parent::__construct (null);
	}
}

class Title extends Control
{
	public $class 	  = 'System.Windows.Forms.DataVisualization.Charting.Title';
	public $namespace = 'System.Windows.Forms.DataVisualization';

	public function __construct ()
	{
		parent::__construct (null);
	}
}
