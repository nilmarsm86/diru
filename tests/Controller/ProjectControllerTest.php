<?php

namespace App\Tests\Controller;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProjectControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $proyectRepository;
    private string $path = '/project/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->proyectRepository = $this->manager->getRepository(Project::class);

        foreach ($this->proyectRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Project index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'project[type]' => 'Testing',
            'project[state]' => 'Testing',
            'project[stopReason]' => 'Testing',
            'project[hasOccupiedArea]' => 'Testing',
            'project[registerAt]' => 'Testing',
            'project[stoppedAt]' => 'Testing',
            'project[canceledAt]' => 'Testing',
            'project[initiatedAt]' => 'Testing',
            'project[completedDiagnosticStatusAt]' => 'Testing',
            'project[urbanRregulationAt]' => 'Testing',
            'project[designAt]' => 'Testing',
            'project[comment]' => 'Testing',
            'project[draftsmans]' => 'Testing',
            'project[client]' => 'Testing',
            'project[investment]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->proyectRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Project();
        $fixture->setType('My Title');
        $fixture->setState('My Title');
        $fixture->setStopReason('My Title');
        $fixture->setHasOccupiedArea('My Title');
        $fixture->setRegisterAt('My Title');
        $fixture->setStoppedAt('My Title');
        $fixture->setCanceledAt('My Title');
        $fixture->setInitiatedAt('My Title');
        $fixture->setCompletedDiagnosticStatusAt('My Title');
        $fixture->setUrbanRregulationAt('My Title');
        $fixture->setDesignAt('My Title');
        $fixture->setComment('My Title');
        $fixture->setDraftsmans('My Title');
        $fixture->setClient('My Title');
        $fixture->setInvestment('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Project');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Project();
        $fixture->setType('Value');
        $fixture->setState('Value');
        $fixture->setStopReason('Value');
        $fixture->setHasOccupiedArea('Value');
        $fixture->setRegisterAt('Value');
        $fixture->setStoppedAt('Value');
        $fixture->setCanceledAt('Value');
        $fixture->setInitiatedAt('Value');
        $fixture->setCompletedDiagnosticStatusAt('Value');
        $fixture->setUrbanRregulationAt('Value');
        $fixture->setDesignAt('Value');
        $fixture->setComment('Value');
        $fixture->setDraftsmans('Value');
        $fixture->setClient('Value');
        $fixture->setInvestment('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'project[type]' => 'Something New',
            'project[state]' => 'Something New',
            'project[stopReason]' => 'Something New',
            'project[hasOccupiedArea]' => 'Something New',
            'project[registerAt]' => 'Something New',
            'project[stoppedAt]' => 'Something New',
            'project[canceledAt]' => 'Something New',
            'project[initiatedAt]' => 'Something New',
            'project[completedDiagnosticStatusAt]' => 'Something New',
            'project[urbanRregulationAt]' => 'Something New',
            'project[designAt]' => 'Something New',
            'project[comment]' => 'Something New',
            'project[draftsmans]' => 'Something New',
            'project[client]' => 'Something New',
            'project[investment]' => 'Something New',
        ]);

        self::assertResponseRedirects('/project/');

        $fixture = $this->proyectRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getStopReason());
        self::assertSame('Something New', $fixture[0]->getHasOccupiedArea());
        self::assertSame('Something New', $fixture[0]->getRegisterAt());
        self::assertSame('Something New', $fixture[0]->getStoppedAt());
        self::assertSame('Something New', $fixture[0]->getCanceledAt());
        self::assertSame('Something New', $fixture[0]->getInitiatedAt());
        self::assertSame('Something New', $fixture[0]->getCompletedDiagnosticStatusAt());
        self::assertSame('Something New', $fixture[0]->getUrbanRregulationAt());
        self::assertSame('Something New', $fixture[0]->getDesignAt());
        self::assertSame('Something New', $fixture[0]->getComment());
        self::assertSame('Something New', $fixture[0]->getDraftsmans());
        self::assertSame('Something New', $fixture[0]->getClient());
        self::assertSame('Something New', $fixture[0]->getInvestment());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Project();
        $fixture->setType('Value');
        $fixture->setState('Value');
        $fixture->setStopReason('Value');
        $fixture->setHasOccupiedArea('Value');
        $fixture->setRegisterAt('Value');
        $fixture->setStoppedAt('Value');
        $fixture->setCanceledAt('Value');
        $fixture->setInitiatedAt('Value');
        $fixture->setCompletedDiagnosticStatusAt('Value');
        $fixture->setUrbanRregulationAt('Value');
        $fixture->setDesignAt('Value');
        $fixture->setComment('Value');
        $fixture->setDraftsmans('Value');
        $fixture->setClient('Value');
        $fixture->setInvestment('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/project/');
        self::assertSame(0, $this->proyectRepository->count([]));
    }
}
