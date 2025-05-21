<?php

namespace App\Tests\Controller;

use App\Entity\Investment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class InvestmentControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $investmentRepository;
    private string $path = '/investment/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->investmentRepository = $this->manager->getRepository(Investment::class);

        foreach ($this->investmentRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Investment index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'investment[workName]' => 'Testing',
            'investment[investmentName]' => 'Testing',
            'investment[estimatedValueConstruction]' => 'Testing',
            'investment[estimatedValueEquipment]' => 'Testing',
            'investment[estimatedValueOther]' => 'Testing',
            'investment[approvedValueConstruction]' => 'Testing',
            'investment[approvedValueEquipment]' => 'Testing',
            'investment[approvedValueOther]' => 'Testing',
            'investment[betweenStreets]' => 'Testing',
            'investment[town]' => 'Testing',
            'investment[popularCouncil]' => 'Testing',
            'investment[block]' => 'Testing',
            'investment[district]' => 'Testing',
            'investment[street]' => 'Testing',
            'investment[addressNumber]' => 'Testing',
            'investment[constructor]' => 'Testing',
            'investment[locationZone]' => 'Testing',
            'investment[municipality]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->investmentRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Investment();
        $fixture->setWorkName('My Title');
        $fixture->setInvestmentName('My Title');
        $fixture->setEstimatedValueConstruction('My Title');
        $fixture->setEstimatedValueEquipment('My Title');
        $fixture->setEstimatedValueOther('My Title');
        $fixture->setApprovedValueConstruction('My Title');
        $fixture->setApprovedValueEquipment('My Title');
        $fixture->setApprovedValueOther('My Title');
        $fixture->setBetweenStreets('My Title');
        $fixture->setTown('My Title');
        $fixture->setPopularCouncil('My Title');
        $fixture->setBlock('My Title');
        $fixture->setDistrict('My Title');
        $fixture->setStreet('My Title');
        $fixture->setAddressNumber('My Title');
        $fixture->setConstructor('My Title');
        $fixture->setLocationZone('My Title');
        $fixture->setMunicipality('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Investment');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Investment();
        $fixture->setWorkName('Value');
        $fixture->setInvestmentName('Value');
        $fixture->setEstimatedValueConstruction('Value');
        $fixture->setEstimatedValueEquipment('Value');
        $fixture->setEstimatedValueOther('Value');
        $fixture->setApprovedValueConstruction('Value');
        $fixture->setApprovedValueEquipment('Value');
        $fixture->setApprovedValueOther('Value');
        $fixture->setBetweenStreets('Value');
        $fixture->setTown('Value');
        $fixture->setPopularCouncil('Value');
        $fixture->setBlock('Value');
        $fixture->setDistrict('Value');
        $fixture->setStreet('Value');
        $fixture->setAddressNumber('Value');
        $fixture->setConstructor('Value');
        $fixture->setLocationZone('Value');
        $fixture->setMunicipality('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'investment[workName]' => 'Something New',
            'investment[investmentName]' => 'Something New',
            'investment[estimatedValueConstruction]' => 'Something New',
            'investment[estimatedValueEquipment]' => 'Something New',
            'investment[estimatedValueOther]' => 'Something New',
            'investment[approvedValueConstruction]' => 'Something New',
            'investment[approvedValueEquipment]' => 'Something New',
            'investment[approvedValueOther]' => 'Something New',
            'investment[betweenStreets]' => 'Something New',
            'investment[town]' => 'Something New',
            'investment[popularCouncil]' => 'Something New',
            'investment[block]' => 'Something New',
            'investment[district]' => 'Something New',
            'investment[street]' => 'Something New',
            'investment[addressNumber]' => 'Something New',
            'investment[constructor]' => 'Something New',
            'investment[locationZone]' => 'Something New',
            'investment[municipality]' => 'Something New',
        ]);

        self::assertResponseRedirects('/investment/');

        $fixture = $this->investmentRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getWorkName());
        self::assertSame('Something New', $fixture[0]->getInvestmentName());
        self::assertSame('Something New', $fixture[0]->getEstimatedValueConstruction());
        self::assertSame('Something New', $fixture[0]->getEstimatedValueEquipment());
        self::assertSame('Something New', $fixture[0]->getEstimatedValueOther());
        self::assertSame('Something New', $fixture[0]->getApprovedValueConstruction());
        self::assertSame('Something New', $fixture[0]->getApprovedValueEquipment());
        self::assertSame('Something New', $fixture[0]->getApprovedValueOther());
        self::assertSame('Something New', $fixture[0]->getBetweenStreets());
        self::assertSame('Something New', $fixture[0]->getTown());
        self::assertSame('Something New', $fixture[0]->getPopularCouncil());
        self::assertSame('Something New', $fixture[0]->getBlock());
        self::assertSame('Something New', $fixture[0]->getDistrict());
        self::assertSame('Something New', $fixture[0]->getStreet());
        self::assertSame('Something New', $fixture[0]->getAddressNumber());
        self::assertSame('Something New', $fixture[0]->getConstructor());
        self::assertSame('Something New', $fixture[0]->getLocationZone());
        self::assertSame('Something New', $fixture[0]->getMunicipality());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Investment();
        $fixture->setWorkName('Value');
        $fixture->setInvestmentName('Value');
        $fixture->setEstimatedValueConstruction('Value');
        $fixture->setEstimatedValueEquipment('Value');
        $fixture->setEstimatedValueOther('Value');
        $fixture->setApprovedValueConstruction('Value');
        $fixture->setApprovedValueEquipment('Value');
        $fixture->setApprovedValueOther('Value');
        $fixture->setBetweenStreets('Value');
        $fixture->setTown('Value');
        $fixture->setPopularCouncil('Value');
        $fixture->setBlock('Value');
        $fixture->setDistrict('Value');
        $fixture->setStreet('Value');
        $fixture->setAddressNumber('Value');
        $fixture->setConstructor('Value');
        $fixture->setLocationZone('Value');
        $fixture->setMunicipality('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/investment/');
        self::assertSame(0, $this->investmentRepository->count([]));
    }
}
