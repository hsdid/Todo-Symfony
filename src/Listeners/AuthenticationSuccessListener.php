<?php

namespace App\Listeners;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class AuthenticationSuccessListener{

    private $secure = false;
    private $tokenTTL;

    public function __construct($tokenTTL){
        $this->tokenTTL = $tokenTTL;
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event){
        $response = $event->getResponse();
        $data     = $event->getData();


        $token = $data['token'];
        unset($data['token']);
        unset($data['refresh_token']);
        $event->setData($data);

        $response->headers->setCookie(
            new Cookie('BEARER' , $token,
            (new \DateTime())
                ->add(new \DateInterval('PT' . $this->tokenTTL . 'S'))
            ), '/', null, $this->secure  
        );

        return $response;
    }
}