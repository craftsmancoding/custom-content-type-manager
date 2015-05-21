<?php namespace CCTM\Controller;

use CCTM\Interfaces\ResourceInterface;
use Pimple\Container;

abstract class ResourceController {

    protected $dic;
    protected $resource;
    protected $render_callback;
    protected $response_code; // e.g. 200, 404, etc

    public function __construct(Container $dic, ResourceInterface $resource, callable $render_callback)
    {
        $this->dic = $dic;
        $this->resource = $resource;
        $this->render_callback = $render_callback;
    }

    /**
     * @param $out
     *
     * @return mixed
     */
    public function render($out)
    {
        call_user_func($this->render_callback, $this->getResponseCode());
        // call_user_func($this->render_callback, $out);
        return $out;
    }

    /**
     * @param int $code
     */
    public function setResponseCode($code)
    {
        $this->response_code = (int) $code;
    }

    /**
     * @return int $code
     */
    public function getResponseCode()
    {
        return $this->response_code;
    }

    public function getResource($id)
    {
        return $this->resource->getOne($id);
    }

    public function getCollection()
    {
        $filters = array(); // todo
        return $this->resource->getCollection($filters);
    }

    public function deleteResource($id)
    {

    }

    public function createResource()
    {

    }

    public function updateResource($id)
    {

    }

    /**
     * Indempotent: this creates/updates the item with the payload
     *
     * @param $id
     */
    public function overwriteResource($id)
    {

    }

}
/*EOF*/