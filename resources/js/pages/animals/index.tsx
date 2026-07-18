import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { AnimalTaxonomyProvider } from '@/hooks/use-animal-taxonomy';
import AppLayout from '@/layouts/app-layout';
import AnimalsCreate from '@/pages/animals/animals-create';
import { IAnimalMiniature, IAnimalTaxonomy } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { ReactNode, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';

type PageProps = {
    animals: Array<IAnimalMiniature>;
    taxonomy: IAnimalTaxonomy;
    hasAccess: boolean;
};

const statusVariant: Record<string, 'default' | 'secondary' | 'outline'> = {
    available: 'default',
    pending: 'secondary',
    healing: 'secondary',
    adopted: 'outline',
    unknown: 'outline',
};

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
    const { animals, taxonomy, hasAccess } = usePage<PageProps>().props;
    const { t } = useTranslation(['animals', 'common']);

    // Binds speciesId's to species, since filters requires both and loading them with the Animals model would be redundant.
    const speciesById = useMemo(
        () => new Map(taxonomy.species.map((specie) => [specie.id, specie])),
        [taxonomy.species],
    );
    const breedsById = useMemo(
        () => new Map(taxonomy.breeds.map((breed) => [breed.id, breed])),
        [taxonomy.breeds],
    );
    const statusesById = useMemo(
        () => new Map(taxonomy.statuses.map((status) => [status.id, status])),
        [taxonomy.statuses],
    );

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
                            <span>{t('Create')}</span>
                        </Button>
                    </div>

                    {hasAccess &&
                        (animals.length > 0 ? (
                            <AnimalsTable>
                                {animals.map((animal) => {
                                    const specie = speciesById.get(
                                        animal.specieId,
                                    );
                                    const breed = animal.breedId
                                        ? breedsById.get(animal.breedId)
                                        : undefined;
                                    const status = statusesById.get(
                                        animal.statusId,
                                    );

                                    return (
                                        <tr key={animal.id}>
                                            <td className="flex items-center gap-3 p-3">
                                                <Avatar>
                                                    {animal.image && (
                                                        <AvatarImage
                                                            src={animal.image}
                                                            alt={animal.name}
                                                        />
                                                    )}
                                                    <AvatarFallback>
                                                        {animal.name
                                                            .slice(0, 2)
                                                            .toUpperCase()}
                                                    </AvatarFallback>
                                                </Avatar>
                                                <span className="font-medium">
                                                    {animal.name}
                                                </span>
                                            </td>
                                            <td className="p-3">
                                                {specie &&
                                                    t(`specie.${specie.name}`)}
                                                {breed && (
                                                    <span className="text-muted-foreground">
                                                        {' '}
                                                        ·{' '}
                                                        {t(
                                                            `breed.${breed.name}`,
                                                        )}
                                                    </span>
                                                )}
                                            </td>
                                            <td className="p-3">
                                                {t(`gender.${animal.gender}`)}
                                            </td>
                                            <td className="p-3 font-mono text-xs">
                                                {animal.chip}
                                            </td>
                                            <td className="p-3">
                                                {status && (
                                                    <Badge
                                                        variant={
                                                            statusVariant[
                                                                status.name
                                                            ] ?? 'outline'
                                                        }
                                                    >
                                                        {t(
                                                            `status.${status.name}`,
                                                        )}
                                                    </Badge>
                                                )}
                                            </td>
                                            <td className="max-w-xs truncate p-3 text-muted-foreground">
                                                {animal.personality}
                                            </td>
                                        </tr>
                                    );
                                })}
                            </AnimalsTable>
                        ) : (
                            <p className="text-muted-foreground">
                                {t('empty')}
                            </p>
                        ))}
                </div>
                <AnimalsCreate
                    showModal={showCreateModal}
                    setShowModal={setShowCreateModal}
                />
            </AnimalTaxonomyProvider>
        </AppLayout>
    );
}
