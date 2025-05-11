<?php

namespace App\Factory;

use App\Entity\Employee;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Employee>
 */
final class EmployeeFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Employee::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $firstName = self::faker()->firstName();
        $lastName = self::faker()->lastName();
        $email = strtolower($firstName . '.' . $lastName . '@tasklinker.com');
        $contracts = ['Permanent', 'Fixed-term', 'Work-study'];
        $randomKey= array_rand($contracts);

        return [
            'active' => self::faker()->numberBetween(0, 1),
            'arrival_at' => self::faker()->dateTimeBetween('-6 years'),
            'contract' => $contracts[$randomKey],
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'password' => self::faker()->text(15),
            'role' => self::faker()->numberBetween(0, 1),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Employee $employee): void {})
        ;
    }
}
