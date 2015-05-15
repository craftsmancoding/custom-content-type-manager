<?php namespace CCTM\Controller;

use Pimple\Container;

class AjaxController {

    private $dic;

    public function __constuct(Container $dic)
    {
        $this->dic = $dic;
    }

    public function getIndex()
    {
        print 'AHASDFASDF';
        wp_die();
        print json_encode(array('hash'=>'cat'));
        wp_die();
    }
}
/*EOF*/