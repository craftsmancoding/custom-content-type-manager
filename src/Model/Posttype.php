<?php namespace CCTM\Model;

class Posttype extends FilebasedModel
{
    function hydrate()
    {
        return array(
            'id' => '',
            'type' => '', // full PHP class name, a la Single Table Inheritance
            'label' => '',
            'description' => '',
            'css_class' => '',
            'extra' => '',
            'default_value' => '',
            'default_filter' => '',
            'validator' => '',
            'meta' => array()
        );
    }

    /**
     * @param $id
     *
     * @return Posttype
     * @throws FileFileNotFoundException
     * @throws FileNotFoundException
     */
    public function getOne($id)
    {

        if (!$exists = $this->filesystem->has($this->getFilename($id)))
        {
            throw new FileFileNotFoundException('File not found: '.$this->getFilename($id));
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
}
/*EOF*/