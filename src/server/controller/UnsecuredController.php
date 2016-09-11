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
        //  api/unsecured/users/email/{email}/password/{password}
        if (isset($request->urlElements[4]) && $request->urlElements[4] == 'users'
            && isset($request->urlElements[5]) && $request->urlElements[5] == 'email'
            && isset($request->urlElements[6]) && ($request->urlElements[6]) != ''
            && isset($request->urlElements[7]) && $request->urlElements[7] == 'password'
            && isset($request->urlElements[8]) && ctype_alnum($request->urlElements[8])
        ) {
            $email = $request->urlElements[6];
            $password = $request->urlElements[8];

            $userId = $this->user->getUserIdByEmailPassword($email, $password);

            $this->response = $this->getUserAuthToken($userId);
        } else {
            header('HTTP/1.1 405 Method not allowed');
            return;
        }

        return $this->response;
    }

    /**
     * Manages all POST requests to api/unsecured
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function postAction(Request $request)
    {
        if (isset($request->urlElements[4]) && $request->urlElements[4] == 'users') {       //  api/unsecured/users
            $email = $request->parameters['email'];
            $password = md5($request->parameters['password']);
            $login = $request->parameters['login'];

            if (!$email || !$password || !$login) {
                header('HTTP/1.1 400 You must send the email and the password and the login');
                return;
            }

            $response = $this->user->createUser($email, $password, $login);

            $this->response = array('response' => $response);
        }

        return $this->response;
    }
}