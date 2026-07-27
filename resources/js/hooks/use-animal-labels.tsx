import { useAnimalTaxonomy } from '@/hooks/use-animal-taxonomy';
import { IAnimalMiniature } from '@/types';

// Resolves an animal's taxonomy ids into translated display labels,
// so consumers (row, show, ...) don't each re-derive the same lookups.
export function useAnimalLabels(animal: IAnimalMiniature | null | undefined) {
    const {
        statusOptions,
        specieOptions,
        furColorOptions,
        breedOptionsBySpecie,
    } = useAnimalTaxonomy();

    const status = animal
        ? statusOptions.find(
              (option) => option.value === String(animal.statusId),
          )
        : undefined;

    const specieLabel = animal
        ? specieOptions.find(
              (option) => option.value === String(animal.specieId),
          )?.label
        : undefined;

    const breedLabel =
        animal?.breedId != null
            ? breedOptionsBySpecie(String(animal.specieId)).find(
                  (option) => option.value === String(animal.breedId),
              )?.label
            : undefined;

    const furColorLabel =
        animal?.furColorId != null
            ? furColorOptions.find(
                  (option) => option.value === String(animal.furColorId),
              )?.label
            : undefined;

    const secondaryFurColorLabel =
        animal?.secondaryFurColorId != null
            ? furColorOptions.find(
                  (option) =>
                      option.value === String(animal.secondaryFurColorId),
              )?.label
            : undefined;

    return {
        status,
        specieLabel,
        breedLabel,
        furColorLabel,
        secondaryFurColorLabel,
    };
}
