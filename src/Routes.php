<?php namespace CCTM;

use CCTM\Exceptions\InvalidVerbException;
use CCTM\Exceptions\NotAllowedException;
use CCTM\Exceptions\NotFoundException;
use Pimple\Container;

/**
 * Class Routes
 *
 * Used to route requests internally for the CCTM admin pages and api.
 * Returns the output of a controller class.  The controller called depends on the inputs. So the behavior of this class is coupled with the injected input (usually $_POST and $_GET data), and the function names it maps to in the controllers implements the
 * ideas from the StormPath API and "best practices" in REST API design.
 *
 * Controller methods are:
 *
 *  getItem($id)        - GET id specified, read-only
 *  getCollection()     - GET without id, read-only
 *  deleteItem($id)     - DELETE
 *  createItem()        - POST
 *  updateItem($id)     - POST updates only those items passed in the request body
 *  overwriteItem($id)  - PUT (indempotent)
 *
 * Remember: PUT requests must be idempotent!!!
 *
 * @package CCTM
 */
class Routes {

    public static $verb = '_verb';
    public static $resource = '_resource';
    public static $id = '_id';

    public static $verbs = array('get','post', 'put', 'delete');

    private $dic;

    public function __construct(Container $dic)
    {
        $this->dic = $dic;
    }

    public function input($key, $default = null)
    {
        if(isset($this->dic['GET']) && isset($this->dic['GET'][$key]))
        {
            return $this->dic['GET'][$key];
        }
//        elseif(isset($this->dic['POST']) && isset($this->dic['POST'][$key]))
//        {
//            return $this->dic['POST'][$key];
//        }
        return $default;
    }

    public function getVerb()
    {
        $verb = strtolower($this->input(self::$verb, 'get'));

        if (!in_array($verb, self::$verbs))
        {
            throw new InvalidVerbException('Unsupported method: '.$verb);
        }
        return $verb;
    }

    /**
     * Basically, we gotta ensure the resource name is a valid classname.
     * @return string
     * @throws NotFoundException
     */
    public function getResourceName()
    {
        if (!$resource_name = $this->input(self::$resource))
        {
            throw new NotFoundException('Unspecified resource');
        }
        if (!is_scalar($resource_name))
        {
            throw new NotFoundException('Invalid data type');
        }
        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $resource_name))
        {
            throw new NotFoundException('Invalid resource name');
        }
        return ucfirst(strtolower($resource_name));
    }

    public function getControllerName($resource_name)
    {
        return ucfirst(strtolower($resource_name)).'Controller';
    }

    /**
     * Figure out which class method on the controller is correct given the resource and verb.
     *
     * @param       $verb
     * @param null  $id
     *
     * @return string
     * @throws NotAllowedException
     */
    public function getMethodName($verb, $id = null)
    {
        if ($verb == 'get')
        {
            return ($id) ? 'getItem' : 'getCollection';
        }
        elseif ($verb == 'post')
        {
            return ($id) ? 'updateItem' : 'createItem';
        }
        elseif ($verb == 'put')
        {
            if ($id)
            {
                return 'overwriteItem';
            }
            else
            {
                throw new NotAllowedException('Put not allowed without resource id.');
            };
        }
        elseif ($verb == 'delete')
        {
            if ($id)
            {
                return 'deleteItem';
            }
            else
            {
                throw new NotAllowedException('Resource id required. deleteCollection not allowed.');
            }
        }
    }

    /**
     * Ultimately, this is what issues a response.
     *
     * @return mixed
     * @throws InvalidVerbException
     * @throws NotAllowedException
     * @throws NotFoundException
     */
    public function handle()
    {
        $verb = $this->getVerb();
        $resource_name = $this->getResourceName();
        $controller_name = $this->getControllerName($resource_name);
        $id = $this->input(self::$id);
        $method_name = $this->getMethodName($verb,$id);

        return $this->dic[$controller_name]->$method_name($id);
    }

}
/*EOF*/