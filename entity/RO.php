<?php

namespace tudien\entity;

use Doctrine\ORM\Mapping\Column;

class RO
{
    const HAS_TERM          = 1;
    const HAS_TOPIC         = 2;
    const HAS_DEFINITION    = 3;
    const HAS_PRONUNCIATION = 4;
    const HAS_EXAMPLE       = 5;
    const HAS_SYNONYM       = 6;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $type;

    /**
     * @Column(type="string")
     * @var string
     */
    private $source;

    /**
     * @Column(type="string")
     * @var string
     */
    private $target;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $weight;

    /**
     * RO constructor.
     *
     * @param int    $type
     * @param string $source
     * @param string $target
     * @param int    $weight
     */
    public function __construct($type, $source, $target, $weight = 0)
    {
        $this->type = $type;
        $this->source = $source;
        $this->target = $target;
        $this->weight = $weight;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }
}
