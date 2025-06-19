<?php

namespace App\Tests\Controller;

use App\Entity\Local;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LocalControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $localRepository;
    private string $path = '/local/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->localRepository = $this->manager->getRepository(Local::class);

        foreach ($this->localRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Local index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'local[number]' => 'Testing',
            'local[area]' => 'Testing',
            'local[type]' => 'Testing',
            'local[height]' => 'Testing',
            'local[technicalStatus]' => 'Testing',
            'local[type2]' => 'Testing',
            'local[name]' => 'Testing',
            'local[floor]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->localRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Local();
        $fixture->setNumber('My Title');
        $fixture->setArea('My Title');
        $fixture->setType('My Title');
        $fixture->setHeight('My Title');
        $fixture->setTechnicalStatus('My Title');
        $fixture->setType2('My Title');
        $fixture->setName('My Title');
        $fixture->setFloor('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Local');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Local();
        $fixture->setNumber('Value');
        $fixture->setArea('Value');
        $fixture->setType('Value');
        $fixture->setHeight('Value');
        $fixture->setTechnicalStatus('Value');
        $fixture->setType2('Value');
        $fixture->setName('Value');
        $fixture->setFloor('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'local[number]' => 'Something New',
            'local[area]' => 'Something New',
            'local[type]' => 'Something New',
            'local[height]' => 'Something New',
            'local[technicalStatus]' => 'Something New',
            'local[type2]' => 'Something New',
            'local[name]' => 'Something New',
            'local[floor]' => 'Something New',
        ]);

        self::assertResponseRedirects('/local/');

        $fixture = $this->localRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNumber());
        self::assertSame('Something New', $fixture[0]->getArea());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getHeight());
        self::assertSame('Something New', $fixture[0]->getTechnicalStatus());
        self::assertSame('Something New', $fixture[0]->getType2());
        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getFloor());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Local();
        $fixture->setNumber('Value');
        $fixture->setArea('Value');
        $fixture->setType('Value');
        $fixture->setHeight('Value');
        $fixture->setTechnicalStatus('Value');
        $fixture->setType2('Value');
        $fixture->setName('Value');
        $fixture->setFloor('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/local/');
        self::assertSame(0, $this->localRepository->count([]));
    }
}
