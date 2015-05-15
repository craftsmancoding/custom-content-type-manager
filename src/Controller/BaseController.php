<?php namespace CCTM\Controller;

use Pimple\Container;

class BaseController {

    protected $dic;

    public function __constuct(Container $dic)
    {
        $this->dic = $dic;
    }

    /**
     * @param $out
     *
     * @return mixed
     */
    public function render($out)
    {
        call_user_func($this->dic['ajax_printer'], $out);
        return $out;
    }


    public function getItem($id)
    {

    }

    public function getCollection()
    {

    }

    public function deleteItem($id)
    {

    }

    public function createItem()
    {

    }

    public function updateItem($id)
    {

    }

    /**
     * Indempotent: this replaces the item
     *
     * @param $id
     */
    public function overwriteItem($id)
    {

    }

}
/*EOF*/