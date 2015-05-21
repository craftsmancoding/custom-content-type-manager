<?php namespace CCTM\Controller;
/**
 * Class PageController
 *
 * For returning HTML views with text/html mime type, but unfortunately WordPress often needs the views to be PRINTED
 * (not just returned), so for saner testing, we pass in a callable parameter where we can inject a printing function.
 *
 * See http://asika.windspeaker.co/post/3975-use-blade-template-engine-outside-laravel
 *
 * @package CCTM
 */

use CCTM\Exceptions\NotAllowedException;

//class PageController extends BaseController{
class PageController {

    // From BaseController
    public function __construct(Container $dic, callable $render_callback)
    {
        $this->dic = $dic;
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
        call_user_func($this->render_callback, $out);
        return $out;
    }



    /**
     * We need a function we can reference in callbacks without control over arguments passed in, so this is where we
     * hard-code a specific template.
     * @return mixed
     */
    public function getIndex()
    {
        return $this->getResource('index');
    }

    public function getResource($id)
    {
        $out = $this->dic['BladeRenderer']->render($id, array());
        return $this->render($out);
    }

//    public function getCollection()
//    {
//        throw new NotAllowedException('Action not allowed.');
//    }
//
//    public function deleteResource($id)
//    {
//        throw new NotAllowedException('Action not allowed.');
//    }
//
//    public function createResource()
//    {
//        throw new NotAllowedException('Action not allowed.');
//    }
//
//    public function updateResource($id)
//    {
//        throw new NotAllowedException('Action not allowed.');
//    }
}
/*EOF*/