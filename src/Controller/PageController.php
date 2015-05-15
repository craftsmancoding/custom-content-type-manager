<?php namespace CCTM\Controller;
/**
 * Class PageController
 *
 * For returning HTML views with text/html mime type, but unfortunately WordPress often needs the views to be PRINTED
 * (not just returned), so for saner testing, we pass in a callable parameter where we can inject a printing function.
 *
 * See http://asika.windspeaker.co/post/3975-use-blade-template-engine-outside-laravel
 *
*@package CCTM
 */

//use Windwalker\Renderer\BladeRenderer;
// use Pimple\Container;
use CCTM\Exceptions\NotAllowedException;

class PageController extends BaseController{

    public function render($out)
    {
        call_user_func($this->dic['printer'], $out);
        return $out;
    }

    public function getItem($id)
    {
        return $this->render( $this->dic['BladeRenderer']->render($id));
    }

    public function getCollection()
    {
        throw new NotAllowedException('Action not allowed.');
    }

    public function deleteItem($id)
    {
        throw new NotAllowedException('Action not allowed.');
    }

    public function createItem()
    {
        throw new NotAllowedException('Action not allowed.');
    }

    public function updateItem($id)
    {
        throw new NotAllowedException('Action not allowed.');
    }
}
/*EOF*/