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
        //  api/users/6/unfollows
        if (isset($request->urlElements[4]) && is_numeric($request->urlElements[4])
            && isset($request->urlElements[5]) && $request->urlElements[5] == 'unfollows'
        ) {
            $userId = $request->urlElements[4];

            $this->response = $this->user->getUnfollowUsers($userId);
        } elseif (isset($request->urlElements[4]) && $request->urlElements[4] == 'login'
            && isset($request->urlElements[5]) && ctype_alnum($request->urlElements[5])) {   //  api/users/login/{user_login}
            $userLogin = $request->urlElements[5];

            $this->response = $this->user->getUserByLogin($userLogin);
        } elseif (isset($request->urlElements[4]) && $request->urlElements[4] == 'id'
            && isset($request->urlElements[5]) && ctype_alnum($request->urlElements[5])) {   //  api/users/login/{user_login}
            $userId = $request->urlElements[5];

            $this->response = $this->user->getUserById($userId);
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
        //  api/users/sessions
        if (isset($request->urlElements[4]) && $request->urlElements[4] == 'sessions') {
            $email = $request->parameters['email'];
            $password = md5($request->parameters['password']);

            $userId = $this->user->getUserIdByEmailPassword($email, $password);

            $token = $this->getUserAuthToken($userId);

            $this->response = array('id_token' => $token);
        } elseif (isset($request->urlElements[4]) && $request->urlElements[4] == 'follows') {       //  api/users/follows
            $friendId = $request->parameters['friendId'];

            $this->response = $this->user->createFriend($friendId, $this->currentUserId);
        }
        else {    //  api/users
          /*  $email = $request->parameters['email'];
            $password = md5($request->parameters['password']);

            $this->response = $this->user->createUser($email, $password);

            $this->response = ($response) ? array('response' => $response, 'id_token' => $password) : array('response' => $response);*/
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
        //  api/users/follows/{user_id}
        if (isset($request->urlElements[4]) && $request->urlElements[4] == 'follows'
            && isset($request->urlElements[5]) && is_numeric($request->urlElements[5])
        ) {
            $friendId = $request->urlElements[5];

            $this->response = $this->user->deleteFriend($this->currentUserId, $friendId);
        } else {

        }

        return $this->response;
    }

    /**
     * Manages all PUT requests to api/users
     *
     * @param Request $request - Request object passed as parameter
     * @return Object (or Array) $data - response data
     */
    public function putAction(Request $request)
    {
        //  api/users/(user_id)
        if (isset($request->urlElements[4]) && is_numeric($request->urlElements[4])) {
            $userId = $request->urlElements[4];
            $login = $request->parameters['login'];
            $email = $request->parameters['email'];
            $info = $request->parameters['info'];

            if ($userId == $this->currentUserId) {
                $this->response = $this->user->updateUserInfo($this->currentUserId, $login, $email, $info);
            } else {

            }
        } else {

        }

        return $this->response;
    }
}