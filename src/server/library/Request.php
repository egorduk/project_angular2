<?php

/**
 * Object Request that stores the user request
 * and all the related information useful to return
 * a proper response
 */
class Request
{
    /**
     * Flag is the request is valid or not
     *
     * @var boolean
     **/
    public $valid = false;

    /**
     * Elements of the request (of the URL)
     *
     * @var array
     **/
	public $urlElements = array();

    /**
     * Version of the APIs (not currently used)
     *
     * @var string
     **/
	public $ver;

    /**
     * Array of optional parameters sent with the request
     *
     * @var array
     **/
	public $parameters = array();

    /**
    * Get all information about the request sent
    *
    * @return boolean - response data
    */
	public function __construct()
	{
        if (isset($_SERVER['REQUEST_URI']) && trim($_SERVER['REQUEST_URI']) != '/') {
            $this->valid = true;

            $this->action = htmlentities($_SERVER['REQUEST_METHOD']);
            $this->urlElements = explode('/', $_SERVER['REQUEST_URI']);

            // call the method to parse all input params
            $this->parseIncomingParams();

            // initialise json as default format
            $this->format = 'json';

            // set the new format based on the input request
            if (isset($this->parameters['format'])) {
                $this->format = $this->parameters['format'];
            }

            return true;
        }

        return false;
	}

    /**
    * Parse requests
    */
	public function parseIncomingParams()
	{
		$parameters = array();

		// Pull GET variables
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $parameters);
        }

        // Pull PUT/POST bodies from input request
        $body = file_get_contents("php://input");

        // Get the requested content type
        $contentType = false;

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $contentType = $_SERVER['CONTENT_TYPE'];
        }

        switch ($contentType) {
        	case 'application/json':
        		$bodyParams = json_decode($body);

        		if ($bodyParams) {
        			foreach ($bodyParams as $paramName => $paramValue) {
        				//$parameters[$paramName] = $parameters[$paramValue];
        				$parameters[$paramName] = $paramValue;
        			}
        		}

        		$this->format = 'json';
                
        		break;
        	case 'application/x-www-form-urlencoded':
        		parse_str($body, $postVars);

        		foreach ($postVars as $field => $value) {
        			$parameters[$field] = $value;
        		}

        		$this->format = 'html';

        		break;
        	default:
        		break;
        }

        $this->parameters = $parameters;
	}
}
