<?php

namespace App\Tests\Controller;

use App\Entity\ProjectUrbanRegulation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProjectUrbanRegulationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $projectUrbanRegulationRepository;
    private string $path = '/project/urban/regulation/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->projectUrbanRegulationRepository = $this->manager->getRepository(ProjectUrbanRegulation::class);

        foreach ($this->projectUrbanRegulationRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ProjectUrbanRegulation index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'project_urban_regulation[data]' => 'Testing',
            'project_urban_regulation[urbanRegulation]' => 'Testing',
            'project_urban_regulation[project]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->projectUrbanRegulationRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new ProjectUrbanRegulation();
        $fixture->setData('My Title');
        $fixture->setUrbanRegulation('My Title');
        $fixture->setProject('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ProjectUrbanRegulation');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new ProjectUrbanRegulation();
        $fixture->setData('Value');
        $fixture->setUrbanRegulation('Value');
        $fixture->setProject('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'project_urban_regulation[data]' => 'Something New',
            'project_urban_regulation[urbanRegulation]' => 'Something New',
            'project_urban_regulation[project]' => 'Something New',
        ]);

        self::assertResponseRedirects('/project/urban/regulation/');

        $fixture = $this->projectUrbanRegulationRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getData());
        self::assertSame('Something New', $fixture[0]->getUrbanRegulation());
        self::assertSame('Something New', $fixture[0]->getProject());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new ProjectUrbanRegulation();
        $fixture->setData('Value');
        $fixture->setUrbanRegulation('Value');
        $fixture->setProject('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/project/urban/regulation/');
        self::assertSame(0, $this->projectUrbanRegulationRepository->count([]));
    }
}
