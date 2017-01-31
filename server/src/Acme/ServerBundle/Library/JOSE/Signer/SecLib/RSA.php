<?php

namespace Namshi\JOSE\Signer\SecLib;

//use phpseclib\Crypt\RSA as CryptRSA;
use Namshi\JOSE\Signer\SecLib\RSA as CryptRSA;

class RSA extends PublicKey
{
    public function __construct()
    {
        $this->encryptionAlgorithm = new CryptRSA();
    }
}
