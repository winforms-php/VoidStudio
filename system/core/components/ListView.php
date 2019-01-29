<?php

namespace VoidEngine;

class ListView extends Control
{
    protected $items;
    protected $columns;
    protected $smallImagesList;
    protected $largeImagesList;

    public function __construct (Control $parent = null)
    {
        parent::__construct ($parent, self::class);

        $this->items           = new ListViewItems ($this->getProperty (['Items', 'object']));
        $this->columns         = new ListViewColumns ($this->getProperty (['Columns', 'object']));
        $this->smallImagesList = new ImageList;
        $this->largeImagesList = new ImageList;

        // $this->setProperty ('SmallImageList', $this->smallImagesList->selector, 'object');
        // $this->setProperty ('LargeImageList', $this->largeImagesList->selector, 'object');
    }

    public function get_items ()
    {
        return $this->items;
    }

    public function get_selectedItems ()
    {
        return new Items ($this->getProperty (['SelectedItems', 'object']));
    }

    public function get_columns ()
    {
        return $this->columns;
    }

    public function get_fullRowSelect ()
    {
        return $this->getProperty (['FullRowSelect', 'bool']);
    }

    public function set_fullRowSelect (bool $fullRowSelect)
    {
        $this->setProperty ('FullRowSelect', [$fullRowSelect, 'bool']);
    }

    public function get_gridLines ()
    {
        return $this->getProperty (['GridLines', 'bool']);
    }

    public function set_gridLines (bool $gridLines)
    {
        $this->setProperty ('GridLines', [$gridLines, 'bool']);
    }

    public function get_view ()
    {
        return $this->getProperty ('View', 'int');
    }

    public function set_view (int $view)
    {
        $this->setProperty ('View', [$view, 'int']);
    }

    public function get_readOnly ()
    {
        return $this->getProperty (['LabelEdit', 'bool']);
    }

    public function set_readOnly (bool $readOnly)
    {
        $this->setProperty ('LabelEdit', [$readOnly, 'bool']);
    }

    public function get_checkboxes ()
    {
        return $this->getProperty (['CheckBoxes', 'bool']);
    }

    public function set_checkboxes (bool $checkBoxes)
    {
        $this->setProperty ('CheckBoxes', [$checkBoxes, 'bool']);
    }

    public function dispose ()
	{
        VoidEngine::removeObject ($this->getProperty (['Items', 'object']));
        VoidEngine::removeObject ($this->getProperty (['Columns', 'object']));
        unset ($this->items, $this->columns);
        
        parent::dispose ();
	}
}

class ListViewItem extends Control
{
    protected $subItems;

    public function __construct (string $text = '')
    {
        parent::__construct (null, self::class);

        $this->text     = $text;
        $this->subItems = new Items ($this->getProperty (['SubItems', 'object']));
    }

    public function get_subItems ()
    {
        return $this->subItems;
    }
}

class ListViewItems extends Items
{
    public function __get ($name)
	{
		switch (strtolower ($name))
		{
			case 'count':
                return VoidEngine::getProperty ($this->selector, ['Count', 'int']);
            break;
				
            case 'list':
                $size = VoidEngine::getProperty ($this->selector, ['Count', 'int']);
                $list = [];
                
				for ($i = 0; $i < $size; ++$i)
                    $list[] = VoidEngine::getArrayValue ($this->selector, [$i, 'object']);
                    
                return $list;
            break;
		}
	}
	
	public function add ($value)
	{
		return $this->offsetSet (null, $value instanceof ListViewItem ? $value->selector : $value);
	}
	
	public function append ($value)
	{
		return $this->offsetSet (null, $value instanceof ListViewItem ? $value->selector : $value);
	}
	
	public function offsetSet ($index, $value)
	{
        return $index === null ?
            VoidEngine::callMethod ($this->selector, 'Add', [$value, 'object']) :
            VoidEngine::callMethod ($this->selector, 'Insert', [(int) $index, 'int'], [$value, 'object']);
	}
	
	public function offsetGet ($index)
	{
		return VoidEngine::getArrayValue ($this->selector, [(int) $index, 'object']);
	}
}

class ColumnHeader extends Control
{
    public function __construct (string $text = '')
    {
        parent::__construct (null, self::class);

        $this->text = $text;
    }
}

class ListViewColumns extends ListViewItems
{
    public function add ($value)
	{
		return $this->offsetSet (null, $value instanceof ColumnHeader ? $value->selector : $value);
	}
	
	public function append ($value)
	{
		return $this->offsetSet (null, $value instanceof ColumnHeader ? $value->selector : $value);
	}
}

class ImageList extends Component
{
    protected $images;

    public function __construct ()
    {
        parent::__construct (self::class);

        $this->images = new ImageListImages ($this->getProperty (['Images', 'object']));
    }

    public function get_images ()
    {
        return $this->images;
    }
}

class ImageListImages extends ListViewItems
{
    public function add ($value)
	{
		return $this->offsetSet (null, $value);
	}
	
	public function append ($value)
	{
		return $this->offsetSet (null, $value);
	}
}

?>
