<?php

namespace Charcoal\Instagram\Object;

use \DateTime;
use \DateTimeInterface;
use \Traversable;
use \InvalidArgumentException;

// From 'charcoal-core'
use \Charcoal\Model\AbstractModel;

/**
 * Scrape records should be saved every time a client initiates a scrape request.
 */
class ScrapeRecord extends AbstractModel
{
    /**
     * The scrape record identifier.
     *
     * @var string
     */
    protected $ident;

    /**
     * The scrape record repository.
     *
     * @var string
     */
    protected $repository;

    /**
     * The scrape record method.
     *
     * @var string
     */
    protected $method;

    /**
     * The scrape record filter.
     *
     * @var string
     */
    protected $filter;

    /**
     * Timestamp of the scrape request.
     *
     * @var DateTimeInterface|null
     */
    protected $ts;

    /**
     * Generate an ident from the object's repository, method, and filter properties.
     *
     * @return string
     */
    public function generateIdent()
    {
        $ident = [
            $this->repository(),
            $this->method(),
            $this->filter()
        ];
        return strtolower(implode('/', $ident));
    }

    /**
     * Set the scrape record's identifier.
     *
     * @param  string $ident The scrape record.
     * @throws InvalidArgumentException If the identifier is not a string.
     * @return ScrapeRecord Chainable
     */
    public function setIdent($ident)
    {
        if (!is_string($ident)) {
            throw new InvalidArgumentException(
                'Scrape ident must be a string.'
            );
        }

        $this->ident = $ident;

        return $this;
    }

    /**
     * Retrieve the scrape record's identifier.
     *
     * @return string
     */
    public function ident()
    {
        return $this->ident;
    }

    /**
     * Set the scrape repository.
     *
     * @param  string $repository The repository name.
     * @throws InvalidArgumentException If the repository is not a string.
     * @return ScrapeRecord Chainable
     */
    public function setRepository($repository)
    {
        if (!is_string($repository)) {
            throw new InvalidArgumentException(
                'Repository must be a string'
            );
        }

        $this->repository = $repository;

        return $this;
    }

    /**
     * Retrieve the repository name.
     *
     * @return string
     */
    public function repository()
    {
        return $this->repository;
    }

    /**
     * Set the scrape method.
     *
     * @param  string $method The method name.
     * @throws InvalidArgumentException If the method is not a string.
     * @return ScrapeRecord Chainable
     */
    public function setMethod($method)
    {
        if (!is_string($method)) {
            throw new InvalidArgumentException(
                'Method must be a string'
            );
        }

        $this->method = $method;

        return $this;
    }

    /**
     * Retrieve the method name.
     *
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * Set the scrape filter.
     *
     * @param  string $filter The filter name.
     * @throws InvalidArgumentException If the filter is not a string.
     * @return ScrapeRecord Chainable
     */
    public function setFilter($filter)
    {
        if (!is_string($filter)) {
            throw new InvalidArgumentException(
                'Filter must be a string'
            );
        }

        $this->filter = $filter;

        return $this;
    }

    /**
     * Retrieve the filter name.
     *
     * @return string
     */
    public function filter()
    {
        return $this->filter;
    }

    /**
     * Set when the scrape was initiated.
     *
     * @param  DateTime|string|null $timestamp The timestamp of scrape record.
     *     NULL is accepted and instances of DateTimeInterface are recommended;
     *     any other value will be converted (if possible) into one.
     * @throws InvalidArgumentException If the timestamp is invalid.
     * @return ScrapeRecord Chainable
     */
    public function setTs($timestamp)
    {
        if ($timestamp === null) {
            $this->ts = null;
            return $this;
        }

        if (is_string($timestamp)) {
            try {
                $timestamp = new DateTime($timestamp);
            } catch (Exception $e) {
                throw new InvalidArgumentException(
                    sprintf('Invalid timestamp: %s', $e->getMessage())
                );
            }
        }

        if (!$timestamp instanceof DateTimeInterface) {
            throw new InvalidArgumentException(
                'Invalid timestamp value. Must be a date/time string or a DateTime object.'
            );
        }

        $this->ts = $timestamp;

        return $this;
    }

    /**
     * Retrieve the creation timestamp.
     *
     * @return DateTime|null
     */
    public function ts()
    {
        return $this->ts;
    }

    /**
     * Event called before _creating_ the object.
     *
     * @see    Charcoal\Source\StorableTrait::preSave() For the "create" Event.
     * @return boolean
     */
    public function preSave()
    {
        $result = parent::preSave();

        $this->setTs('now');
        $this->setIdent($this->generateIdent());

        return $result;
    }
}
