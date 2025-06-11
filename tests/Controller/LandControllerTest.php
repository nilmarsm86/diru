<?php

namespace App\Tests\Controller;

use App\Entity\Land;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LandControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $landRepository;
    private string $path = '/land/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->landRepository = $this->manager->getRepository(Land::class);

        foreach ($this->landRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Land index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'land[landArea]' => 'Testing',
            'land[occupiedArea]' => 'Testing',
            'land[perimeter]' => 'Testing',
            'land[photo]' => 'Testing',
            'land[microlocalization]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->landRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Land();
        $fixture->setLandArea('My Title');
        $fixture->setOccupiedArea('My Title');
        $fixture->setPerimeter('My Title');
        $fixture->setPhoto('My Title');
        $fixture->setMicrolocalization('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Land');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Land();
        $fixture->setLandArea('Value');
        $fixture->setOccupiedArea('Value');
        $fixture->setPerimeter('Value');
        $fixture->setPhoto('Value');
        $fixture->setMicrolocalization('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'land[landArea]' => 'Something New',
            'land[occupiedArea]' => 'Something New',
            'land[perimeter]' => 'Something New',
            'land[photo]' => 'Something New',
            'land[microlocalization]' => 'Something New',
        ]);

        self::assertResponseRedirects('/land/');

        $fixture = $this->landRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getLandArea());
        self::assertSame('Something New', $fixture[0]->getOccupiedArea());
        self::assertSame('Something New', $fixture[0]->getPerimeter());
        self::assertSame('Something New', $fixture[0]->getPhoto());
        self::assertSame('Something New', $fixture[0]->getMicrolocalization());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Land();
        $fixture->setLandArea('Value');
        $fixture->setOccupiedArea('Value');
        $fixture->setPerimeter('Value');
        $fixture->setPhoto('Value');
        $fixture->setMicrolocalization('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/land/');
        self::assertSame(0, $this->landRepository->count([]));
    }
}
