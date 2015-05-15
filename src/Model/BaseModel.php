<?php namespace CCTM\Model;

use CCTM\Exceptions\NotFoundException;

class BaseModel {

    protected $dic;
    protected $attr;
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
        return $this->dic['JsonDecoder']->decodeFile($this->getLocalDir().$id.'.json'); // '/path/to/file.json'
    }

    public function getCollection()
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
        $this->dic['JsonEncoder']->encodeFile($this->attr, '/path/to/file.json');
    }
}

/*EOF*/