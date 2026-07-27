import { show } from '@/actions/App/Http/Controllers/AnimalController';
import { IAnimal } from '@/types';
import { useEffect, useState } from 'react';

// Cache that saves animals between modal open/close
const animalCache = new Map<number, IAnimal>();

export function seedAnimalCache(animal: IAnimal) {
    animalCache.set(animal.id, animal);
}

async function fetchAnimal(id: number): Promise<IAnimal> {
    const response = await fetch(show(id).url, {
        headers: { Accept: 'application/json' },
    });
    if (!response.ok) {
        throw new Error(response.statusText);
    }
    return await response.json();
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

    function loadAnimal(animalId: number, isCancelled: () => boolean = () => false) {
        fetchAnimal(animalId)
            .then((data) => {
                if (isCancelled()) return;
                animalCache.set(animalId, data);
                setAnimal(data);
            })
            .catch((err: Error) => {
                if (!isCancelled()) setError(err.message);
            });
    }

    useEffect(() => {
        if (id === null || animalCache.has(id)) {
            return;
        }

        let cancelled = false;
        loadAnimal(id, () => cancelled);

        return () => {
            cancelled = true;
        };
    }, [id]);

    const loading = id !== null && animal === null && error === null;

    // Called imperatively (e.g. after a successful edit), so it just
    // refetches directly rather than nudging the effect above to re-run.
    function refresh() {
        if (id === null) return;

        animalCache.delete(id);
        loadAnimal(id);
    }

    return { animal, loading, error, refresh };
}
