import AnimalController from '@/actions/App/Http/Controllers/AnimalController';
import { Badge } from '@/components/ui/badge';
import { useImage } from '@/hooks/use-image-asset';
import { statusVariant } from '@/lib/animal-enums';
import { IDashboardAnimal } from '@/types';
import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

function RecentAnimalCard({ animal }: { animal: IDashboardAnimal }) {
    const { t } = useTranslation('animals');
    const image = useImage(animal.image, 'full');

    return (
        <Link
            href={AnimalController.show(animal.id).url}
            className="group flex flex-col gap-2 rounded-lg border border-sidebar-border/70 p-2 transition-colors hover:bg-accent/50 dark:border-sidebar-border"
        >
            <div className="aspect-square w-full overflow-hidden rounded-md bg-muted">
                {image ? (
                    <img
                        src={image.src}
                        srcSet={image.srcSet}
                        alt={animal.name}
                        className="size-full object-cover transition-transform group-hover:scale-105"
                    />
                ) : (
                    <div className="flex size-full items-center justify-center text-2xl font-medium text-muted-foreground">
                        {animal.name.slice(0, 2).toUpperCase()}
                    </div>
                )}
            </div>
            <div className="flex items-center justify-between gap-2">
                <span className="truncate text-sm font-medium">
                    {animal.name}
                </span>
                <Badge
                    variant={statusVariant[animal.statusName] ?? 'outline'}
                    className="shrink-0"
                >
                    {t(`status.${animal.statusName}`)}
                </Badge>
            </div>
            <span className="truncate text-xs text-muted-foreground">
                {t(`specie.${animal.specieName}`)}
            </span>
        </Link>
    );
}

export function RecentAnimalsWidget({ animals }: { animals: IDashboardAnimal[] }) {
    const { t } = useTranslation('dashboard');

    return (
        <div className="flex flex-col gap-3 rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
            <div className="flex items-center justify-between">
                <h2 className="font-medium">{t('recentlyAdded.title')}</h2>
                <Link
                    href={AnimalController.index().url}
                    className="text-right text-sm text-muted-foreground hover:underline"
                >
                    {t('recentlyAdded.viewAll')}
                </Link>
            </div>

            {animals.length > 0 ? (
                <div className="grid grid-cols-2 gap-3 @xl:grid-cols-3 @3xl:grid-cols-4">
                    {animals.map((animal) => (
                        <RecentAnimalCard key={animal.id} animal={animal} />
                    ))}
                </div>
            ) : (
                <p className="text-sm text-muted-foreground">
                    {t('recentlyAdded.empty')}
                </p>
            )}
        </div>
    );
}
