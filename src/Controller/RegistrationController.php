<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Context\Context;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Entity\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationController extends AbstractFOSRestController
{   
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */

    private $passwordEncoder;

    /**
     * @var EntityManagerInterface
     */

     private $entityManager;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager) 
    {

        $this->userRepository  = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager   = $entityManager;
    }


    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function register(Request $request)
    {
       
        $email = $request->get('email');
        $password = $request->get('password');
        
        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        if (!is_null($user)) {

            return $this->view([
                'message' => 'User already exist'
            ], Response::HTTP_CONFLICT);
        }

        $user = new User();


        $user->setEmail($email);
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $password)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->view([
            $user
        ], Response::HTTP_CREATED)->setContext((new Context())->setGroups(['public']) );

    }
}
