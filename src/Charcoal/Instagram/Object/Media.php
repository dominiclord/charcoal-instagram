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

// From `charcoal-instagram`
use \Charcoal\Instagram\Object\Tag;
use \Charcoal\Instagram\Object\User;

/**
 * Instagram Media Object
 */
class Media extends AbstractModel implements
    DependentInterface
{
    use ManufacturableModelTrait;
    use ManufacturableModelCollectionTrait;

    /**
     * Objects are active by default.
     *
     * @var boolean $active
     */
    protected $active = true;

    /**
     * Object creation date (provided by third-party).
     *
     * @var DateTime $created
     */
    protected $created;

    /**
     * One or more tags the object belongs to (provided by third-party).
     *
     * @var ModelInterface[]|array|null
     */
    protected $tags;

    /**
     * The object's caption for the media (provided by third-party).
     *
     * @var string|null
     */
    protected $caption;

    /**
     * User object that created the media (provided by third-party).
     *
     * @var ModelInterface|null
     */
    protected $user;

    /**
     * The main media source chosen for the object.
     *
     * @var string|null
     */
    protected $image;

    /**
     * The main media type. Differentiates between an image and video (provided by third-party).
     *
     * @var string|null
     */
    protected $type;

    /**
     * The object's JSON representation/backup as provided by third-party when saved.
     *
     * @var string|null
     */
    protected $json;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setCollectionLoader($container['model/collection/loader']);
        $this->setCollectionLoaderFactory($container['model/collection/loader/factory']);
        $this->setModelFactory($container['model/factory']);
    }

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
     * Set the object's creation date.
     *
     * @param \DateTimeInterface|string|null $created The date/time at object's creation.
     * @throws InvalidArgumentException If the date/time is invalid.
     * @return self
     */
    public function setCreated($created)
    {
        if ($created === null) {
            $this->created = null;
            return $this;
        }
        if (is_string($created)) {
            $created = new DateTime($created);
        }
        if (!($created instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid "Created" value. Must be a date/time string or a DateTime object.'
            );
        }
        $this->created = $created;

        return $this;
    }

    /**
     * Retrieve the object's creation date.
     *
     * @return DateTimeInterface|null
     */
    public function created()
    {
        return $this->created;
    }

    /**
     * Set the tags the object belongs to.
     *
     * @param ModelInterface[]|array|null $tags The object's tags.
     * @return self
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Retrieve the tags the object belongs to.
     *
     * @return ModelInterface[]|array|null
     */
    public function tags()
    {
        return $this->tags;
    }

    /**
     * Set the object's caption.
     *
     * @param  string|null $caption The caption.
     * @return self
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Retrieve the object's caption.
     *
     * @return string|null
     */
    public function caption()
    {
        return $this->caption;
    }

    /**
     * Set the object's user.
     *
     * @param ModelInterface|null
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Retrieve the object's user.
     *
     * @return ModelInterface|null
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * Retrieve the object's image.
     *
     * @param string $image The main image/video.
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Retrieve the object's image.
     *
     * @return string
     */
    public function image()
    {
        return $this->image;
    }

    /**
     * Set the object's media type.
     *
     * @param string $type Either `image` or `video`.
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Retrieve the object's media type.
     *
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Set the object's original JSON structure.
     *
     * @param string $json A JSON structure.
     * @return self
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Retrieve the object's JSON structure.
     *
     * @return string
     */
    public function json()
    {
        return $this->json;
    }
}
