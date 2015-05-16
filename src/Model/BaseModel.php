<?php namespace CCTM\Model;

use CCTM\Exceptions\FileNotFoundException;
use CCTM\Exceptions\NotFoundException;

class BaseModel {

    protected $dic;
    protected $attr;
    protected $id;
    protected $subdir; // starts with leading slash

    public function __construct($dic, array $attributes = array())
    {
        $this->dic;
        $this->attr = $attributes;
    }

    /**
     * Gets the full path to the storage directory with trailing slash
     * @return string
     */
    public function getLocalDir()
    {
        $subdir = trim($this->subdir,'/');
        return $this->dic['storage_dir'].'/'.$subdir.'/';
    }

    public function getItem($id)
    {
        //$this->dic['Filesystem']->read($id);
        // TODO: permissions: can read?
        // Avoid directory transversing
        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $id))
        {
            throw new NotFoundException('Invalid resource name');
        }

        if (!$exists = $this->dic['Filesystem']->has())
        {
            throw new FileNotFoundException('File not found: '.$this->getLocalDir().$id.'.json');
        }
        return $this->dic['JsonDecoder']->decodeFile($this->getLocalDir().$id.'.json'); // '/path/to/file.json'
    }

    public function getCollection(array $filters=array())
    {
        // TODO: cache this so we're not having to iterate and parse every @!% file
    }

    public function get($key)
    {
        return (isset($this->attr[$key])) ? $this->attr[$key] : null;
    }

    public function set($key, $value)
    {
        $this->attr[$key] = $value;
    }


    public function save()
    {

        //$this->dic['JsonEncoder']->encode($this->attr, '/path/to/file.json');
        $this->dic['Filesystem']->put($file, $contents);
    }
}

/*EOF*/