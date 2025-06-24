<?php

namespace App\Story;

use App\Factory\ContactFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

/**
 * AppStory seeds the database with sample Contact data for development and testing.
 *
 * This story uses the ContactFactory to generate a batch of contacts.
 */
#[AsFixture(name: 'main')]
final class AppStory extends Story
{

    /**
     * Build and persist the story's objects.
     *
     * This method creates 200 Contact entities with randomized data.
     */
    public function build(): void
    {
        // Create 200 random Contact entities for fixtures
        ContactFactory::createMany(200);
    }
}
