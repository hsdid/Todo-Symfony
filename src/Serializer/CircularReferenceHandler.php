<?php

namespace App\Serializer;

use App\Entity\TaskList;
use App\Entity\Task;
use App\Entity\Note;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CircularReferenceHandler
{   

    // /**
    //  * @var RouterInterface
    //  */
    // private $router;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct( UrlGeneratorInterface $router) {

        $this->router = $router;
    }

    public function __invoke($object) {
        
        
        switch ($object) {

            case $object instanceof TaskList:
                return $this->router->generate('app_list_getlist', ['id' => $object->getId()]);
            case $object instanceof Task;
                return $this->router->generate('app_task_gettask', ['id' => $object->getId()]);
            case $object instanceof Note;
                return $this->router->generate('app_note_getnote', ['id' => $object->getId()]);

        }

        return $object->getId();
    }


}