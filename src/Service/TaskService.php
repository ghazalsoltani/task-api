<?php

namespace App\Service;

use App\DTO\CreateTaskDTO;
use App\Entity\Task;
use App\Repository\TaskRepository;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
    ) {
    }

    // Transforme le DTO en entité et délègue la persistance au repository
    public function createTask(CreateTaskDTO $dto): Task
    {
        $task = new Task();
        $task->setTitle($dto->title);
        $task->setDescription($dto->description);

        $this->taskRepository->save($task);

        return $task;
    }

    /**
     * @return Task[]
     */
    public function getAllTasks(): array
    {
        return $this->taskRepository->findAllOrderedByDate();
    }
}