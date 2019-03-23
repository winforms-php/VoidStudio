<?php

namespace VoidEngine;

class Chart extends Control
{
	protected $annotations;
	protected $chartAreas;
	protected $legends;
	protected $series;
	protected $titles;

    public function __construct (Control $parent = null)
	{
        $this->selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.DataVisualization.Charting.Chart', 'System.Windows.Forms.DataVisualization'));
		Components::addComponent ($this->selector, $this);

		$this->annotations = new Items ($this->getProperty ('Annotations'));
		$this->chartAreas  = new Items ($this->getProperty ('ChartAreas'));
		$this->legends 	   = new Items ($this->getProperty ('Legends'));
		$this->series	   = new Items ($this->getProperty ('Series'));
		$this->titles 	   = new Items ($this->getProperty ('Titles'));
        
		if ($parent)
			$this->parent = $parent;
	}
}

class Annotation extends Control
{
	public function __construct ()
	{
        $this->selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.DataVisualization.Charting.Annotation', 'System.Windows.Forms.DataVisualization'));
		Components::addComponent ($this->selector, $this);
	}
}

class ChartArea extends Control
{
	protected $axes;

	public function __construct ()
	{
        $this->selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.DataVisualization.Charting.ChartArea', 'System.Windows.Forms.DataVisualization'));
		Components::addComponent ($this->selector, $this);

		$this->axes = new Items ($this->getProperty ('Axes'));
	}
}

class Legend extends Control
{
	public function __construct ()
	{
        $this->selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.DataVisualization.Charting.Legend', 'System.Windows.Forms.DataVisualization'));
		Components::addComponent ($this->selector, $this);
	}
}

class Series extends Control
{
	public function __construct ()
	{
        $this->selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.DataVisualization.Charting.Series', 'System.Windows.Forms.DataVisualization'));
		Components::addComponent ($this->selector, $this);
	}
}

class Title extends Control
{
	public function __construct ()
	{
        $this->selector = VoidEngine::createObject (new ObjectType ('System.Windows.Forms.DataVisualization.Charting.Title', 'System.Windows.Forms.DataVisualization'));
		Components::addComponent ($this->selector, $this);
	}
}

?>
