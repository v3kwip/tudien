<?php

namespace tudien\entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

/**
 * @Entity()
 */
class Term
{
    /**
     * @Id()
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     * @var integer
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
     * Term constructor.
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
