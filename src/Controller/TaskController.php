<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskList;
use App\Entity\Note;

use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;



class TaskController extends AbstractFOSRestController
{
    


    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository){
        
        $this->entityManager      = $entityManager;
        $this->taskRepository     = $taskRepository;
    }


    /**
     * @Rest\Delete("/tasks/{id}")
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */

    public function removeTask(int $id) {
        
              
        $task = $this->taskRepository->find($id);

        if ($task) {

            $this->entityManager->remove($task);
            $this->entityManager->flush();

            return $this->view(null, Response::HTTP_NO_CONTENT); 
        }

        return $this->view(['message' => 'Task not found'], Response::HTTP_BAD_REQUEST);
    
    }

    /**
     * @Rest\Get("/tasks/{id}/notes")
     * @param int $id
     */
    public function getTaskNotes(int $id) {
        
        $task = $this->taskRepository->find($id);

        if ($task) {

            $data = $task->getNotes();

            return $this->view($data, Response::HTTP_OK);   
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);

    }


    /**
     * @Rest\Patch("/tasks/{id}/status")
     *  @param int $id
     *  @return \FOS\RestBundle\View\View
     */
    public function statusTask(int $id) {

        $task = $this->taskRepository->find($id);

        if ($task) {

            $task->setIsComplete(!$task->getIsComplete());
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->view($task->getIsComplete(), Response::HTTP_NO_CONTENT);   
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @Rest\Post("/tasks/{id}/notes")
     * @Rest\RequestParam(name="note", description="Content of the new note", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function addNoteToTask(int $id) {

        $noteText = $paramFetcher->get('note');
        $task = $this->taskRepository->find($id);

        if ($task) {
            
            if ($noteText) {

                $note = new Note();
                $note->setNote($noteText);
                $note->setTask($task);

                $task->addNote($note);

                $this->entityManager->persist($note);
                $this->entityManager->flush();

                return $this->view($note, Response::HTTP_OK);
            }
            return $this->view(['message' => 'Note cannot be empty'], Response::HTTP_BAD_REQUEST);
           
        }
        return $this->view(['message' => 'Something went wrong'], Response::HTTP_BAD_REQUEST);

    }
    

}
