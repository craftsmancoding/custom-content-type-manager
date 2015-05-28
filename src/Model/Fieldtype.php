<?php namespace CCTM\Model;

use Pimple\Container;

/**
 * Class Fieldtype
 *
 * A field type is something like text, textarea, or a custom field type.
 * This is in distinction to the Field model, which would pull up a specific
 * defined field instance.
 *
 * A field type can be anything that implements the FieldInterface contract.
 * E.g. see https://stackoverflow.com/questions/3993759/php-how-to-get-a-list-of-classes-that-implement-certain-interface
 * Note that we can't easily include classes that are loaded via autoload.
 *
 * This does NOT implement the Filebased model class.
 *
 * @package CCTM\Model
 */
class Fieldtype
{
    protected $dic;

    private $built_in = array(
        '\\CCTM\\Fields\\Checkbox',
        '\\CCTM\\Fields\\Colorselector',
        '\\CCTM\\Fields\\Date',
        '\\CCTM\\Fields\\Directory',
        '\\CCTM\\Fields\\Dropdown',
        '\\CCTM\\Fields\\Hidden',
        '\\CCTM\\Fields\\Image',
        '\\CCTM\\Fields\\Media',
        '\\CCTM\\Fields\\Multiselect',
        '\\CCTM\\Fields\\Relation',
        '\\CCTM\\Fields\\Text',
        '\\CCTM\\Fields\\Textarea',
        '\\CCTM\\Fields\\User',
        '\\CCTM\\Fields\\Wysiwyg',
    );

    public function __construct(Container $dic)
    {
        $this->dic = $dic;
    }

    public function getCollection(array $filters=array())
    {
        // pull up built-in classes
        $this->built_in;

        // look to settings to include any other classes

        // key/value localization?  E.g. "Text" instead of "CCTM\Fields\Text"
    }
}
/*EOF*/