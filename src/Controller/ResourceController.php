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
        // TODO: filter out "private" POST vars?
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
        call_user_func($this->render_callback, $out, $this->getResponseCode());
        // call_user_func($this->render_callback, $out);
        return $out;
        
        $out = $this->dic['JsonApi']->encode($this->resource);
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
        return $this->resource->getOne($id)->delete();
    }

    public function createResource()
    {
        $this->resource->fromArray($this->dic['POST']);
    }

    /**

     * @param $id
     */
    public function updateResource($id)
    {

    }

    /**
     * From the JSON-API Spec: "the server MUST interpret the missing fields as if they
     * were included with their current values. It MUST NOT interpret them as null values."
     *
     * @param $id
     */
    public function patchResource($id)
    {

    }

}
/*EOF*/