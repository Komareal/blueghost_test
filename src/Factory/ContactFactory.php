<?php

namespace App\Factory;

use App\Entity\Contact;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * Factory for creating Contact entities with randomized data for testing or seeding.
 *
 * @extends PersistentProxyObjectFactory<Contact>
 */
final class ContactFactory extends PersistentProxyObjectFactory
{

    /**
     * ContactFactory constructor.
     * Initializes the factory.
     */
    public function __construct()
    {
        // No initialization required for now.
    }

    /**
     * Returns the default attributes for a Contact entity.
     * This method generates random data using Faker for the Contact entity.
     * Required attributes get generated always (email, name, surname),
     * while optional attributes (phone, note) may or may not be included based on random chance.
     *
     * @return array|callable Default attribute values for Contact
     */
    protected function defaults(): array|callable
    {
        // Generate default values using Faker
        $defaults = [
            'email' => self::faker()->safeEmail(),
            'name' => self::faker()->firstName(),
            'surname' => self::faker()->lastName(),
        ];
        // Optionally add a phone number
        if (self::faker()->boolean()) {
            $defaults['phone'] = self::faker()->phoneNumber();
        }
        // Optionally add a note
        if (self::faker()->boolean()) {
            $defaults['note'] = self::faker()->realTextBetween(50, 200);
        }
        return $defaults;
    }

    /**
     * Hook for additional initialization after instantiation.
     *
     *
     * @return static
     */
    protected function initialize(): static
    {
        return $this->afterInstantiate(function (Contact $contact) {
            static $usedSlugs = [];
            $baseSlug = Contact::slugify($contact->getName(), $contact->getSurname());
            $slugCandidate = $baseSlug;
            $i = 1;
            while (in_array($slugCandidate, $usedSlugs, true)) {
                $slugCandidate = $baseSlug . '-' . $i++;
            }
            $usedSlugs[] = $slugCandidate;
            $contact->setSlug($slugCandidate);
        });
    }

    /**
     * Returns the class name of the entity managed by this factory.
     *
     * @return string
     */
    public static function class(): string
    {
        return Contact::class;
    }
}
