<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

abstract class BasePantherTest extends PantherTestCase
{
    protected static ?\Symfony\Component\Panther\Client $pantherClient = null;

    protected static function getPantherClient(): \Symfony\Component\Panther\Client
    {
        if (null === self::$pantherClient) {
            self::$pantherClient = static::createPantherClient([
                'browser' => static::CHROME,
                'driver_options' => [
                    'chromeOptions' => [
                        'args' => [
                            '--ignore-certificate-errors',
                            '--disable-background-networking',
                            '--disable-default-apps',
                            '--disable-extensions',
                            '--disable-sync',
                            '--no-first-run',
                            '--log-level=3',
                            // '--headless',        // sin UI = más rápido en CI
                            '--disable-gpu',
                            '--no-sandbox',
                        ],
                    ],
                ],
            ]);
        }

        return self::$pantherClient;
    }

    public static function tearDownAfterClass(): void
    {
        if (null !== self::$pantherClient) {
            self::$pantherClient->quit();
            self::$pantherClient = null;
        }
    }

    // Limpia cookies/estado entre tests sin reiniciar Chrome
    protected function resetSession(): void
    {
        self::$pantherClient?->getWebDriver()->manage()->deleteAllCookies();
        self::$pantherClient?->request('GET', 'about:blank');
    }
}
