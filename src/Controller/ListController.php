<?php

namespace App\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Task;
use App\Entity\TaskList;
use App\Repository\TaskListRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;





class ListController extends AbstractFOSRestController
{

    /**
     * @var TaskListRepository
     */
    private $taskListRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    public function __construct(TaskListRepository $taskListRepository,EntityManagerInterface $entityManager, TaskRepository $taskRepository){
        $this->taskListRepository = $taskListRepository;
        $this->entityManager      = $entityManager;
        $this->taskRepository     = $taskRepository;
    }


    /**
     * @Rest\Get("/lists")
     *
     * @return \FOS\RestBundle\View\View
     * 
     */
    public function getLists()
    {
        $data = $this->taskListRepository->findAll();

        return $this->view($data, Response::HTTP_OK);       
    }

    /**
     * @Rest\Get("/lists/{id}")
     * @return \FOS\RestBundle\View\View
     * @param int $id
     */
    public function getList(int $id)
    {
       

        $data =  $this->taskListRepository->find($id);
        
        return $this->view($data, Response::HTTP_OK);

    }


    /**
     * @Rest\Post("/lists")
     * @Rest\RequestParam(name="title", description="Title of the list", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return \FOS\RestBundle\View\View
     */
    public function createLists(ParamFetcher $paramFetcher)
    {
        $title = $paramFetcher->get('title');
        if ($title) {

            $list = new TaskList();

            $list->setTitle($title);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            return $this->view($list, Response::HTTP_CREATED);
        }

        return $this->view(['title' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }


    /**
     * @Rest\Post("/lists/{id}/tasks")
     * @Rest\RequestParam(name="title", description="Title of the new task", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */

    public function addTaskToList(int $id,ParamFetcher $paramFetcher) {
        
        $title = $paramFetcher->get('title');

        if ($title) {
            $list = $this->taskListRepository->find($id);

            if ($list) {
                $task = new Task();
                $task->setTitle($title);
                $task->setList($list);
                
                $list->addTask($task);

                $this->entityManager->persist($task);
                $this->entityManager->flush();

                return $this->view($task, Response::HTTP_OK); 
            }

            return $this->view(['message' => 'List not found'], Response::HTTP_BAD_REQUEST);

        }
        return $this->view(['message' => 'Title cannot be null'], Response::HTTP_BAD_REQUEST);
        
        
    }

    



    /**
     * @Rest\Get("/lists/{id}/tasks")
     * @return \FOS\RestBundle\View\View
     */
    public function getListTasks(int $id)
    {   

        $list = $this->taskListRepository->find($id);
        $data = $list->getTasks();
        return $this->view($data, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/lists/{id}/background")
     * @Rest\FileParam(name="image", description="The background of the list", nullable=false, image=true)
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function backgroundLists(Request $request, ParamFetcher $paramFetcher, int $id) {

        $list = $this->taskListRepository->find($id);
        $currentBackground = $list->getBackground();

        if (!is_null($currentBackground)) {
            $filesystem = new FileSystem();
            $filesystem->remove(
                $this->getUploadsDir() . $currentBackground
            );
        }
        
        /**@var UploadedFile $file */
        $file = $paramFetcher->get('image');
        
        if ($file) {
            $filename = md5(uniqid()) . '.' .$file->guessClientExtension();
            $file->move(
                $this->getUploadsDir(),
                $filename
            );

            $list->setBackground($filename);
            $list->setBackgroundPath('/uploads/' . $filename);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            $data = $request->getUriForPath(
                $list->getBackgroundPath()
            );

            return $this->view($data, Response::HTTP_OK);
        }
        
        
        return $this->view(['message' => 'Something went wrong'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("lists/{id}")
     */
    public function removeList(int $id) {

        $list = $this->taskListRepository->find($id);
        $this->entityManager->remove($list);
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Patch("lists/{id}/title")
     * @Rest\RequestParam(name="title", description="The new title for the list", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function updateListTitle(ParamFetcher $paramFetcher, int $id){
        
        $errors = [];

        $list = $this->taskListRepository->find($id);
        $title = $paramFetcher->get('title');

        if (trim($title) !== '') {
            if ($title) {
                $list->setTitle($title);

                $this->entityManager->persist($list);
                $this->entityManager->flush();

                return $this->view(null, Response::HTTP_NO_CONTENT);
            }
            $errors = [
                'title' => 'This value cant be empty'
            ];
        }

        $errors = [
            'title' => 'List not found'
        ];

        return $this->view($errors, Response::HTTP_NO_CONTENT);

    }

    private function getUploadsDir(){

        return $this->getParameter('uploads_dir');
    }

}

