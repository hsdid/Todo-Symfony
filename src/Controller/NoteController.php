<?php

namespace App\Controller;

use App\Entity\Task;

use App\Entity\Note;
use App\Repository\NoteRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;

class NoteController extends AbstractFOSRestController
{

    public function __construct(EntityManagerInterface $entityManager, NoteRepository $noteRepository){
        
        $this->entityManager      = $entityManager;
        $this->noteRepository     = $noteRepository;
    }


   /**
     * @Rest\Delete("/notes/{id}")
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */

    public function removeNote(int $id) {
        
              
        $note = $this->noteRepository->find($id);

        if ($note) {

            $this->entityManager->remove($note);
            $this->entityManager->flush();

            return $this->view(null, Response::HTTP_NO_CONTENT); 
        }

        return $this->view(['message' => 'Note not found'], Response::HTTP_BAD_REQUEST);
    
    }
}
