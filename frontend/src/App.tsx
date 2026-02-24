import React, { useState, useEffect, useCallback } from 'react';
import TaskForm from './components/TaskForm';
import TaskList from './components/TaskList';
import { Task } from './types/Task';
import { taskApi } from './services/taskApi';

/**
 * Composant racine - orchestre le formulaire et la liste.
 * Le state des tâches est géré ici et transmis aux enfants via props.
 * Pas de Context API car le state ne traverse qu'un seul niveau de composants.
 */
const App: React.FC = () => {
    const [tasks, setTasks] = useState<Task[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const loadTasks = useCallback(async () => {
        try {
            const data = await taskApi.getAll();
            setTasks(data);
            setError(null);
        } catch {
            setError('Impossible de charger les tâches.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        loadTasks();
    }, [loadTasks]);

    // Mise à jour optimiste : la tâche est ajoutée au state local immédiatement
    // sans refaire un appel GET, pour une UX plus réactive
    const handleTaskCreated = (newTask: Task) => {
        setTasks((prev) => [newTask, ...prev]);
    };

    return (
        <div style={{ maxWidth: '600px', margin: '2rem auto', padding: '0 1rem' }}>
            <h1>📋 Gestionnaire de tâches</h1>

            {error && <p style={{ color: 'red' }}>{error}</p>}

            <TaskForm onTaskCreated={handleTaskCreated} />
            <TaskList tasks={tasks} isLoading={isLoading} />
        </div>
    );
};

export default App;
