<?php

namespace Acme\ServerBundle\Helper;

use Doctrine\Common\Persistence\ObjectManager;
use Namshi\JOSE\SimpleJWS;

class AuthHelper
{
    const ENCODE_ALGORITHM = 'RS256';

    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @param int $userId
     *
     * @return string
     */
    public function getAuthToken($userId)
    {
        $jws = new SimpleJWS(
            [
                'alg' => self::ENCODE_ALGORITHM,
            ]
        );

        $jws->setPayload(
            [
                'uid' => $userId,
            ]
        );

        $privateKey = $this->getPrivateKey();

        $jws->sign($privateKey);

        return $jws->getTokenString();
    }

    private function getPrivateKey()
    {
        $serverFolder = dirname(dirname(__FILE__));

        return openssl_pkey_get_private('file://' . $serverFolder . '/key/private.pem', 'pass');
    }

    private function getPublicKey()
    {
        $serverFolder = dirname(dirname(__FILE__));

        return openssl_pkey_get_public('file://' . $serverFolder . '/key/public.pem');
    }
}
