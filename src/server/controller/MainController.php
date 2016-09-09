<?php

require_once "JOSE/autoloader.php";

define('ENC_ALG' , 'RS256');

/**
 * Main controller extended by all other controllers
 */
class MainController
{
    protected $jws = '';
    protected $serverFolder = '';

    public function __construct() {
       // $jws = \Namshi\JOSE\SimpleJWS::load($token);
        //$this->token = $this->getFormattedToken();
        //var_dump(apache_request_headers());die;
    }

    /**
    * Manages all the POST requests
    *
    * @param Request $request - Request object passed as parameter
    * @return Array $data - response data
    */
    public function postAction(Request $request)
    {
        $data = $request->parameters;
        $data['message'] = 'POST requests are not available';

        return $data;
    }

    protected function getFormattedToken() {
        $headers = apache_request_headers();
        $authorizationHeader = isset($headers['authorization']) ? $headers['authorization'] : $headers['Authorization'];

        return str_replace('Bearer ', '', $authorizationHeader);
    }

    protected function isValidToken($token) {
        $this->jws = \Namshi\JOSE\SimpleJWS::load($token);
        $publicKey = $this->getPublicKey();

        return $this->jws->isValid($publicKey, ENC_ALG);
    }

    protected function getPublicKey() {
        $this->serverFolder = dirname(dirname(__FILE__));

        return openssl_pkey_get_public('file://' . $this->serverFolder . '/key/public.pem');
    }

    protected function getUserIdFromPayload($token) {
        if ($this->isValidToken($token)) {
            $payload = $this->jws->getPayload();

            return $payload['uid'];
        } else {
            return -1;
        }
    }

    protected function getFileSizeBytes($fileSize)
    {
        $val = trim($fileSize);
        $last = strtolower($val[strlen($val)-1]);

        switch($last) {
            case 'g':
                $val *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $val *= 1024 * 1024;
                break;
            case 'k':
                $val *= 1024;
                break;
            default:
                break;
        }

        return $val;
    }

    protected function getPrivateKey()
    {
        $this->serverFolder = dirname(dirname(__FILE__));

        return openssl_pkey_get_private('file://' . $this->serverFolder . '/key/private.pem', 'pass');
    }

    protected function getUserAuthToken($userId)
    {
        if ($this->isValidId($userId)) {
            $jws  = new \Namshi\JOSE\SimpleJWS(array(
                'alg' => ENC_ALG
            ));

            $jws->setPayload(array(
                'uid' => $userId,
            ));

            $privateKey = $this->getPrivateKey();

            $jws->sign($privateKey);

            return array('response' => true, 'token' => $jws->getTokenString());
        }

        return array('response' => false);
    }

    protected function isValidId($id)
    {
        return is_numeric($id) && ($id > 0);
    }
}