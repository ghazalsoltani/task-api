<?php

namespace App\Tests\Service;

use App\DTO\CreateTaskDTO;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires du service — le repository est mocké pour isoler la logique métier.
 * On ne teste pas la base de données ici, uniquement la transformation DTO → Entity.
 */
class TaskServiceTest extends TestCase
{
    private TaskService $taskService;
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->taskService = new TaskService($this->taskRepository);
    }

    public function testCreateTaskReturnsTaskWithCorrectData(): void
    {
        $dto = new CreateTaskDTO(
            title: 'Ma tâche',
            description: 'Description de test'
        );

        $this->taskRepository
            ->expects($this->once())
            ->method('save');

        $task = $this->taskService->createTask($dto);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Ma tâche', $task->getTitle());
        $this->assertEquals('Description de test', $task->getDescription());
        $this->assertFalse($task->isDone());
        $this->assertInstanceOf(\DateTimeImmutable::class, $task->getCreatedAt());
    }

    public function testCreateTaskWithoutDescription(): void
    {
        $dto = new CreateTaskDTO(title: 'Tâche sans description');

        $this->taskRepository
            ->expects($this->once())
            ->method('save');

        $task = $this->taskService->createTask($dto);

        $this->assertNull($task->getDescription());
    }

    // Vérifie que le service délègue bien la récupération au repository
    public function testGetAllTasksDelegatesToRepository(): void
    {
        $this->taskRepository
            ->expects($this->once())
            ->method('findAllOrderedByDate')
            ->willReturn([]);

        $tasks = $this->taskService->getAllTasks();

        $this->assertIsArray($tasks);
        $this->assertEmpty($tasks);
    }
}
