<?php
/**
 * Super simple storage object needed so we handle properties that have not been set.
 *
 * @package CCTM
 */
namespace CCTM;
class Data {
    
    public $data = array();
    public $notset = array();
    public $bounces = array();
    
    // Define here a valid hash. If set, only these keys can be set.
    // The keys used in this template are irrelevant. We include the entire key/value
    // structure because it's easier to pass and it's faster to check (isset vs in_array)
    public $template = array();

    /**
     * Our Getter. We track any "misses" in the notset array for debugging.
     */
    public function __get($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        array_push($this->notset, $key);
        return null;
    }
    
    /**
     * Our setter
     */
    public function __set($key,$value) {
        if (!$this->template) {
            $this->data[$key] = $value;
        }
        elseif(isset($this->template[$k])) {
            $this->data[$key] = $value;
        }
        else {
            array_push($this->bounces, $key);
        }
    }
    
    /**
     * Bulk add values
     * @param array $array the key/value pairs to add
     * @param boolean $reset if true, the contents of $array will completely replace
     *  the existing contents of $this->data, i.e. "resetting" or "initializing" the array.
     */
    public function fromArray(array $array, $reset=false) {
        if ($reset) {
            $this->data = $array;
        }
        else {
            foreach ($array as $k => $v) {
                $this->data[$k] = $v;
            }
        }
    }
}

/*EOF*/