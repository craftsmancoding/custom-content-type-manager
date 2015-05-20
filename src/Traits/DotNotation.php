<?php namespace CCTM\Traits;

/**
 * Class DotNotation
 *
 * Provides get() and set() methods to a model class to they can support nested data structures using dot-notation.
 * E.g. $obj->set('x.y', 'z') infers an internal data structure of
 *  Array('x' => Array('y' => 'z'))
 *
 * See https://medium.com/@assertchris/dot-notation-3fd3e42edc61
 * @package CCTM\Traits
 */
trait DotNotation {

    protected $data = array();


    public function isNodeSet($key)
    {

        if (isset($this->data[$key]))
        {
            return true;
        }
        $array = $this->data;
        foreach (explode('.', $key) as $part) {
            if (!is_array($array) or !isset($array[$part]))
            {
                return false;
            }

            $array = $array[$part];
        }

        return true;

    }

    public function nodeUnset($key)
    {
        $array =& $this->data;

        $parts = explode(".", $key);

        while (count($parts) > 1) {
            $part = array_shift($parts);

            if (isset($array[$part]) and is_array($array[$part])) {
                $array =& $array[$part];
            }
        }

        unset($array[array_shift($parts)]);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->getData($this->data, $key);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->setData($this->data, $key, $value);
    }

    public function toArray()
    {
        return $this->data;
    }

    /**
     * Set/overwrite internal data with array provided.
     * @param array $array
     */
    public function fromArray(array $array = array())
    {
        $this->data = $array;
    }

    /**
     * Recursively merges the provided $array into the internal data.
     * Warning: array lists (e.g. tags) will not be appended to as might be expected. See array_replace_recursive for examples.
     * @param array $array
     *
     * @return array
     */
    public function mergeArray(array $array)
    {
        return $this->data = array_replace_recursive($this->data, $array);
    }

    /**
     * @param array $array
     * @param string $key
     * @param mixed $value
     */
    protected function setData(array &$array, $key, $value)
    {
        $parts = explode('.', $key);

        while (count($parts) > 1) {
            $part = array_shift($parts);

            if (!isset($array[$part]) or !is_array($array[$part]))
            {
                $array[$part] = [];
            }

            $array =& $array[$part];
        }

        $array[array_shift($parts)] = $value;
    }

    /**
     * @param array $array
     * @param string $key
     *
     * @return null
     */
    protected function getData(array $array, $key)
    {
        if (isset($array[$key]))
        {
            return $array[$key];
        }

        foreach (explode('.', $key) as $part) {
            if (!is_array($array) or !isset($array[$part]))
            {
                return null;
            }

            $array = $array[$part];
        }

        return $array;
    }

}