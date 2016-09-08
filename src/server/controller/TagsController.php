<?php

/**
 * TagsController for handling api/tags
 */
class TagsController extends MainController
{

    private $response = '';
    private $tag = null;

    public function __construct()
    {
        $this->tag = new TagsModel();
    }

    /**
     * Manages all GET requests to api/tags
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function getAction(Request $request)
    {
        $this->response = $this->tag->getTags();

        return $this->response;
    }

    /**
     * Manages all POST requests to api/tags
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function postAction(Request $request)
    {

    }
}