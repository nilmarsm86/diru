<?php

namespace App\Tests\Controller;

use App\Entity\UrbanRegulation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UrbanRegulationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $urbanRegulationRepository;
    private string $path = '/urban/regulation/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->urbanRegulationRepository = $this->manager->getRepository(UrbanRegulation::class);

        foreach ($this->urbanRegulationRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('UrbanRegulation index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'urban_regulation[code]' => 'Testing',
            'urban_regulation[description]' => 'Testing',
            'urban_regulation[data]' => 'Testing',
            'urban_regulation[measurementUnit]' => 'Testing',
            'urban_regulation[photo]' => 'Testing',
            'urban_regulation[comment]' => 'Testing',
            'urban_regulation[legalReference]' => 'Testing',
            'urban_regulation[type]' => 'Testing',
            'urban_regulation[projects]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->urbanRegulationRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new UrbanRegulation();
        $fixture->setCode('My Title');
        $fixture->setDescription('My Title');
        $fixture->setData('My Title');
        $fixture->setMeasurementUnit('My Title');
        $fixture->setPhoto('My Title');
        $fixture->setComment('My Title');
        $fixture->setLegalReference('My Title');
        $fixture->setType('My Title');
        $fixture->setProjects('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('UrbanRegulation');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new UrbanRegulation();
        $fixture->setCode('Value');
        $fixture->setDescription('Value');
        $fixture->setData('Value');
        $fixture->setMeasurementUnit('Value');
        $fixture->setPhoto('Value');
        $fixture->setComment('Value');
        $fixture->setLegalReference('Value');
        $fixture->setType('Value');
        $fixture->setProjects('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'urban_regulation[code]' => 'Something New',
            'urban_regulation[description]' => 'Something New',
            'urban_regulation[data]' => 'Something New',
            'urban_regulation[measurementUnit]' => 'Something New',
            'urban_regulation[photo]' => 'Something New',
            'urban_regulation[comment]' => 'Something New',
            'urban_regulation[legalReference]' => 'Something New',
            'urban_regulation[type]' => 'Something New',
            'urban_regulation[projects]' => 'Something New',
        ]);

        self::assertResponseRedirects('/urban/regulation/');

        $fixture = $this->urbanRegulationRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getCode());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getData());
        self::assertSame('Something New', $fixture[0]->getMeasurementUnit());
        self::assertSame('Something New', $fixture[0]->getPhoto());
        self::assertSame('Something New', $fixture[0]->getComment());
        self::assertSame('Something New', $fixture[0]->getLegalReference());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getProjects());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new UrbanRegulation();
        $fixture->setCode('Value');
        $fixture->setDescription('Value');
        $fixture->setData('Value');
        $fixture->setMeasurementUnit('Value');
        $fixture->setPhoto('Value');
        $fixture->setComment('Value');
        $fixture->setLegalReference('Value');
        $fixture->setType('Value');
        $fixture->setProjects('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/urban/regulation/');
        self::assertSame(0, $this->urbanRegulationRepository->count([]));
    }
}
