<?php

namespace AppBundle\JWT;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\DefaultEncoder;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class Encoder extends DefaultEncoder implements JWTEncoderInterface 
{
    /**
     * @return bool|resource
     */
    protected function getPrivateKey()
    {
        return openssl_pkey_get_private(base64_decode($this->privateKey), $this->passPhrase);
    }

    /**
     * @return resource
     */
    protected function getPublicKey()
    {
        return openssl_pkey_get_public(base64_decode($this->publicKey));
    }
}
