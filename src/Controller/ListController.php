<?php

namespace App\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class ListController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/lists")
     * 
     */
    public function getListsAction()
    {
        
    }

    /**
     * @Rest\Get("/lists/{$id}")
     * 
     */
    public function getListAction(int $id)
    {
        
    }

    /**
     * @Rest\Post("/lists")
     * 
     */

    public function postListsAction()
    {
        


    }

    /**
     * @Rest\Put("/lists")
     * 
     */

    public function putListsAction()
    {
        
        

    }

     /**
     * @Rest\Patch("/lists/{id}")
     * 
     */

    public function patchListsAction(int $id)
    {
        
        

    }

    /**
     * @Rest\Get("/lists/{id}/tasks")
     */
    public function getListTasksAction(int $id)
    {
        return $id;
    }
}

