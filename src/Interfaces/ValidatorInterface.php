<?php namespace CCTM\Interfaces;
interface ValidatorInterface {

    public function validate(array $values,$context=null);
    public function getMessages();
}
/*EOF*/