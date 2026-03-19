<?php

namespace App\Tests\Controller;

use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\PantherTestCase;

class LoginErrorTest extends PantherTestCase
{
    /**
     * @retrun mixed
     */
    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createPantherClient([
            'browser' => static::CHROME,
            'driver_options' => [
                'chromeOptions' => [
                    'args' => [
                        '--ignore-certificate-errors',
                        '--disable-background-networking',
                        '--disable-client-side-phishing-detection',
                        '--disable-default-apps',
                        '--disable-extensions',
                        '--disable-sync',
                        '--no-first-run',
                        '--log-level=3',  // Solo errores críticos
                    ],
                ],
            ],
        ]);

        // Navegar a la página de login
        $crawler = $client->request('GET', 'https://127.0.0.1:8000/login');

        // Esperar a que la página cargue
        $client->waitFor('#exampleInputEmail');

        $driver = $client->getWebDriver();

        // Limpiar y llenar el campo email
        $emailInput = $driver->findElement(WebDriverBy::id('exampleInputEmail'));
        $emailInput->clear();
        $emailInput->sendKeys('pepe');

        // Limpiar y llenar el campo password
        $passwordInput = $driver->findElement(WebDriverBy::id('exampleInputPassword'));
        $passwordInput->clear();
        $passwordInput->sendKeys('pepe');

        // Encontrar y hacer clic en el botón de autenticación
        $button = $crawler->selectButton('Autenticarse');
        $button->click();

        // Esperar hasta que aparezca el mensaje de error
        $client->waitForElementToContain('body', 'Credenciales inválidas');

        // Verificar que el mensaje de error está presente
        $this->assertSelectorTextContains('body', 'Credenciales inválidas');

        // Cerrar el cliente
        $client->quit();
    }
}
