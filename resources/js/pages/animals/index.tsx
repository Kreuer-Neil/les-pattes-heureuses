import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/app-layout';
import { IAnimalFilters, IAnimalMiniature } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useMemo } from 'react';
import { useTranslation } from 'react-i18next';

type PageProps = {
    animals: Array<IAnimalMiniature>;
    filters: IAnimalFilters;
    hasAccess: boolean;
};

const statusVariant: Record<string, 'default' | 'secondary' | 'outline'> = {
    available: 'default',
    pending: 'secondary',
    healing: 'secondary',
    adopted: 'outline',
    unknown: 'outline',
};

export default function AnimalsIndex() {
    const { animals, filters, hasAccess } = usePage<PageProps>().props;
    const { t } = useTranslation(['animals', 'common']);

    // Binds speciesId's to species, since filters requires both and loading them with the Animals model would be redundant.
    const speciesById = useMemo(
        () => new Map(filters.species.map((specie) => [specie.id, specie])),
        [filters.species],
    );
    const breedsById = useMemo(
        () => new Map(filters.breeds.map((breed) => [breed.id, breed])),
        [filters.breeds],
    );
    const statusesById = useMemo(
        () => new Map(filters.statuses.map((status) => [status.id, status])),
        [filters.statuses],
    );

    return (
        <AppLayout>
            <Head
                title={t('common:app_name') + ' – ' + t('common:nav_animals')}
            />

            <div className="flex flex-col gap-4 p-4">
                <h1 className="text-xl font-semibold">{t('our_pets')}</h1>

                {hasAccess &&
                    (animals.length > 0 ? (
                        <div className="overflow-x-auto rounded-md border">
                            <table className="w-full text-sm">
                                <thead className="bg-muted/50 text-left">
                                    <tr>
                                        <th className="p-3 font-medium">
                                            {t('table.name')}
                                        </th>
                                        <th className="p-3 font-medium">
                                            {t('table.specie')}
                                        </th>
                                        <th className="p-3 font-medium">
                                            {t('table.gender')}
                                        </th>
                                        <th className="p-3 font-medium">
                                            {t('table.chip')}
                                        </th>
                                        <th className="p-3 font-medium">
                                            {t('table.status')}
                                        </th>
                                        <th className="p-3 font-medium">
                                            {t('table.personality')}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y">
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
                                                                src={
                                                                    animal.image
                                                                }
                                                                alt={
                                                                    animal.name
                                                                }
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
                                                        t(
                                                            `specie.${specie.name}`,
                                                        )}
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
                                                    {t(
                                                        `gender.${animal.gender}`,
                                                    )}
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
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <p className="text-muted-foreground">{t('empty')}</p>
                    ))}
            </div>
        </AppLayout>
    );
}
