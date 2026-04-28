<?php

namespace App\Tests\Controller;

use App\Entity\MeasurementUnit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MeasurementUnitControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    /** @var EntityRepository<MeasurementUnit> */
    private EntityRepository $measurementUnitRepository;
    private string $path = '/measurement/unit/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->measurementUnitRepository = $this->manager->getRepository(MeasurementUnit::class);

        foreach ($this->measurementUnitRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('MeasurementUnit index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'measurement_unit[name]' => 'Testing',
            'measurement_unit[code]' => 'Testing',
        ]);

        self::assertResponseRedirects('/measurement/unit');

        self::assertSame(1, $this->measurementUnitRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new MeasurementUnit();
        $fixture->setName('My Title');
        $fixture->setCode('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('MeasurementUnit');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new MeasurementUnit();
        $fixture->setName('Value');
        $fixture->setCode('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'measurement_unit[name]' => 'Something New',
            'measurement_unit[code]' => 'Something New',
        ]);

        self::assertResponseRedirects('/measurement/unit');

        $fixture = $this->measurementUnitRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getCode());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new MeasurementUnit();
        $fixture->setName('Value');
        $fixture->setCode('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/measurement/unit');
        self::assertSame(0, $this->measurementUnitRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
