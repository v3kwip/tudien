<?php

namespace tudien\entity\term;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

/**
 * @Entity()
 */
class Pronunciation
{
    /**
     * @Id()
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string")
     * @var string
     */
    private $filePath;

    /**
     * Pronunciation constructor.
     *
     * @param $id
     * @param $filePath
     */
    public function __construct($id, $filePath)
    {
        $this->id = $id;
        $this->filePath = $filePath;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
}
