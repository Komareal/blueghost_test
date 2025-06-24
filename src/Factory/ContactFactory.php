<?php

namespace App\Factory;

use App\Entity\Contact;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Contact>
 */
final class ContactFactory extends PersistentProxyObjectFactory
{

    public function __construct()
    {
    }

    protected function defaults(): array|callable
    {
        $defaults = [
            'email' => self::faker()->safeEmail(),
            'name' => self::faker()->firstName(),
            'surname' => self::faker()->lastName(),
        ];
        if (self::faker()->boolean()) {
            $defaults['phone'] = self::faker()->phoneNumber();
        }
        if (self::faker()->boolean()) {
            $defaults['note'] = self::faker()->realTextBetween(50, 200);
        }
        return $defaults;
    }

    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(Contact $contact): void {})
            ;
    }

    public static function class(): string
    {
        return Contact::class;
    }
}
