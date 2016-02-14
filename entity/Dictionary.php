<?php

namespace tudien\entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity()
 */
class Dictionary
{
    /**
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(type="string")
     * @var string
     */
    private $slug;

    /**
     * @Column(type="string")
     * @var string
     */
    private $name;

    /**
     * Dictionary constructor.
     *
     * @param int    $id
     * @param string $slug
     * @param string $name
     */
    public function __construct($id, $slug, $name)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
