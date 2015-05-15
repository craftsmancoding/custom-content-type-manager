<?php namespace CCTM\Controller;
    /**
     * Class ErrorController
     *
     *
     *@package CCTM
     */


use Pimple\Container;

class ErrorController {

    private $dic;

    public function __construct(Container $dic)
    {
        $this->dic = $dic;
    }

    public function render($tpl, array $data = array())
    {
        $out = $this->dic['BladeRenderer']->render($tpl, $data);

        call_user_func($this->dic['printer'], $out);
        return $out;
    }

    public function getIndex()
    {
        $data = array('foo' => 'bar');
        //print $this->dic['BladeRenderer']->render('index', $data);
        return $this->render('index', $data);
        //return $this->render($this->dic['BladeRenderer']->render('index', $data));
    }
}
/*EOF*/