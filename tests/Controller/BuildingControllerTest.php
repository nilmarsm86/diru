<?php

namespace App\Tests\Controller;

use App\Entity\Building;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BuildingControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $buildingRepository;
    private string $path = '/building/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->buildingRepository = $this->manager->getRepository(Building::class);

        foreach ($this->buildingRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Building index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'building[estimatedValueConstruction]' => 'Testing',
            'building[estimatedValueEquipment]' => 'Testing',
            'building[estimatedValueOther]' => 'Testing',
            'building[approvedValueConstruction]' => 'Testing',
            'building[approvedValueEquipment]' => 'Testing',
            'building[approvedValueOther]' => 'Testing',
            'building[name]' => 'Testing',
            'building[constructor]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->buildingRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Building();
        //        $fixture->setEstimatedValueConstruction('My Title');
        $fixture->setEstimatedValueEquipment('My Title');
        $fixture->setEstimatedValueOther('My Title');
        $fixture->setApprovedValueConstruction('My Title');
        $fixture->setApprovedValueEquipment('My Title');
        $fixture->setApprovedValueOther('My Title');
        $fixture->setName('My Title');
        $fixture->setConstructor('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Building');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Building();
        //        $fixture->setEstimatedValueConstruction('Value');
        $fixture->setEstimatedValueEquipment('Value');
        $fixture->setEstimatedValueOther('Value');
        $fixture->setApprovedValueConstruction('Value');
        $fixture->setApprovedValueEquipment('Value');
        $fixture->setApprovedValueOther('Value');
        $fixture->setName('Value');
        $fixture->setConstructor('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'building[estimatedValueConstruction]' => 'Something New',
            'building[estimatedValueEquipment]' => 'Something New',
            'building[estimatedValueOther]' => 'Something New',
            'building[approvedValueConstruction]' => 'Something New',
            'building[approvedValueEquipment]' => 'Something New',
            'building[approvedValueOther]' => 'Something New',
            'building[name]' => 'Something New',
            'building[constructor]' => 'Something New',
        ]);

        self::assertResponseRedirects('/building/');

        $fixture = $this->buildingRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getEstimatedValueConstruction());
        self::assertSame('Something New', $fixture[0]->getEstimatedValueEquipment());
        self::assertSame('Something New', $fixture[0]->getEstimatedValueOther());
        self::assertSame('Something New', $fixture[0]->getApprovedValueConstruction());
        self::assertSame('Something New', $fixture[0]->getApprovedValueEquipment());
        self::assertSame('Something New', $fixture[0]->getApprovedValueOther());
        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getConstructor());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Building();
        //        $fixture->setEstimatedValueConstruction('Value');
        $fixture->setEstimatedValueEquipment('Value');
        $fixture->setEstimatedValueOther('Value');
        $fixture->setApprovedValueConstruction('Value');
        $fixture->setApprovedValueEquipment('Value');
        $fixture->setApprovedValueOther('Value');
        $fixture->setName('Value');
        $fixture->setConstructor('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/building/');
        self::assertSame(0, $this->buildingRepository->count([]));
    }
}
