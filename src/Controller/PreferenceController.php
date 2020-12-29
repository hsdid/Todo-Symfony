<?php

namespace App\Controller;

use App\Entity\TaskList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TaskListRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;

class PreferenceController extends AbstractFOSRestController
{
    
    /**
     * @var TaskListRepository
     */
    private $taskListRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    

    public function __construct(TaskListRepository $taskListRepository,EntityManagerInterface $entityManager){
        $this->taskListRepository = $taskListRepository;
        $this->entityManager      = $entityManager;
        
    }
    
    
    /**
     * 
     * @Rest\Get("/preferences/{id}")
     * @return \FOS\RestBundle\View\View
     * @param int $id
     */
    public function getPreferences(int $id)
    {
        
        $list = $this->taskListRepository->find($id);

        $data = $list->getPreferences($list);
        
        return $this->view($data, Response::HTTP_OK);

    }

    /**
     * 
     * @Rest\Patch("/preferences/{id}/sort")
     * @Rest\RequestParam(name="sortValue", description="The value will be used to sort the list", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @param int $id
     * @return \FOS\RestBundle\View\View

     */
    public function sortPreferences(ParamFetcher $paramFetcher ,int $id)
    {
       
        $sortValue = $paramFetcher->get('sortValue');
        $list = $this->taskListRepository->find($id);

    
        if ($sortValue) {
            $list->getPreferences()->setSortValue($sortValue);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            return $this->view(null, Response::HTTP_NO_CONTENT);
        }
        
        $data['code']    = Response::HTTP_CONFLICT;
        $data['message'] = 'The sortValue cannot be null'; 

        return $this->view($data, Response::HTTP_CONFLICT);
    }

     /**
     * 
     * @Rest\Patch("/preferences/{id}/filter")
     * @Rest\RequestParam(name="filterValue", description="The filter value", nullable=false)
     * @param int $id
     * @param ParamFetcher $paramFetcher
     * @return \FOS\RestBundle\View\View
     */
    public function filterPreferences(ParamFetcher $paramFetcher, int $id)
    {
        
        $list = $this->taskListRepository->find($id);
        $filterValue = $paramFetcher->get('filterValue');

        if ($filterValue) {
            $list->getPreferences()->setFilterValue($filterValue);

            $this->entityManager->persist($list);
            $this->entityManager->flush();
            return $this->view(null, Response::HTTP_NO_CONTENT);
        }

        $data['code']    = Response::HTTP_CONFLICT;
        $data['message'] = 'The filterValue cannot be null'; 

        return $this->view($data, Response::HTTP_CONFLICT);
        
        

    }


}
