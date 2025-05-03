<?php

namespace App\Tests\Controller;

use App\Entity\IndividualClient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class IndividualClientControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $individualClientRepository;
    private string $path = '/individual/client/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->individualClientRepository = $this->manager->getRepository(IndividualClient::class);

        foreach ($this->individualClientRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('IndividualClient index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'individual_client[phone]' => 'Testing',
            'individual_client[email]' => 'Testing',
            'individual_client[person]' => 'Testing',
            'individual_client[municipality]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->individualClientRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new IndividualClient();
        $fixture->setPhone('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPerson('My Title');
        $fixture->setMunicipality('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('IndividualClient');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new IndividualClient();
        $fixture->setPhone('Value');
        $fixture->setEmail('Value');
        $fixture->setPerson('Value');
        $fixture->setMunicipality('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'individual_client[phone]' => 'Something New',
            'individual_client[email]' => 'Something New',
            'individual_client[person]' => 'Something New',
            'individual_client[municipality]' => 'Something New',
        ]);

        self::assertResponseRedirects('/individual/client/');

        $fixture = $this->individualClientRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getPhone());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getPerson());
        self::assertSame('Something New', $fixture[0]->getMunicipality());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new IndividualClient();
        $fixture->setPhone('Value');
        $fixture->setEmail('Value');
        $fixture->setPerson('Value');
        $fixture->setMunicipality('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/individual/client/');
        self::assertSame(0, $this->individualClientRepository->count([]));
    }
}
