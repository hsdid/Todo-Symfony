<?php

namespace App\Serializer\Normalizer;

use App\Entity\Note;
use App\Entity\Task;
use App\Entity\TaskList;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Asset\Packages;



class PublicDataNormalizzer implements  NormalizerInterface {


    /**
     * @var Package
     */

    private $packages; 

    /**
     * @var ObjectNormalizer
     */
    private $objectNormalizer;

    public function __construct(ObjectNormalizer $objectNormalizer, Packages $packages)
    {
        $this->packages = $packages;
        $this->objectNormalizer = $objectNormalizer;
    }

    
    public function normalize($object, $format = null, array $context = [])
    {
        $context['ignored_attributes'] = ['user'];

        $data = $this->objectNormalizer->normalize($object,$format,$context);
        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        return  $data instanceof Task || $data instanceof Note ;
    }

}