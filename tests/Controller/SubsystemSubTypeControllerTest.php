<?php

namespace App\Tests\Controller;

use App\Entity\SubsystemSubType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SubsystemSubTypeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $subsystemSubTypeRepository;
    private string $path = '/subsystem/sub/type/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->subsystemSubTypeRepository = $this->manager->getRepository(SubsystemSubType::class);

        foreach ($this->subsystemSubTypeRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('SubsystemSubType index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'subsystem_sub_type[name]' => 'Testing',
            'subsystem_sub_type[subsystemType]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->subsystemSubTypeRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new SubsystemSubType();
        $fixture->setName('My Title');
        $fixture->setSubsystemType('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('SubsystemSubType');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new SubsystemSubType();
        $fixture->setName('Value');
        $fixture->setSubsystemType('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'subsystem_sub_type[name]' => 'Something New',
            'subsystem_sub_type[subsystemType]' => 'Something New',
        ]);

        self::assertResponseRedirects('/subsystem/sub/type/');

        $fixture = $this->subsystemSubTypeRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getSubsystemType());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new SubsystemSubType();
        $fixture->setName('Value');
        $fixture->setSubsystemType('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/subsystem/sub/type/');
        self::assertSame(0, $this->subsystemSubTypeRepository->count([]));
    }
}
