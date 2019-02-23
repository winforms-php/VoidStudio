namespace VoidEngine;

$objs = VoidStudioAPI::getObjects ('main');
VLFImporter::import (APP_DIR .'/forms/about.vlf', $objs['PropertiesList__List'], $objs['EventsList__ActiveEvents'], $objs['PropertiesPanel__SelectedComponent'], $objs['Designer__FormsList']);