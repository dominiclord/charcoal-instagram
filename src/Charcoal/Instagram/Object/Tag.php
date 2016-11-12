<?php

namespace Charcoal\Instagram\Object;

use \DateTime;
use \DateTimeInterface;
use \InvalidArgumentException;

// From `pimple`
use \Pimple\Container;

// From `charcoal-factory`
use \Charcoal\Factory\FactoryInterface;

// From `charcoal-core`
use \Charcoal\Model\AbstractModel;

// From `charcoal-support`
use \Charcoal\Support\Container\DependentInterface;
use \Charcoal\Support\Model\ManufacturableModelTrait;
use \Charcoal\Support\Model\ManufacturableModelCollectionTrait;

/**
 * Instagram Tag Object
 */
class Tag extends AbstractModel
{
    /**
     * Objects are active by default.
     *
     * @var boolean $active
     */
    protected $active = true;

    // Setters and getters
    // =================================================================================================================

    /**
     * @param boolean $active The active flag.
     * @return Content Chainable
     */
    public function setActive($active)
    {
        $this->active = !!$active;

        return $this;
    }

    /**
     * @return boolean
     */
    public function active()
    {
        return $this->active;
    }
}
