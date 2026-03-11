<?php

namespace App\Tests\Controller;

use App\Entity\ConstructiveAction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ConstructiveActionControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $constructiveActionRepository;
    private string $path = '/constructive/action/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->constructiveActionRepository = $this->manager->getRepository(ConstructiveAction::class);

        foreach ($this->constructiveActionRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ConstructiveAction index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'constructive_action[type]' => 'Testing',
            'constructive_action[name]' => 'Testing',
        ]);

        self::assertResponseRedirects('/constructive/action');

        self::assertSame(1, $this->constructiveActionRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new ConstructiveAction();
        $fixture->setType('My Title');
        $fixture->setName('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ConstructiveAction');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new ConstructiveAction();
        $fixture->setType('Value');
        $fixture->setName('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'constructive_action[type]' => 'Something New',
            'constructive_action[name]' => 'Something New',
        ]);

        self::assertResponseRedirects('/constructive/action');

        $fixture = $this->constructiveActionRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getName());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new ConstructiveAction();
        $fixture->setType('Value');
        $fixture->setName('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/constructive/action');
        self::assertSame(0, $this->constructiveActionRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
