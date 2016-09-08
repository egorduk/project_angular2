<?php

/**
 * UsersController for handling api/users
 */
class UsersController extends MainController
{

    private $response = '';
    private $user = null;
    private $currentUserId = 0;
    private $token = '';

    public function __construct()
    {
        $this->user = new UsersModel();
        $this->token = $this->getFormattedToken();
        $this->currentUserId = $this->getUserIdFromPayload($this->token);
    }

    /**
     * Manages all GET requests to api/users
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function getAction(Request $request)
    {
        if (isset($request->urlElements[4]) && ctype_alnum($request->urlElements[4])) {
            $this->response = $this->user->getUserByLogin($request->urlElements[4]);
        } else {

        }

        return $this->response;
    }

    /**
     * Manages all POST requests to api/users
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function postAction(Request $request)
    {
        if (isset($request->urlElements[4]) && $request->urlElements[4] == 'sessions') {
            $email = $request->parameters['email'];
            $password = md5($request->parameters['password']);

            $userId = $this->user->getUserByEmailPassword($email, $password);

            $token = $this->getUserAuthToken($userId);

            $this->response = array('id_token' => $token);
        } elseif (isset($request->urlElements[4]) && $request->urlElements[4] == 'follows') {
            $friendId = $request->parameters['id'];

            $this->response = $this->user->createFriend($friendId, $this->currentUserId);
        } else {
            $email = $request->parameters['email'];
            $password = md5($request->parameters['password']);

            $response = $this->user->createUser($email, $password);

            $this->response = ($response) ? array('response' => $response, 'id_token' => $password) : array('response' => $response);
        }

        return $this->response;
    }

    /**
     * Manages all DELETE requests to api/users
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function deleteAction(Request $request)
    {
        if (isset($request->urlElements[4]) && $request->urlElements[4] == 'follows'
            && isset($request->urlElements[5]) && is_numeric($request->urlElements[5])
        ) {
            $friendId = $request->urlElements[5];

            $this->user->deleteFriend($this->currentUserId, $friendId);
        }
    }
}