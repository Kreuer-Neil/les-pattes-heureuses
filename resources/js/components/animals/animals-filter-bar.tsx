import { ItemCombobox } from '@/components/item-combobox';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useAnimalTaxonomy } from '@/hooks/use-animal-taxonomy';
import { AnimalStatus } from '@/lib/animal-enums';
import { IAnimalFilters } from '@/types';
import { useTranslation } from 'react-i18next';

export const DEFAULT_ANIMAL_FILTERS: IAnimalFilters = {
    status: 'active',
    specie: null,
    breed: null,
    gender: null,
    q: '',
};

export function isDefaultAnimalFilters(filters: IAnimalFilters): boolean {
    return (
        filters.status === DEFAULT_ANIMAL_FILTERS.status &&
        filters.specie === DEFAULT_ANIMAL_FILTERS.specie &&
        filters.breed === DEFAULT_ANIMAL_FILTERS.breed &&
        filters.gender === DEFAULT_ANIMAL_FILTERS.gender &&
        filters.q === DEFAULT_ANIMAL_FILTERS.q
    );
}

const selectClassName =
    'h-9 rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 md:text-sm dark:bg-input/30';

export function AnimalsFilterBar({
    filters,
    onChange,
}: {
    filters: IAnimalFilters;
    onChange: (next: IAnimalFilters) => void;
}) {
    const { t } = useTranslation('animals');
    const { specieOptions, breedOptionsBySpecie } = useAnimalTaxonomy();

    const specieId = filters.specie !== null ? String(filters.specie) : null;
    const breedOptions = breedOptionsBySpecie(specieId);

    function update(patch: Partial<IAnimalFilters>) {
        onChange({ ...filters, ...patch });
    }

    return (
        <div className="flex flex-wrap items-end gap-3">
            <div className="flex flex-col gap-1">
                <label
                    htmlFor="animal-filter-q"
                    className="text-xs text-muted-foreground"
                >
                    {t('filters.search')}
                </label>
                <Input
                    id="animal-filter-q"
                    value={filters.q}
                    onChange={(event) => update({ q: event.target.value })}
                    placeholder={t('filters.searchPlaceholder')}
                    className="w-48"
                />
            </div>

            <div className="flex flex-col gap-1">
                <label
                    htmlFor="animal-filter-status"
                    className="text-xs text-muted-foreground"
                >
                    {t('filters.status.label')}
                </label>
                <select
                    id="animal-filter-status"
                    value={filters.status}
                    onChange={(event) =>
                        update({
                            status: event.target
                                .value as IAnimalFilters['status'],
                        })
                    }
                    className={selectClassName}
                >
                    <option value="active">{t('filters.status.active')}</option>
                    <option value="gone">{t('filters.status.gone')}</option>
                    {Object.values(AnimalStatus).map((status) => (
                        <option key={status} value={status}>
                            {t(`status.${status}`)}
                        </option>
                    ))}
                </select>
            </div>

            <div className="flex flex-col gap-1">
                <label
                    htmlFor="animal-filter-specie"
                    className="text-xs text-muted-foreground"
                >
                    {t('filters.specie')}
                </label>
                <ItemCombobox
                    id="animal-filter-specie"
                    name="specie_filter"
                    options={specieOptions}
                    value={specieId}
                    onValueChange={(next) =>
                        update({
                            specie: next ? Number(next) : null,
                            breed: null,
                        })
                    }
                    placeholder={t('filters.allSpecies')}
                    emptyText={t('create.noResults')}
                />
            </div>

            <div className="flex flex-col gap-1">
                <label
                    htmlFor="animal-filter-breed"
                    className="text-xs text-muted-foreground"
                >
                    {t('filters.breed')}
                </label>
                <ItemCombobox
                    id="animal-filter-breed"
                    name="breed_filter"
                    options={breedOptions}
                    value={
                        filters.breed !== null ? String(filters.breed) : null
                    }
                    onValueChange={(next) =>
                        update({ breed: next ? Number(next) : null })
                    }
                    placeholder={t('filters.allBreeds')}
                    emptyText={t('create.noResults')}
                    disabled={!specieId}
                />
            </div>

            <div className="flex flex-col gap-1">
                <label
                    htmlFor="animal-filter-gender"
                    className="text-xs text-muted-foreground"
                >
                    {t('filters.gender')}
                </label>
                <select
                    id="animal-filter-gender"
                    value={filters.gender ?? ''}
                    onChange={(event) =>
                        update({
                            gender: (event.target.value ||
                                null) as IAnimalFilters['gender'],
                        })
                    }
                    className={selectClassName}
                >
                    <option value="">{t('filters.allGenders')}</option>
                    <option value="M">{t('gender.M')}</option>
                    <option value="F">{t('gender.F')}</option>
                </select>
            </div>

            {!isDefaultAnimalFilters(filters) && (
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => onChange(DEFAULT_ANIMAL_FILTERS)}
                >
                    {t('filters.clear')}
                </Button>
            )}
        </div>
    );
}
