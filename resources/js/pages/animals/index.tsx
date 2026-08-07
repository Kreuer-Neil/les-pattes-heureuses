import AnimalController from '@/actions/App/Http/Controllers/AnimalController';
import AnimalsCreate from '@/components/animals/animals-create';
import {
    AnimalsFilterBar,
    isDefaultAnimalFilters,
} from '@/components/animals/animals-filter-bar';
import AnimalRow from '@/components/animals/animals-row';
import AnimalsShow from '@/components/animals/animals-show';
import { Button } from '@/components/ui/button';
import { seedAnimalCache } from '@/hooks/use-animal';
import { AnimalTaxonomyProvider } from '@/hooks/use-animal-taxonomy';
import { useDebouncedValue } from '@/hooks/use-debounced-value';
import AppLayout from '@/layouts/app-layout';
import {
    IAnimal,
    IAnimalFilters,
    IAnimalMiniature,
    IAnimalTaxonomy,
} from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { ReactNode, useEffect, useRef, useState } from 'react';
import { useTranslation } from 'react-i18next';

type PageProps = {
    animals: Array<IAnimalMiniature>;
    taxonomy: IAnimalTaxonomy;
    hasAccess: boolean;
    filters: IAnimalFilters;
    selectedAnimal?: IAnimal;
};

function parseAnimalIdFromPath(pathname: string): number | null {
    const match = pathname.match(/\/animals\/(\d+)/);
    return match ? Number(match[1]) : null;
}

function AnimalsTable({ children }: { children: ReactNode }) {
    const { t } = useTranslation('animals');

    return (
        <div className="overflow-x-auto rounded-md border">
            <table className="w-full text-sm">
                <thead className="bg-muted/50 text-left">
                    <tr>
                        <th className="p-3 font-medium">{t('table.name')}</th>
                        <th className="p-3 font-medium">{t('table.specie')}</th>
                        <th className="p-3 font-medium">{t('table.gender')}</th>
                        <th className="p-3 font-medium">{t('table.chip')}</th>
                        <th className="p-3 font-medium">{t('table.status')}</th>
                        <th className="p-3 font-medium">
                            {t('table.personality')}
                        </th>
                    </tr>
                </thead>
                <tbody className="divide-y">{children}</tbody>
            </table>
        </div>
    );
}

export default function AnimalsIndex() {
    const {
        animals,
        taxonomy,
        hasAccess,
        filters: initialFilters,
        selectedAnimal,
    } = usePage<PageProps>().props;
    const { t } = useTranslation(['animals', 'common']);

    const [filters, setFilters] = useState<IAnimalFilters>(initialFilters);
    const debouncedSearch = useDebouncedValue(filters.q, 350);
    const isFirstRender = useRef(true);

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }

        const params: Record<string, string> = {};
        if (filters.status !== 'active') {
            params.status_filter = filters.status;
        }
        if (filters.specie !== null) {
            params.specie = String(filters.specie);
        }
        if (filters.breed !== null) {
            params.breed = String(filters.breed);
        }
        if (filters.gender !== null) {
            params.gender = filters.gender;
        }
        if (debouncedSearch !== '') {
            params.q = debouncedSearch;
        }

        router.get(AnimalController.index().url, params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['animals', 'filters'],
        });
    }, [
        filters.status,
        filters.specie,
        filters.breed,
        filters.gender,
        debouncedSearch,
    ]);

    const [selectedAnimalId, setSelectedAnimalId] = useState<number | null>(
        () =>
            selectedAnimal?.id ??
            parseAnimalIdFromPath(window.location.pathname),
    );

    useEffect(() => {
        if (selectedAnimal) {
            seedAnimalCache(selectedAnimal);
        }
    }, [selectedAnimal]);

    useEffect(() => {
        function handlePopState() {
            setSelectedAnimalId(
                parseAnimalIdFromPath(window.location.pathname),
            );
        }

        window.addEventListener('popstate', handlePopState);
        return () => window.removeEventListener('popstate', handlePopState);
    }, []);

    function openAnimal(id: number) {
        window.history.pushState(null, '', AnimalController.show(id).url);
        setSelectedAnimalId(id);
    }

    function closeAnimal() {
        window.history.pushState(null, '', AnimalController.index().url);
        setSelectedAnimalId(null);
    }

    const [showCreateModal, setShowCreateModal] = useState<boolean>(false);

    return (
        <AppLayout>
            <AnimalTaxonomyProvider taxonomy={taxonomy}>
                <Head
                    // nav_animals = "Our pets"
                    title={
                        t('common:app_name') + ' – ' + t('common:nav_animals')
                    }
                />

                <div className="flex flex-col gap-4 p-4">
                    <div className="flex items-end">
                        <h1 className="text-xl font-semibold">
                            {t('our_pets')}
                        </h1>
                        <Button
                            className="ml-auto"
                            onClick={() => setShowCreateModal(true)}
                        >
                            <Plus />
                            <span>{t('create_button')}</span>
                        </Button>
                    </div>

                    {hasAccess && (
                        <>
                            <AnimalsFilterBar
                                filters={filters}
                                onChange={setFilters}
                            />

                            {animals.length > 0 ? (
                                <AnimalsTable>
                                    {animals.map((animal) => (
                                        <AnimalRow
                                            key={animal.id}
                                            animal={animal}
                                            onClick={() =>
                                                openAnimal(animal.id)
                                            }
                                        />
                                    ))}
                                </AnimalsTable>
                            ) : (
                                <p className="text-muted-foreground">
                                    {t(
                                        isDefaultAnimalFilters(filters)
                                            ? 'empty'
                                            : 'filters.noResults',
                                    )}
                                </p>
                            )}
                        </>
                    )}
                </div>
                <AnimalsCreate
                    showModal={showCreateModal}
                    setShowModal={setShowCreateModal}
                />
                <AnimalsShow
                    animalId={selectedAnimalId}
                    onClose={closeAnimal}
                />
            </AnimalTaxonomyProvider>
        </AppLayout>
    );
}
