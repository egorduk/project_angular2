<?php

/**
 * UnsecuredController for handling none-secured requests
 */
class UnsecuredController extends MainController
{

    private $response = '';
    private $user = null;

    public function __construct()
    {
        parent::__construct();

        $this->user = new UsersModel();
    }

    /**
     * Manages all GET requests to api/unsecured
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function getAction(Request $request)
    {
    }

    /**
     * Manages all POST requests to api/unsecured
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function postAction(Request $request)
    {
        if (isset($request->urlElements[4]) && $request->urlElements[4] == 'users') {
            $email = $request->parameters['email'];
            $password = md5($request->parameters['password']);

            $response = $this->user->createUser($email, $password);

            $this->response = ($response) ? array('response' => $response, 'id_token' => $password) : array('response' => $response);

            return $this->response;
        }
    }
}