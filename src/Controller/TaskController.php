<?php

namespace App\Controller;

use App\DTO\CreateTaskDTO;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controller fin — son rôle se limite à : recevoir, valider, déléguer, répondre.
 * Aucune logique métier ici. Tout est dans TaskService.
 */
#[Route('/api')]
class TaskController extends AbstractController
{
    // Injection par constructeur avec readonly — méthode recommandée en Symfony
    public function __construct(
        private readonly TaskService $taskService,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/tasks', name: 'api_tasks_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // 1. Décoder le JSON
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json(
                ['error' => 'JSON invalide.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // 2. Hydrater le DTO
        $dto = new CreateTaskDTO(
            title: $data['title'] ?? '',
            description: $data['description'] ?? null,
        );

        // 3. Valider
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            // Formater les erreurs par champ pour un affichage frontend précis
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(
                ['errors' => $errorMessages],
                Response::HTTP_BAD_REQUEST
            );
        }

        // 4. Déléguer la création au service métier
        $task = $this->taskService->createTask($dto);

        // 5. Répondre avec 201 Created
        return $this->json($task, Response::HTTP_CREATED, [], [
            'groups' => ['task:read'],
        ]);
    }

    #[Route('/tasks', name: 'api_tasks_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $tasks = $this->taskService->getAllTasks();

        return $this->json($tasks, Response::HTTP_OK, [], [
            'groups' => ['task:read'],
        ]);
    }
}
