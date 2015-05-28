<?php namespace CCTM\Model;

use CCTM\Exceptions\FileExistsException;
use CCTM\Exceptions\FileNotFoundException;
use CCTM\Exceptions\InvalidAttributesException;
use CCTM\Interfaces\ResourceInterface;
use CCTM\Interfaces\ValidatorInterface;
use Pimple\Container;

/**
 * Class FilebasedModel
 *
 * This base class is an interface for using flat files to store object data, e.g. JSON files.
 * The intended use case is that one directory contains files representing one data type: all
 * JSON files in that directory should have a normalized structure.
 *
 * TODO: make abstract?
 *
 * @package CCTM\Model
 */
class FilebasedModel implements ResourceInterface{

    use \CCTM\Traits\DotNotation;

    protected $dic;

    protected $id;
    protected $ext = 'json'; // without the dot
    protected $pk = 'id'; // name of primary key (should be one of the attributes)
    protected $context = 'create'; // create | update
    protected $filesystem;
    protected $validator;

    /**
     *
     * @param                           $dic        Container
     * @param                           $dir        string
     * @param ValidatorInterface|object $validator  object
     */
    public function __construct(Container $dic, $dir, ValidatorInterface $validator)
    {
        $this->dic = $dic;
        $this->filesystem = $this->dic['Filesystem']($dir);
        $this->validator = $validator;
    }

    public function getResourceUrl($type, $id=null)
    {
        return call_user_func($this->dic['resource_url'], $type,$id);
    }

    /**
     * Get relative filename within the Flysystem root
     *
     * @param $id
     *
     * @return string
     * @throws FileNotFoundException
     */
    public function getFilename($id)
    {
        // Avoid directory transversing
        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $id))
        {
            throw new FileNotFoundException('Invalid File Name', 40450, array(
                'id' => 'FileNotFoundException',
                'href' => '',
                'status' => 404,
                'title' => 'Invalid File Name',
                'detail' => 'Files must be identified by stubs only, without extensions.  Directory traversing and sub-directories are not allowed',
            ));
        }
        return $id.'.'.$this->ext;
    }

    /**
     * Get the primary key
     * @return mixed|null
     */
    public function getId()
    {
        return ($this->isNodeSet($this->pk)) ? $this->get($this->pk) : null;
        //return $this->id;
    }

    /**
     * Override "id" as the primary identifier for this object
     * @param $key
     */
    public function setPk($key)
    {
        $this->pk = $key;
    }

    /**
     * Says whether we are updating an existing item or creating a new one
     */
    public function isNew()
    {
        return ($this->context == 'create');
    }

    public function getOne($id)
    {
        //$this->filesystem->read($id);
        // TODO: permissions: can read?


        if (!$exists = $this->filesystem->has($this->getFilename($id)))
        {
            throw new FileNotFoundException('File not found', 50000, array(
                'id' => 'FileNotFoundException',
                'href' => '',
                'status' => 500,
                'detail' => 'File not found: '.$this->getFilename($id),
            ));
        }

        // Tricky... because this class represents both a specific object AND actions on objects/collections in general,
        // there can be headaches when fetching/duplicating/renaming objects
        $one = clone $this;
        $one->fromArray((array) $one->dic['JsonDecoder']->decode($this->filesystem->read($one->getFilename($id))));
        $one->context = 'update';
        $this->context = 'update';
        $one->id = $id; // required for duplication
        //$this->id = $id; // required getId
        return $one;
    }

    // TODO: filters
    public function getCollection(array $filters=array())
    {
        // TODO: cache this?
        $contents = $this->filesystem->listContents('/');

        //return $contents;
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
        $filtered = array();
        foreach ($contents as $i => $c)
        {
            if ($c['extension'] != $this->ext)
            {
                continue;
            }

            $filtered[] = $this->getOne($c['filename']);
        }
        return $filtered;
    }


    public function delete()
    {
        $this->filesystem->delete($this->getFilename($this->id));
        // do action?  Hook related items to this?
        return true;
    }

    /**
     * This is a file operation.  If the object attributes have not been persisted (i.e. saved to file),
     * then the new copy will not contain them.
     *
     * @param $new_id
     *
     * @return FilebasedModel
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function duplicate($new_id)
    {
        // Has this file been saved yet?
        // if ($this->isNew())

        if ($exists = $this->filesystem->has($this->getFilename($new_id)))
        {
            throw new FileExistsException('File not found', 40900, array(
                'id' => 'FileExistsException',
                'href' => '',
                'status' => 409,
                'detail' => 'File cannot be duplicated because the target file cannot be overwritten: '.$this->getFilename($new_id),
            ));
        }

        $this->filesystem->copy($this->getFilename($this->getId()), $this->getFilename($new_id));

        $copy = $this->getOne($new_id);
        // Update primary key
        $copy->set($copy->pk, $new_id);
        $copy->save();
        return $copy;
    }

    public function rename($new_id)
    {
        $this->duplicate($new_id);
        $this->delete();
    }

    public function save()
    {
        // Check PK
        $pk = $this->pk; // prepare string
        $this->id = ($this->isNodeSet($this->pk)) ? $this->get($pk) : null;
        if(!$this->id)
        {
            throw new InvalidAttributesException('Missing Primary Key', 50020, array(
                'id' => 'InvalidAttributesException',
                'href' => '',
                'status' => 500,
                'detail' => 'You cannot save this object without specifying its unique primary key ('.$this->pk.')',
            ));
        }
        // Validate
        if (!$this->validator->validate($this->toArray(), $this->context))
        {
            throw new InvalidAttributesException('Validation Error', 40020, array(
                'id' => 'InvalidAttributesException',
                'href' => '',
                'status' => 400,
                'detail' => 'Input validation failed with the following errors: '.$this->validator->getMessages(),
            ));
        }

        // After validation, mark this as an update
        $this->context = 'update';

        $this->filesystem->put($this->getFilename($this->id), $this->dic['JsonEncoder']->encode($this->data));

        return true;
    }
}

/*EOF*/