<?php

namespace App\Tests\E2E;

use Facebook\WebDriver\WebDriverBy;

class LoginTest extends BasePantherTest
{
    public function testWithInvalidCredentials(): void
    {
        $client = self::getPantherClient();

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

    public function testSuccessful(): void
    {
        $client = self::getPantherClient();

        // Navegar a la página de login
        $client->request('GET', 'https://127.0.0.1:8000/login');
        $client->waitFor('#exampleInputEmail');

        $driver = $client->getWebDriver();

        // Llenar el campo email
        $driver->findElement(WebDriverBy::id('exampleInputEmail'))
            ->clear()
            ->sendKeys('superadmin');

        // Llenar el campo password
        $driver->findElement(WebDriverBy::id('exampleInputPassword'))
            ->clear()
            ->sendKeys('superadmin');

        // Clic en el botón de autenticación
        $driver->findElement(WebDriverBy::xpath('//button[contains(text(),"Autenticarse")]'))
            ->click();

        // Esperar y verificar login exitoso
        $client->waitForElementToContain('body', 'Proyectos');
        $this->assertSelectorTextContains('body', 'Proyectos');

        $client->quit();
    }
}
