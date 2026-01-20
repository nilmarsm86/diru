<?php

namespace App\Tests\Controller;

use App\Entity\BuildingRevision;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BuildingRevisionControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $buildingRevisionRepository;
    private string $path = '/building/revision/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->buildingRevisionRepository = $this->manager->getRepository(BuildingRevision::class);

        foreach ($this->buildingRevisionRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('BuildingRevision index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'building_revision[createdAt]' => 'Testing',
            'building_revision[comment]' => 'Testing',
            'building_revision[building]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->buildingRevisionRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new BuildingRevision();
        $fixture->setCreatedAt('My Title');
        $fixture->setComment('My Title');
        $fixture->setBuilding('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('BuildingRevision');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new BuildingRevision();
        $fixture->setCreatedAt('Value');
        $fixture->setComment('Value');
        $fixture->setBuilding('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'building_revision[createdAt]' => 'Something New',
            'building_revision[comment]' => 'Something New',
            'building_revision[building]' => 'Something New',
        ]);

        self::assertResponseRedirects('/building/revision/');

        $fixture = $this->buildingRevisionRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getComment());
        self::assertSame('Something New', $fixture[0]->getBuilding());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new BuildingRevision();
        $fixture->setCreatedAt('Value');
        $fixture->setComment('Value');
        $fixture->setBuilding('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/building/revision/');
        self::assertSame(0, $this->buildingRevisionRepository->count([]));
    }
}
