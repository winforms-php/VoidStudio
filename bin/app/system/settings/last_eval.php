namespace VoidEngine;

EngineAdditions::loadModule ('System.Windows.Forms.DataVisualization');
EngineAdditions::loadModule ('System.Windows.Forms.DataVisualization.Design');

$form = new Form;

$chart = new WFObject ('System.Windows.Forms.DataVisualization.Charting.Chart');

$form->show ();