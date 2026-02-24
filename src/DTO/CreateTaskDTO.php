<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO pour la création d'une tâche.
 * Sépare les données d'entrée utilisateur de l'entité Doctrine.
 * Seul point de validation d'entrée
 */
class CreateTaskDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
        #[Assert\Length(
            min: 3,
            minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.'
        )]
        public readonly string $title = '',

        public readonly ?string $description = null,
    ) {
    }
}