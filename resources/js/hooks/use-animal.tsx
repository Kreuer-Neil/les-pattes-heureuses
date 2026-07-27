import { show } from '@/actions/App/Http/Controllers/AnimalController';
import { IAnimal } from '@/types';
import { useEffect, useState } from 'react';

// Cache that saves animals between modal open/close
const animalCache = new Map<number, IAnimal>();

export function seedAnimalCache(animal: IAnimal) {
    animalCache.set(animal.id, animal);
}

export function useAnimal(id: number | null) {
    const [trackedId, setTrackedId] = useState(id);
    const [animal, setAnimal] = useState<IAnimal | null>(() =>
        id !== null ? (animalCache.get(id) ?? null) : null,
    );
    const [error, setError] = useState<string | null>(null);

    // Reset synchronously during render when the id changes, rather than in
    // the effect below, so there's no stale-animal flash before it re-runs.
    if (id !== trackedId) {
        setTrackedId(id);
        setAnimal(id !== null ? (animalCache.get(id) ?? null) : null);
        setError(null);
    }

    useEffect(() => {
        if (id === null || animalCache.has(id)) {
            return;
        }

        let cancelled = false;

        fetch(show(id).url, { headers: { Accept: 'application/json' } })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                return response.json();
            })
            .then((data: IAnimal) => {
                if (cancelled) return;
                animalCache.set(id, data);
                setAnimal(data);
            })
            .catch((err: Error) => {
                if (!cancelled) setError(err.message);
            });

        return () => {
            cancelled = true;
        };
    }, [id]);

    const loading = id !== null && animal === null && error === null;

    return { animal, loading, error };
}
