<?php

namespace App\Tests\Controller;

use App\Entity\CorporateEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CorporateEntityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $corporateEntityRepository;
    private string $path = '/corporate/entity/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->corporateEntityRepository = $this->manager->getRepository(CorporateEntity::class);

        foreach ($this->corporateEntityRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('CorporateEntity index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'corporate_entity[code]' => 'Testing',
            'corporate_entity[nit]' => 'Testing',
            'corporate_entity[type]' => 'Testing',
            'corporate_entity[name]' => 'Testing',
            'corporate_entity[municipality]' => 'Testing',
            'corporate_entity[organism]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->corporateEntityRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new CorporateEntity();
        $fixture->setCode('My Title');
        $fixture->setNit('My Title');
        $fixture->setType('My Title');
        $fixture->setName('My Title');
        $fixture->setMunicipality('My Title');
        $fixture->setOrganism('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('CorporateEntity');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new CorporateEntity();
        $fixture->setCode('Value');
        $fixture->setNit('Value');
        $fixture->setType('Value');
        $fixture->setName('Value');
        $fixture->setMunicipality('Value');
        $fixture->setOrganism('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'corporate_entity[code]' => 'Something New',
            'corporate_entity[nit]' => 'Something New',
            'corporate_entity[type]' => 'Something New',
            'corporate_entity[name]' => 'Something New',
            'corporate_entity[municipality]' => 'Something New',
            'corporate_entity[organism]' => 'Something New',
        ]);

        self::assertResponseRedirects('/corporate/entity/');

        $fixture = $this->corporateEntityRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getCode());
        self::assertSame('Something New', $fixture[0]->getNit());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getMunicipality());
        self::assertSame('Something New', $fixture[0]->getOrganism());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new CorporateEntity();
        $fixture->setCode('Value');
        $fixture->setNit('Value');
        $fixture->setType('Value');
        $fixture->setName('Value');
        $fixture->setMunicipality('Value');
        $fixture->setOrganism('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/corporate/entity/');
        self::assertSame(0, $this->corporateEntityRepository->count([]));
    }
}
