<?php namespace CCTM\Controller;

use CCTM\Interfaces\ResourceInterface;
use Pimple\Container;

/**
 * Class ResourceController
 *
 * --- Response Codes ---
 *
 * GETTING RESOURCES (or COLLECTIONS)
 * 200 OK - A server MUST respond to a successful request to fetch an individual resource or resource collection with a 200 OK response.
 * A server MUST respond to a successful request to fetch a resource collection with an array of resource objects or an empty array ([]) as the response document's primary data.
 *
 *
 *
 * CREATING RESOURCES:
 *
 * 201 Created - if the request did not include a client generated ID.
 *  - should include a "Location" directive including a link to the newly created resource.
 *  - The response must include the resource created.
 * 202 Accepted - if the server has queued the request for processing
 * 204 No Content - used if the request specified a client generated ID
 *  - does not return a response document.
 *
 * UPDATING RESOURCES:
 *
 * 202 Accepted - if the server has queued the request for processing.
 * 200 OK - The response document MUST include a representation of the updated resource(s) as if a GET request was made to the request URL.
 * 204 No Content - If an update is successful and the server doesn't update any attributes besides those provided (i.e. a PATCH), the server MUST return either a 200 OK status code and response document (as described above) or a 204 No Content status code with no response document.
 *
 * DELETING RESOURCES
 * 202 Accepted - If a deletion request has been accepted for processing, but the processing has not been completed by the time the server responds, the server MUST return a 202 Accepted status code.
 * 204 No Content - if a deletion request is successful and no content is returned.
 * 200 OK - A server MUST return a 200 OK status code if a deletion request is successful and the server responds with only top-level "meta" data.
 *
 * For filters, see http://jsonapi.org/examples/ and http://jsonapi.org/recommendations/
 * e.g. GET /comments?filter[post]=1,2&filter[author]=12
 *
 * @package CCTM\Controller
 */

abstract class ResourceController {

    protected $dic;
    protected $resource;
    protected $render_callback;

    public function __construct(Container $dic, ResourceInterface $resource, callable $render_callback)
    {
        $this->dic = $dic;
        // TODO: filter out "private" POST vars?
        $this->resource = $resource;
        $this->render_callback = $render_callback;
    }

    /**
     * @param $out
     * @param $headers array
     * @param $http_status_code integer 200
     * @return mixed
     */
    public function render($out, array $headers=array('Content-Type: application/vnd.api+json'), $http_status_code=200)
    {

        return call_user_func($this->render_callback, $out, $headers, $http_status_code);

    }


    public function getResource($id)
    {
        $resource = $this->resource->getOne($id);
        $out = $this->dic['JsonApiEncoder']->encode($resource);
        return $this->render($out);
    }

    public function getCollection()
    {
        $filters = array(); // todo
        $collection = $this->resource->getCollection($filters);
        $out = $this->dic['JsonApiEncoder']->encode($collection);
        return $this->render($out);
    }

    public function deleteResource($id)
    {
        $this->resource->getOne($id)->delete();
        return $this->render('',array(),204);
    }

    /**
     * Should respond with
     *  - 201 Created and the full object
     *  - 202 Accepted if the action is queued for later processing
     *  - 204 No Content is acceptable if the request included a client generated ID
     * See http://jsonapi.org/format/#crud
     */
    public function createResource()
    {
        $this->resource->fromArray($this->dic['POST']['data']['attributes']);

        // Patch the id over as an attribute
        if (isset($this->dic['POST']['data']['id']))
        {
            $this->resource->set('id', $this->dic['POST']['data']['id']);
        }

        if ($this->resource->getId())
        {
            $this->resource->save();
            return $this->render('',array(),204);
        }
        else
        {
            $out = $this->dic['JsonApiEncoder']->encode($this->resource);
            return $this->render($out,array('Content-Type: application/vnd.api+json'),201);
        }
    }

    /**
     * Unclear from JSON API spec (?) is behavior of the POST update...
     * POST is not indempotent (whereas PUT is).
     * Here I'm interpreting a POST to a specific resource ($id is set) as
     * a PUT: all resource attributes should be included in the request. Any
     * that are omitted will be eliminated from the resource or set to null.
     * A full response body is returned.
     *
     * @param $id
     *
     * @return string
     */
    public function updateResource($id)
    {
        $resource = $this->resource->getOne($id);
        $resource->fromArray($this->dic['POST']['data']['attributes']);
        $resource->save();
        $out = $this->dic['JsonApiEncoder']->encode($this->resource);
        return $this->render($out);
    }

    /**
     * From the JSON-API Spec: "the server MUST interpret the missing fields as if they
     * were included with their current values. It MUST NOT interpret them as null values."
     *
     * @param $id
     *
     * @return string
     */
    public function patchResource($id)
    {
        $resource = $this->resource->getOne($id);
        foreach ($this->dic['POST']['data']['attributes'] as $key => $val)
        {
            $resource->set($key, $val);
        }
        $this->resource->save();
        return $this->render('',array(),204);
    }

}
/*EOF*/