import React, { useState } from 'react';
import { taskApi } from '../services/taskApi';
import { Task, ValidationErrors } from '../types/Task';

interface TaskFormProps {
    onTaskCreated: (task: Task) => void;
}

/**
 * Formulaire de création de tâche.
 * Gère la soumission vers l'API, l'état de chargement,
 * et l'affichage des erreurs de validation sous chaque champ concerné.
 */
const TaskForm: React.FC<TaskFormProps> = ({ onTaskCreated }) => {
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [errors, setErrors] = useState<ValidationErrors>({});
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});          // Reset des erreurs à chaque soumission
        setIsSubmitting(true);  // Désactive le bouton pour éviter les doubles soumissions

        try {
            const task = await taskApi.create({
                title: title.trim(),
                description: description.trim() || undefined, // Envoie undefined si vide (champ optionnel)
            });

            onTaskCreated(task);  // Remonte la tâche créée au parent (App) pour mise à jour optimiste
            setTitle('');         // Reset du formulaire après succès
            setDescription('');
        } catch (error: unknown) {
            if (error && typeof error === 'object' && 'errors' in error) {
                setErrors((error as { errors: ValidationErrors }).errors);
            } else {
                setErrors({ general: 'Une erreur est survenue.' });
            }
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} style={{ marginBottom: '2rem' }}>
            <h2>Nouvelle tâche</h2>

            <div style={{ marginBottom: '1rem' }}>
                <label htmlFor="title">
                    Titre <span style={{ color: 'red' }}>*</span>
                </label>
                <br />
                <input
                    id="title"
                    type="text"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    placeholder="Ex : Faire les courses"
                    style={{ width: '100%', padding: '0.5rem', fontSize: '1rem' }}
                />
                {errors.title && (
                    <p style={{ color: 'red', margin: '0.25rem 0' }}>{errors.title}</p>
                )}
            </div>

            <div style={{ marginBottom: '1rem' }}>
                <label htmlFor="description">Description</label>
                <br />
                <textarea
                    id="description"
                    value={description}
                    onChange={(e) => setDescription(e.target.value)}
                    placeholder="Ex : Acheter du lait"
                    rows={3}
                    style={{ width: '100%', padding: '0.5rem', fontSize: '1rem' }}
                />
            </div>

            {errors.general && (
                <p style={{ color: 'red' }}>{errors.general}</p>
            )}

            <button
                type="submit"
                disabled={isSubmitting}
                style={{
                    padding: '0.5rem 1.5rem',
                    fontSize: '1rem',
                    cursor: isSubmitting ? 'not-allowed' : 'pointer',
                }}
            >
                {isSubmitting ? 'Création...' : 'Créer la tâche'}
            </button>
        </form>
    );
};

export default TaskForm;
