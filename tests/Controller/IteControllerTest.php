<?php

namespace App\Tests\Controller;

use App\Entity\Ite;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class IteControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    /** @var EntityRepository<Ite> */
    private EntityRepository $iteRepository;
    private string $path = '/ite/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->iteRepository = $this->manager->getRepository(Ite::class);

        foreach ($this->iteRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ite index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'ite[type]' => 'Testing',
            'ite[quality]' => 'Testing',
            'ite[min]' => 'Testing',
            'ite[max]' => 'Testing',
            'ite[yearReference]' => 'Testing',
            'ite[comment]' => 'Testing',
            'ite[sourceAccess]' => 'Testing',
            'ite[measurementUnit]' => 'Testing',
            'ite[source]' => 'Testing',
            'ite[city]' => 'Testing',
            'ite[projectType]' => 'Testing',
        ]);

        self::assertResponseRedirects('/ite');

        self::assertSame(1, $this->iteRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new Ite();
        $fixture->setType('My Title');
        $fixture->setQuality('My Title');
        $fixture->setMin('My Title');
        $fixture->setMax('My Title');
        $fixture->setYearReference('My Title');
        $fixture->setComment('My Title');
        $fixture->setSourceAccess('My Title');
        $fixture->setMeasurementUnit('My Title');
        $fixture->setSource('My Title');
        $fixture->setCity('My Title');
        $fixture->setProjectType('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ite');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new Ite();
        $fixture->setType('Value');
        $fixture->setQuality('Value');
        $fixture->setMin('Value');
        $fixture->setMax('Value');
        $fixture->setYearReference('Value');
        $fixture->setComment('Value');
        $fixture->setSourceAccess('Value');
        $fixture->setMeasurementUnit('Value');
        $fixture->setSource('Value');
        $fixture->setCity('Value');
        $fixture->setProjectType('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'ite[type]' => 'Something New',
            'ite[quality]' => 'Something New',
            'ite[min]' => 'Something New',
            'ite[max]' => 'Something New',
            'ite[yearReference]' => 'Something New',
            'ite[comment]' => 'Something New',
            'ite[sourceAccess]' => 'Something New',
            'ite[measurementUnit]' => 'Something New',
            'ite[source]' => 'Something New',
            'ite[city]' => 'Something New',
            'ite[projectType]' => 'Something New',
        ]);

        self::assertResponseRedirects('/ite');

        $fixture = $this->iteRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getQuality());
        self::assertSame('Something New', $fixture[0]->getMin());
        self::assertSame('Something New', $fixture[0]->getMax());
        self::assertSame('Something New', $fixture[0]->getYearReference());
        self::assertSame('Something New', $fixture[0]->getComment());
        self::assertSame('Something New', $fixture[0]->getSourceAccess());
        self::assertSame('Something New', $fixture[0]->getMeasurementUnit());
        self::assertSame('Something New', $fixture[0]->getSource());
        self::assertSame('Something New', $fixture[0]->getCity());
        self::assertSame('Something New', $fixture[0]->getProjectType());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new Ite();
        $fixture->setType('Value');
        $fixture->setQuality('Value');
        $fixture->setMin('Value');
        $fixture->setMax('Value');
        $fixture->setYearReference('Value');
        $fixture->setComment('Value');
        $fixture->setSourceAccess('Value');
        $fixture->setMeasurementUnit('Value');
        $fixture->setSource('Value');
        $fixture->setCity('Value');
        $fixture->setProjectType('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/ite');
        self::assertSame(0, $this->iteRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
