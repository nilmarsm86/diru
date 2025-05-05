<?php

namespace App\Tests\Controller;

use App\Entity\EnterpriseClient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class EnterpriseClientControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $enterpriseClientRepository;
    private string $path = '/enterprise/client/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->enterpriseClientRepository = $this->manager->getRepository(EnterpriseClient::class);

        foreach ($this->enterpriseClientRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('EnterpriseClient index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'enterprise_client[phone]' => 'Testing',
            'enterprise_client[email]' => 'Testing',
            'enterprise_client[street]' => 'Testing',
            'enterprise_client[person]' => 'Testing',
            'enterprise_client[municipality]' => 'Testing',
            'enterprise_client[corporateEntity]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->enterpriseClientRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new EnterpriseClient();
        $fixture->setPhone('My Title');
        $fixture->setEmail('My Title');
        $fixture->setStreet('My Title');
        $fixture->setPerson('My Title');
        $fixture->setMunicipality('My Title');
        $fixture->setCorporateEntity('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('EnterpriseClient');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new EnterpriseClient();
        $fixture->setPhone('Value');
        $fixture->setEmail('Value');
        $fixture->setStreet('Value');
        $fixture->setPerson('Value');
        $fixture->setMunicipality('Value');
        $fixture->setCorporateEntity('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'enterprise_client[phone]' => 'Something New',
            'enterprise_client[email]' => 'Something New',
            'enterprise_client[street]' => 'Something New',
            'enterprise_client[person]' => 'Something New',
            'enterprise_client[municipality]' => 'Something New',
            'enterprise_client[corporateEntity]' => 'Something New',
        ]);

        self::assertResponseRedirects('/enterprise/client/');

        $fixture = $this->enterpriseClientRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getPhone());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getStreet());
        self::assertSame('Something New', $fixture[0]->getPerson());
        self::assertSame('Something New', $fixture[0]->getMunicipality());
        self::assertSame('Something New', $fixture[0]->getCorporateEntity());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new EnterpriseClient();
        $fixture->setPhone('Value');
        $fixture->setEmail('Value');
        $fixture->setStreet('Value');
        $fixture->setPerson('Value');
        $fixture->setMunicipality('Value');
        $fixture->setCorporateEntity('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/enterprise/client/');
        self::assertSame(0, $this->enterpriseClientRepository->count([]));
    }
}
