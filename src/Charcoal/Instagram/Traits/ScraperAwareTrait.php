<?php

namespace Charcoal\Instagram\Traits;

use \RuntimeException;

// From `charcoal-instagram`
use \Charcoal\Instagram\Scraper as InstagramScraper;

/**
 * Common Instagram Scraper features for template controllers.
 */
trait ScraperAwareTrait
{
    /**
     * Store the scraper instance for the current class.
     *
     * @var Scraper
     */
    protected $instagramScraper;

    /**
     * Set the scraper.
     *
     * @param  Scraper $scrape The scraper instance.
     * @return self
     */
    protected function setInstagramScraper(InstagramScraper $scraper)
    {
        $this->instagramScraper = $scraper;

        return $this;
    }

    /**
     * Retrieve the scraper.
     *
     * @throws RuntimeException If the search runner was not previously set.
     * @return Scraper
     */
    public function instagramScraper()
    {
        if (!isset($this->instagramScraper)) {
            throw new RuntimeException(
                sprintf('Instagram Scraper is not defined for "%s"', get_class($this))
            );
        }

        return $this->instagramScraper;
    }
}
