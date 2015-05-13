<?php namespace CCTM;
/**
 * Class AdminController
 *
 * See http://asika.windspeaker.co/post/3975-use-blade-template-engine-outside-laravel
 * @package CCTM
 */


class AdminController {

    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function getIndex()
    {

        $data = array('foo' => 'bar');
        print $this->view->render('index', $data);
    }
}
/*EOF*/