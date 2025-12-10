<?php

namespace App\Tests\Controller;

use App\Entity\SeparateConcept;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SeparateConceptControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $separateConceptRepository;
    private string $path = '/separate/concept/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->separateConceptRepository = $this->manager->getRepository(SeparateConcept::class);

        foreach ($this->separateConceptRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('SeparateConcept index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'separate_concept[type]' => 'Testing',
            'separate_concept[number]' => 'Testing',
            'separate_concept[formula]' => 'Testing',
            'separate_concept[name]' => 'Testing',
            'separate_concept[parent]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->separateConceptRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new SeparateConcept();
        $fixture->setType('My Title');
        $fixture->setNumber('My Title');
        $fixture->setFormula('My Title');
        $fixture->setName('My Title');
        $fixture->setParent('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('SeparateConcept');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new SeparateConcept();
        $fixture->setType('Value');
        $fixture->setNumber('Value');
        $fixture->setFormula('Value');
        $fixture->setName('Value');
        $fixture->setParent('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'separate_concept[type]' => 'Something New',
            'separate_concept[number]' => 'Something New',
            'separate_concept[formula]' => 'Something New',
            'separate_concept[name]' => 'Something New',
            'separate_concept[parent]' => 'Something New',
        ]);

        self::assertResponseRedirects('/separate/concept/');

        $fixture = $this->separateConceptRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getNumber());
        self::assertSame('Something New', $fixture[0]->getFormula());
        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getParent());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new SeparateConcept();
        $fixture->setType('Value');
        $fixture->setNumber('Value');
        $fixture->setFormula('Value');
        $fixture->setName('Value');
        $fixture->setParent('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/separate/concept/');
        self::assertSame(0, $this->separateConceptRepository->count([]));
    }
}
