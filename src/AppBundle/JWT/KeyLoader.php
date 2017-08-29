<?php

namespace AppBundle\JWT;

use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\KeyLoaderInterface;

class KeyLoader implements KeyLoaderInterface
{
    const TYPE_PUBLIC = 'public';
    const TYPE_PRIVATE = 'private';

    private $privateKey;

    private $publicKey;

    private $passphrase;

    public function __construct($privateKey, $publicKey, $passphrase)
    {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->passphrase = $passphrase;
    }

    /**
     * Loads a key from a given type (public or private).
     *
     * @param resource|string
     *
     * @return resource|string
     */
    public function loadKey($type)
    {
        if (!in_array($type, [self::TYPE_PUBLIC, self::TYPE_PRIVATE])) {
            throw new \InvalidArgumentException(
                sprintf('The key type must be "public" or "private", "%s" given.', $type)
            );
        }

        $key = null;

        if (self::TYPE_PRIVATE === $type) {
            return $this->getPrivateKey();
        }

        return $this->getPublicKey();
    }

    /**
     * @return string
     */
    public function getPassphrase()
    {
        return $this->passphrase;
    }

    private function getPrivateKey()
    {
        return openssl_pkey_get_private(base64_decode($this->privateKey), $this->passphrase);
    }

    private function getPublicKey()
    {
        return openssl_pkey_get_public(base64_decode($this->publicKey));
    }
}
