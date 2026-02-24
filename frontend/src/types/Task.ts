export interface Task {
    id: number;
    title: string;
    description: string | null;
    createdAt: string;
    isDone: boolean;
}

// Payload minimal pour la création - seul title est obligatoire
export interface CreateTaskPayload {
    title: string;
    description?: string;
}

// Structure des erreurs de validation retournées par l'API
export interface ValidationErrors {
    [field: string]: string;
}