import { IAnimalTaxonomy } from '@/types';
import { createContext, ReactNode, useContext, useMemo } from 'react';
import { useTranslation } from 'react-i18next';

export interface ITaxonomyOption {
    value: string;
    label: string;
    swatch?: string;
    name?: string;
}

interface IAnimalTaxonomyOptions {
    statusOptions: ITaxonomyOption[];
    specieOptions: ITaxonomyOption[];
    furColorOptions: ITaxonomyOption[];
    furPatternOptions: ITaxonomyOption[];
    vaccineOptions: ITaxonomyOption[];
    breedOptionsBySpecie: (specieId: string | null) => ITaxonomyOption[];
}

// Lets any form field under the provider (specie/breed/status/fur pickers, ...)
// pull ready-to-render, translated options without re-deriving them itself or
// threading the raw taxonomy prop through every level.
const AnimalTaxonomyContext = createContext<IAnimalTaxonomyOptions | null>(
    null,
);

export function AnimalTaxonomyProvider({
    taxonomy,
    children,
}: {
    taxonomy: IAnimalTaxonomy;
    children: ReactNode;
}) {
    const { t } = useTranslation('animals');

    // Memoized because this object is the context value: consumers only need to
    // re-render when the taxonomy or language actually changes, not on every
    // render of whatever happens to sit above the provider.
    const options = useMemo<IAnimalTaxonomyOptions>(() => {
        // Breeds are the only taxonomy tied to another (specie), so they can't be
        // exposed as a flat list — the specie isn't known until a consumer picks
        // one. Map once here, filter lazily per call below.
        const breedOptions = taxonomy.breeds.map((breed) => ({
            value: String(breed.id),
            label: t(`breed.${breed.name}`),
            specieId: String(breed.specieId),
        }));

        return {
            statusOptions: taxonomy.statuses.map((status) => ({
                value: String(status.id),
                label: t(`status.${status.name}`),
                name: status.name,
            })),
            specieOptions: taxonomy.species.map((specie) => ({
                value: String(specie.id),
                label: t(`specie.${specie.name}`),
            })),
            furColorOptions: taxonomy.furColors.map((furColor) => ({
                value: String(furColor.id),
                label: t(`furColor.${furColor.name}`),
                swatch: furColor.color,
            })),
            furPatternOptions: taxonomy.furPatterns.map((furPattern) => ({
                value: String(furPattern.id),
                label: t(`furPattern.${furPattern.name}`),
            })),
            vaccineOptions: taxonomy.vaccines.map((vaccine) => ({
                value: String(vaccine.id),
                label: t(`vaccine.${vaccine.name}`),
            })),
            breedOptionsBySpecie: (specieId) =>
                breedOptions
                    // !specieId is if filtering directly by breedOption instead of a first-step specie filtering.
                    // A Claude suggestion that might be useful later.
                    .filter(
                        (breed) => /*!specieId ||*/ breed.specieId === specieId,
                    )
                    .map(({ value, label }) => ({ value, label })),
        };
    }, [taxonomy, t]);

    return (
        <AnimalTaxonomyContext.Provider value={options}>
            {children}
        </AnimalTaxonomyContext.Provider>
    );
}

export function useAnimalTaxonomy() {
    const context = useContext(AnimalTaxonomyContext);

    if (!context) {
        throw new Error(
            'useAnimalTaxonomy must be used within an AnimalTaxonomyProvider',
        );
    }

    return context;
}
