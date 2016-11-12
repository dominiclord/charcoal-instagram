<?php

namespace Charcoal\Instagram\Object;

use \DateTime;
use \DateTimeInterface;
use \InvalidArgumentException;

// From `charcoal-core`
use \Charcoal\Model\AbstractModel;

/**
 * Instagram Tag Object
 */
class User extends AbstractModel
{
    /**
     * Objects are active by default.
     *
     * @var boolean $active
     */
    protected $active = true;

    /**
     * The object's username.
     *
     * @var string|null
     */
    protected $username;

    /**
     * The object's full name.
     *
     * @var string|null
     */
    protected $fullName;

    /**
     * The path to the object's profile picture.
     *
     * @var string|null
     */
    protected $profilePicture;

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

    /**
     * Retrieve the object's username.
     *
     * @param string $username The displayed username.
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Retrieve the object's username.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * Retrieve the object's full name.
     *
     * @param string $fullName A full name.
     * @return self
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Retrieve the object's full name.
     *
     * @return string
     */
    public function fullName()
    {
        return $this->fullName;
    }

    /**
     * Retrieve the object's profile picture.
     *
     * @param string $profilePicture A path to an image.
     * @return self
     */
    public function setprofilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * Retrieve the object's profile picture.
     *
     * @return string
     */
    public function profilePicture()
    {
        return $this->profilePicture;
    }
}
