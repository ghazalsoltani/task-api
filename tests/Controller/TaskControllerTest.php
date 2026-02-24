<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels = teste les endpoints API de bout en bout.
 * Chaque test nettoie la base pour garantir l'isolation.
 */
class TaskControllerTest extends WebTestCase
{
    // Nettoyage de la base avant chaque test pour éviter les interférences entre tests
    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager();
        $em->createQuery('DELETE FROM App\Entity\Task')->execute();
        self::ensureKernelShutdown();
    }

    public function testCreateTaskReturns201(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/tasks', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Faire les courses', 'description' => 'Acheter du lait']));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Faire les courses', $data['title']);
        $this->assertFalse($data['isDone']);
        $this->assertArrayHasKey('createdAt', $data);
    }

    public function testCreateTaskWithoutTitleReturns400(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/tasks', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['description' => 'Sans titre']));

        $this->assertResponseStatusCodeSame(400);
    }

    // Flux complet : création puis listing — vérifie la cohérence de bout en bout
    public function testListTasksReturns200(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/tasks', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['title' => 'Test liste']));

        $client->request('GET', '/api/tasks');

        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(1, $data);
        $this->assertEquals('Test liste', $data[0]['title']);
    }
}