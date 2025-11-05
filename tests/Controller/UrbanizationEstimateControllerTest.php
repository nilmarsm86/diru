<?php

namespace App\Tests\Controller;

use App\Entity\UrbanizationEstimate;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UrbanizationEstimateControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $urbanizationEstimateRepository;
    private string $path = '/urbanization/estimate/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->urbanizationEstimateRepository = $this->manager->getRepository(UrbanizationEstimate::class);

        foreach ($this->urbanizationEstimateRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('UrbanizationEstimate index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'urbanization_estimate[concept]' => 'Testing',
            'urbanization_estimate[amount]' => 'Testing',
            'urbanization_estimate[MeasurementUnit]' => 'Testing',
            'urbanization_estimate[price]' => 'Testing',
            'urbanization_estimate[quantity]' => 'Testing',
            'urbanization_estimate[comment]' => 'Testing',
            'urbanization_estimate[building]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->urbanizationEstimateRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new UrbanizationEstimate();
        $fixture->setConcept('My Title');
        $fixture->setAmount('My Title');
        $fixture->setMeasurementUnit('My Title');
        $fixture->setPrice('My Title');
        $fixture->setQuantity('My Title');
        $fixture->setComment('My Title');
        $fixture->setBuilding('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('UrbanizationEstimate');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new UrbanizationEstimate();
        $fixture->setConcept('Value');
        $fixture->setAmount('Value');
        $fixture->setMeasurementUnit('Value');
        $fixture->setPrice('Value');
        $fixture->setQuantity('Value');
        $fixture->setComment('Value');
        $fixture->setBuilding('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'urbanization_estimate[concept]' => 'Something New',
            'urbanization_estimate[amount]' => 'Something New',
            'urbanization_estimate[MeasurementUnit]' => 'Something New',
            'urbanization_estimate[price]' => 'Something New',
            'urbanization_estimate[quantity]' => 'Something New',
            'urbanization_estimate[comment]' => 'Something New',
            'urbanization_estimate[building]' => 'Something New',
        ]);

        self::assertResponseRedirects('/urbanization/estimate/');

        $fixture = $this->urbanizationEstimateRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getConcept());
        self::assertSame('Something New', $fixture[0]->getAmount());
        self::assertSame('Something New', $fixture[0]->getMeasurementUnit());
        self::assertSame('Something New', $fixture[0]->getPrice());
        self::assertSame('Something New', $fixture[0]->getQuantity());
        self::assertSame('Something New', $fixture[0]->getComment());
        self::assertSame('Something New', $fixture[0]->getBuilding());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new UrbanizationEstimate();
        $fixture->setConcept('Value');
        $fixture->setAmount('Value');
        $fixture->setMeasurementUnit('Value');
        $fixture->setPrice('Value');
        $fixture->setQuantity('Value');
        $fixture->setComment('Value');
        $fixture->setBuilding('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/urbanization/estimate/');
        self::assertSame(0, $this->urbanizationEstimateRepository->count([]));
    }
}
