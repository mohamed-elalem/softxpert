<?php

namespace TaskTrackBundle\Encoder;

use JWT\Authentication\JWT;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

/**
 * NixillaJWTEncoder
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class NixillaJWTEncoder implements JWTEncoderInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * __construct
     */
    public function __construct($key = 'pLQ!D)wHbFGQTf~vox{2z1|5:p,yfY6`WO!&TcYV<B8hKu;icy5"HOu:+_;KQ_1')
    {
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function encode(array $data)
    {
        return JWT::encode($data, $this->key);
    }

    /**
     * {@inheritdoc}
     */
    public function decode($token)
    {
        try {
            return (array) JWT::decode($token, $this->key);
        } catch (\Exception $e) {
            return false;
        }
    }
}