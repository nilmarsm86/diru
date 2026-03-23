<?php

namespace App\Tests\Controller;

use App\Tests\E2E\BasePantherTest;
use Facebook\WebDriver\WebDriverBy;

class RegistrationTest extends BasePantherTest
{
    public function testEmptyRegistration(): void
    {
        $client = self::getPantherClient();

        // Navegar a la página de registro
        $client->request('GET', 'https://127.0.0.1:8000/register');
        $client->waitFor('#registration_form_name');

        $driver = $client->getWebDriver();

        // Clic en botón Crear Cuenta sin llenar campos
        $driver->findElement(WebDriverBy::xpath('//button[contains(text(),"Crear Cuenta")]'))
            ->click();

        // Verificar que aparece el mensaje de campo vacío
        $client->waitForElementToContain('body', 'vacío');
        $this->assertSelectorTextContains('body', 'vacío');

        $client->quit();
    }

    public function testPasswordMinLengthValidation(): void
    {
        $client = self::getPantherClient();

        // Navegar a la página de registro
        $client->request('GET', 'https://127.0.0.1:8000/register');
        $client->waitFor('#registration_form_name');

        $driver = $client->getWebDriver();

        $driver->findElement(WebDriverBy::id('registration_form_name'))
            ->clear()->sendKeys('Nilmar');

        $driver->findElement(WebDriverBy::id('registration_form_lastname'))
            ->clear()->sendKeys('Sanchez Muguercia');

        $driver->findElement(WebDriverBy::id('registration_form_identificationNumber'))
            ->clear()->sendKeys('86122308643');

        $driver->findElement(WebDriverBy::id('registration_form_phone'))
            ->clear()->sendKeys('54023981');

        $driver->findElement(WebDriverBy::id('registration_form_email'))
            ->clear()->sendKeys('nilmarsm86@gmail.com');

        $driver->findElement(WebDriverBy::id('registration_form_username'))
            ->clear()->sendKeys('nilmarsm86');

        // Contraseña corta (4 caracteres) para provocar el error
        $driver->findElement(WebDriverBy::id('registration_form_plainPassword_first'))
            ->clear()->sendKeys('pepe');

        $driver->findElement(WebDriverBy::id('registration_form_plainPassword_second'))
            ->clear()->sendKeys('pepe');

        // Aceptar términos
        $driver->findElement(WebDriverBy::id('registration_form_agreeTerms'))->click();

        // Enviar formulario
        $driver->findElement(WebDriverBy::xpath('//button[contains(text(),"Crear Cuenta")]'))->click();

        // Verificar mensaje de error por contraseña corta
        $client->waitForElementToContain('body', 'La contraseña debe tener como mínimo 6 caracteres');
        $this->assertSelectorTextContains('body', 'La contraseña debe tener como mínimo 6 caracteres');

        $client->quit();
    }

    public function testSuccessfulRegistration(): void
    {
        $client = self::getPantherClient();

        // Navegar a la página de registro
        $client->request('GET', 'https://127.0.0.1:8000/register');
        $client->waitFor('#registration_form_name');

        $driver = $client->getWebDriver();

        $driver->findElement(WebDriverBy::id('registration_form_name'))
            ->clear()->sendKeys('Nilmar');

        $driver->findElement(WebDriverBy::id('registration_form_lastname'))
            ->clear()->sendKeys('Sanchez Muguercia');

        $driver->findElement(WebDriverBy::id('registration_form_identificationNumber'))
            ->clear()->sendKeys('86122308643');

        $driver->findElement(WebDriverBy::id('registration_form_phone'))
            ->clear()->sendKeys('54023981');

        $driver->findElement(WebDriverBy::id('registration_form_email'))
            ->clear()->sendKeys('nilmarsm86@gmail.com');

        $driver->findElement(WebDriverBy::id('registration_form_username'))
            ->clear()->sendKeys('nilmarsm86');

        $driver->findElement(WebDriverBy::id('registration_form_plainPassword_first'))
            ->clear()->sendKeys('TigreBomby86*');

        $driver->findElement(WebDriverBy::id('registration_form_plainPassword_second'))
            ->clear()->sendKeys('TigreBomby86*');

        // Aceptar términos
        $driver->findElement(WebDriverBy::id('registration_form_agreeTerms'))->click();

        // Enviar formulario
        $driver->findElement(WebDriverBy::xpath('//button[contains(text(),"Crear Cuenta")]'))->click();

        // Verificar mensaje de éxito
        $client->waitForElementToContain('body', 'Se a registrado correctamente en el sistema');
        $this->assertSelectorTextContains('body', 'Se a registrado correctamente en el sistema');

        $client->quit();
    }
}
