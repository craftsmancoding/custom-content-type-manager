<?php namespace CCTM\Model;

use CCTM\Exceptions\FileNotFoundException;
use CCTM\Exceptions\InvalidAttributesException;
use CCTM\Exceptions\NotFoundException;

class FilebasedModel {

    protected $dic;
    protected $attr;
    protected $id;
    protected $ext = 'json'; // without the dot
    protected $pk; // primary key (should be one of the attributes)
    protected $context = 'update'; // create | update
    protected $validator; // separate from $dic so we don't need to rely a convention to get the exact validator classname

    public function __construct($dic, $validator)
    {
        $this->dic = $dic;

        // For testing the BaseModel, otherwise set in the child class
        if (empty($this->pk)) {
            $this->pk = $dic['pk'];
        }
    }

    public function __isset($key)
    {
        return (isset($this->attr->$key)) ? true : false;
    }

    public function __unset($key)
    {
        unset($this->attr->$key);
    }

    public function __get($key)
    {
        return (isset($this->attr->$key)) ? $this->attr->$key : null;
    }

    public function __set($key, $value)
    {
        $this->attr->$key = $value;
    }

    public function getAttributes()
    {
        return $this->attr;
    }

    /**
     * Get relative filename within the Flysystem root
     *
     * @param $id
     *
     * @return string
     * @throws NotFoundException
     */
    public function getFilename($id)
    {
        // Avoid directory transversing
        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $id))
        {
            throw new NotFoundException('Invalid resource name');
        }
        return $id.'.'.$this->ext;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Says whether we are updating an existing item or creating a new one
     */
    public function isNew()
    {
        return ($this->context == 'create');
    }

    public function getItem($id)
    {
        //$this->dic['Filesystem']->read($id);
        // TODO: permissions: can read?


        if (!$exists = $this->dic['Filesystem']->has($this->getFilename($id)))
        {
            throw new FileNotFoundException('File not found: '.$this->getLocalDir().$id.'.json');
        }

        $this->attr = $this->dic['JsonDecoder']->decode($this->dic['Filesystem']->read($this->getFilename($id)));
        $this->context = 'update';
        $this->id = $id;
        return $this;
    }

    // TODO: filters
    public function getCollection(array $filters=array())
    {
        // TODO: cache this?
        $contents = $this->dic['Filesystem']->listContents('/');

        // Sample contents
        //        Array
        //        (
        //            [type] => file
        //            [path] => x.json
        //            [timestamp] => 1432097947
        //            [size] => 23
        //            [dirname] =>
        //            [basename] => x.json
        //            [extension] => json
        //            [filename] => x
        //        )
        foreach ($contents as $i => $c)
        {
            if ($c['extension'] != $this->ext)
            {
                continue;
            }

            $contents[$i] = $this->getItem($c['filename']);
        }
        return $contents;
    }

    /**
     * Supply an array or an object
     * From https://stackoverflow.com/questions/1869091/how-to-convert-an-array-to-object-in-php
     * @param mixed $attributes
     */
    public function fromArray($attributes = array())
    {
        $this->context = 'create';
        $this->attr = $this->dic['JsonDecoder']->decode( ($this->dic['JsonEncoder']->encode($attributes)));
    }


    public function duplicate($new_id)
    {
        if ($exists = $this->dic['Filesystem']->has($this->getFilename($new_id)))
        {
            throw new FileExistsException('File cannot be ovewritten. '.$this->getFilename($new_id));
        }
    }


    public function save()
    {
        // Check PK
        $pk = $this->pk; // prepare string
        $id = (isset($this->attr->$pk)) ? $this->attr->$pk : null;
        if(!$id)
        {
            throw new InvalidAttributesException('Missing primary key.');
        }
        // Validate
        if (!$this->dic['Validator']->validate($this->attr, $this->context))
        {
            throw new InvalidAttributesException($this->dic['Validator']->getMessages());
        }

        // After validation, mark this as an update
        $this->context = 'update';

        $this->dic['Filesystem']->put($this->getFilename($id), $this->dic['JsonEncoder']->encode($this->attr));
    }
}

/*EOF*/