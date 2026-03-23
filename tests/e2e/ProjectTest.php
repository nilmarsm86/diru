<?php

namespace App\Tests\Controller;

use App\Tests\E2E\BasePantherTest;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

class ProjectTest extends BasePantherTest
{
    public function testCreateNewProject(): void
    {
        $client = self::getPantherClient();
        $driver = $client->getWebDriver();

        // PASO 1: Login
        $client->request('GET', 'https://127.0.0.1:8000/login');
        $client->waitFor('#exampleInputEmail');

        $driver->findElement(WebDriverBy::id('exampleInputEmail'))
            ->clear()->sendKeys('superadmin');

        $driver->findElement(WebDriverBy::id('exampleInputPassword'))
            ->clear()->sendKeys('superadmin');

        $driver->findElement(WebDriverBy::xpath('//button[contains(text(),"Autenticarse")]'))->click();

        $client->waitForElementToContain('body', 'Proyectos');
        $this->assertSelectorTextContains('body', 'Proyectos');

        // PASO 2: Navegar a proyectos
        $client->request('GET', 'https://127.0.0.1:8000/project');
        $client->waitForElementToContain('body', 'Listado de proyectos');
        $this->assertSelectorTextContains('body', 'Listado de proyectos');

        // PASO 3: Clic en botón Nuevo
        $driver->findElement(WebDriverBy::xpath('//*[contains(text(),"Nuevo")]'))->click();

        $client->waitForElementToContain('body', 'Nuevo proyecto');
        $this->assertSelectorTextContains('body', 'Nuevo proyecto');

        // PASO 4: Llenar formulario
        $client->waitFor('#project_name');

        $driver->findElement(WebDriverBy::id('project_name'))
            ->clear()->sendKeys('Proyecto4');

        // Seleccionar inversión usando WebDriverSelect
        $investmentSelect = new WebDriverSelect(
            $driver->findElement(WebDriverBy::id('project_investment'))
        );
        $investmentSelect->selectByVisibleText('Inversion1');

        // Seleccionar cliente individual
        $clientSelect = new WebDriverSelect(
            $driver->findElement(WebDriverBy::id('project_individualClient'))
        );
        $clientSelect->selectByVisibleText('Person 1');

        // Seleccionar moneda
        $currencySelect = new WebDriverSelect(
            $driver->findElement(WebDriverBy::id('project_currency'))
        );
        $currencySelect->selectByVisibleText('Peso Cubano');

        // Seleccionar dibujante
        $draftsmanSelect = new WebDriverSelect(
            $driver->findElement(WebDriverBy::id('project_draftsman'))
        );
        $draftsmanSelect->selectByVisibleText('Draftsman');

        // Scroll al botón enviar y hacer clic
        $client->executeScript('document.getElementById("enviar").scrollIntoView();');
        $driver->findElement(WebDriverBy::id('enviar'))->click();

        // Verificar mensaje de éxito
        $client->waitForElementToContain('body', 'Se ha agregado el proyecto');
        $this->assertSelectorTextContains('body', 'Se ha agregado el proyecto');
        $this->assertSelectorTextContains('body', 'Proyecto4');

        $client->quit();
    }
}
