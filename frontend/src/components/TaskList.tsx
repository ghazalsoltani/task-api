import React from 'react';
import { Task } from '../types/Task';

interface TaskListProps {
    tasks: Task[];
    isLoading: boolean;
}

// Formatage de la date
const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

/**
 * Affiche la liste des tâches.
 * Gère 3 états : chargement, liste vide, et liste avec données.
 */
const TaskList: React.FC<TaskListProps> = ({ tasks, isLoading }) => {
    if (isLoading) {
        return <p>Chargement des tâches...</p>;
    }

    if (tasks.length === 0) {
        return <p>Aucune tâche pour le moment.</p>;
    }

    return (
        <div>
            <h2>Liste des tâches ({tasks.length})</h2>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {tasks.map((task) => (
                    <li
                        key={task.id}
                        style={{
                            padding: '1rem',
                            marginBottom: '0.5rem',
                            border: '1px solid #ddd',
                            borderRadius: '4px',
                            backgroundColor: task.isDone ? '#f0f0f0' : '#fff',
                        }}
                    >
                        <strong>{task.title}</strong>
                        {task.description && (
                            <p style={{ margin: '0.5rem 0 0', color: '#666' }}>
                                {task.description}
                            </p>
                        )}
                        <small style={{ color: '#999' }}>
                            Créée le {formatDate(task.createdAt)}
                            {task.isDone && ' — ✅ Terminée'}
                        </small>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default TaskList;