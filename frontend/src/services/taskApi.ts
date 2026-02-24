import { CreateTaskPayload, Task } from '../types/Task';

// URL externalisée
const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api';

/**
 * Service API centralisé — isole les appels HTTP des composants React.
 * Les composants n'ont pas à connaître les détails de fetch().
 */
export const taskApi = {
    async getAll(): Promise<Task[]> {
        const response = await fetch(`${API_BASE_URL}/tasks`);

        if (!response.ok) {
            throw new Error('Erreur lors de la récupération des tâches');
        }

        return response.json();
    },

    async create(payload: CreateTaskPayload): Promise<Task> {
        const response = await fetch(`${API_BASE_URL}/tasks`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });

        const data = await response.json();

        // En cas d'erreur 400, on lance les données d'erreur pour que le composant les affiche
        if (!response.ok) {
            throw data;
        }

        return data;
    },
};
