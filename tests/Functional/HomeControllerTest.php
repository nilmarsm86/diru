<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createLocalClient();
        $client->request('GET', '/home');

        self::assertResponseIsSuccessful();
    }
}
