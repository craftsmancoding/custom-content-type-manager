<?php namespace CCTM\Exceptions;

/**
 * Class CCTMException
 *
 * Extend the class so we can pad the exception with some more useful info
 * and keep our routing/trapping clean.  This helps us populate the JSON-API
 * Exception documents with the following info:
 *
 *  'id' : A unique identifier for this particular occurrence of the problem
 *  'href' : optional link to a wiki page explaining the error
 *  'status': HTTP status code
 *  'code': app specific code
 *  'title' Should not change per instance other than localization
 *  'detail': human-readable explanation;
 *
 * @package CCTM\Exceptions
 */

class CCTMException extends \Exception {

    protected $data;

    protected $id;
    protected $href;
    protected $status;
    protected $title;
    protected $detail;


    public function __construct($message='', $code=0, $data=array())
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->href = (isset($data['href'])) ? $data['href'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : 200;
        $this->title = (isset($data['title'])) ? $data['title'] : null;
        $this->detail = (isset($data['detail'])) ? $data['detail'] : null;

        parent::__construct($message, $code);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getHref()
    {
        return $this->href;
    }

    // Http status code
    public function getStatus()
    {
        return $this->status;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDetail()
    {
        return $this->detail;
    }

}