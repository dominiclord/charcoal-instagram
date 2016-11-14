<?php

namespace Charcoal\Instagram;

use \DateTime;
use \Exception;
use \InvalidArgumentException;

// Module `charcoal-factory` dependencies
use \Charcoal\Factory\FactoryInterface;

// From 'larabros/elogram'
use \Larabros\Elogram\Client as InstagramClient;

// From `charcoal-instagram`
use \Charcoal\Instagram\Object\Media;
use \Charcoal\Instagram\Object\Tag;
use \Charcoal\Instagram\Object\User;
use \Charcoal\Instagram\Object\ScrapeRecord;

/**
 * Scraping class that connects to Instagram API and converts data to Charcoal Objects.
 */
class Scraper
{
    /**
     * @var FactoryInterface $modelFactory
     */
    private $modelFactory;

    /**
     * @var \Larabros\Elogram\Client $instagramClient
     */
    private $instagramClient;

    /**
     * @var array $results
     */
    private $results;

    /**
     * @param array $data The constructor options.
     * @return void
     */
    public function __construct(array $data)
    {
        $this->setModelFactory($data['model_factory']);
        $this->setInstagramClient($data['client']);
    }

    /**
     * @param FactoryInterface $factory The factory used to create logs and models.
     * @return void
     */
    private function setModelFactory(FactoryInterface $factory)
    {
        $this->modelFactory = $factory;
    }

    /**
     * @throws Exception If the model factory was not properly set.
     * @return FactoryInterface
     */
    protected function modelFactory()
    {
        if ($this->modelFactory === null) {
            throw new Exception(
                'Can not access model factory, the dependency has not been set.'
            );
        }
        return $this->modelFactory;
    }

    /**
     * @param InstagramClient $client The Instagram Client used to query the API.
     * @return self
     */
    private function setInstagramClient(InstagramClient $client)
    {
        $this->instagramClient = $client;

        return $this;
    }

    /**
     * Retrieve the Instagram Client.
     *
     * @throws Exception If the Instagram client was not properly set.
     * @return InstagramClient
     */
    protected function instagramClient()
    {
        if ($this->instagramClient === null) {
            throw new Exception(
                'Can not access Instagram client, the dependency has not been set.'
            );
        }
        return $this->instagramClient;
    }

    /**
     * Retrieve results.
     *
     * @return ModelInterface[]|array|null
     */
    public function results()
    {
        return $this->results;
    }

    /**
     * Scrape Instagram API according to hashtag.
     *
     * @param  string $tag The searched tag.
     * @throws InvalidArgumentException If the query is not a string.
     * @return ModelInterface[]|null
     */
    public function scrapeByTag($tag)
    {
        if (!is_string($tag)) {
            throw new InvalidArgumentException(
                'Scraped tag must be a string.'
            );
        }

        if ($tag == '') {
            throw new InvalidArgumentException(
                'Tag can not be empty.'
            );
        }

        return $this->scrapeMedia([
            'repository' => 'tags',
            'method' => 'getRecentMedia',
            'filter' => $tag
        ]);
    }

    /**
     * Scrape Instagram API for all posts by authorized user.
     *
     * @return ModelInterface[]|null
     */
    public function scrapeAll()
    {
        return $this->scrapeMedia([
            'repository' => 'users',
            'method' => 'getMedia',
            'filter' => 'self'
        ]);
    }

    /**
     * Scrape Instagram API and parse scraped data to create Charcoal models.
     *
     * @param  string $tag The searched tag.
     * @param  array  $data  Raw API data.
     * @throws InvalidArgumentException If the query is not a string.
     * @return ModelInterface[]|null
     */
    private function scrapeMedia(array $options = [])
    {
        if ($this->results === null) {
            // Test for recent scrapes
            $record = $this->fetchRecentScrapeRecord($options);

            // An non-null ID means a recent record exists
            if ($record->id() !== null) {
                return $this->results;
            }

            // Reset results
            $this->results = [];

            $callApi = true;
            $max = $min = null;
            $rawMedias = [];
            $models = [];

            // First, attempt fetching Instagram data through pagination
            try {
                while ($callApi) {
                    $apiResponse = $this->instagramClient()->{$options['repository']}()->{$options['method']}($options['filter'], 32, $min, $max);

                    $rawMedias = $apiResponse->get()->merge($rawMedias);

                    if (empty($apiResponse->pagination->next_max_tag_id)) {
                        $callApi = false;
                    } else {
                        $max = $apiResponse->pagination->next_max_tag_id;
                    }
                }
            } catch (Exception $e) {
                error_log('Fatal exception');
                error_log(get_class($e));
                error_log($e->getMessage());
                return $this->results;
            }

            // Save the scrape record for caching purposes
            $record->save();

            // Loop through all media and store them with Charcoal if they don't already exist
            foreach ($rawMedias as $media) {
                $mediaModel = $this->modelFactory()->create(Media::class);

                if (!$mediaModel->source()->tableExists()) {
                    $mediaModel->source()->createTable();
                }

                $mediaModel->load($media['id']);

                if ($mediaModel->id() === null) {
                    $tags = [];

                    foreach($media['tags'] as $tag) {
                        // Save the hashtags if not already saved
                        $tagModel = $this->modelFactory()->create(Tag::class);

                        if (!$tagModel->source()->tableExists()) {
                            $tagModel->source()->createTable();
                        }

                        $tagModel->load($tag);

                        if ($tagModel->id() === null) {
                            $tagModel->setData([
                                'id' => $tag
                            ]);
                            $tagModel->save();
                        }

                        $tags[] = $tagModel->id();
                    }

                    // Save the user if not already saved
                    $userData = $media['user'];
                    $userModel = $this->modelFactory()->create(User::class);

                    if (!$userModel->source()->tableExists()) {
                        $userModel->source()->createTable();
                    }

                    $userModel->load($userData['id']);

                    if ($userModel->id() === null) {
                        $userModel->setData([
                            'id' => $userData['id'],
                            'username' => $userData['username'],
                            'fullName' => $userData['full_name'],
                            'profilePicture' => $userData['profile_picture']
                        ]);
                        $userModel->save();
                    }

                    $created = new DateTime('now');
                    $created->setTimestamp($media['created_time']);

                    $mediaModel->setData([
                        'id'      => $media['id'],
                        'created' => $created,
                        'tags'    => $tags,
                        'caption' => $media['caption']['text'],
                        'user'    => $userModel->id(),
                        'image'   => $media['images']['standard_resolution']['url'],
                        'type'    => $media['type'],
                        'json'    => json_encode($media)
                    ]);

                    $mediaModel->save();
                }

                $models[] = $mediaModel;
            }

            $this->results = $models;

        }

        return $this->results;
    }

    /**
     * Attempt to get the latest ScrapeRecord according to specific properties
     *
     * @param  array  $options Array of options used to create a new ScrapeRecord
     * @return ModelInterface  A ScrapeRecord instance.
     */
    private function fetchRecentScrapeRecord(array $options = [])
    {
        // Create a proto model to generate the ident
        $proto = $this->modelFactory()
            ->create(ScrapeRecord::class)
            ->setData([
                'repository' => $options['repository'],
                'method' => $options['method'],
                'filter' => $options['filter']
            ]);

        if (!$proto->source()->tableExists()) {
            $proto->source()->createTable();
        }

        $earlierDate = new DateTime('now - 1 hour');

        // Query the DB for an existing record in the past hour
        $model = $this->modelFactory()
            ->create(ScrapeRecord::class)
            ->loadFromQuery('
                SELECT * FROM `' . $proto->source()->table() . '`
                WHERE
                    `ident` = :ident
                ORDER BY
                    `ts` DESC
                LIMIT 1',
                [
                    'ident' => $proto->generateIdent()
                ]
            );

        return ($earlierDate > $model->ts()) ? $proto : $model;
    }
}
