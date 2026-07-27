import CustomModal, {
    ModalDescription,
    ModalHeader,
    ModalTitle,
} from '@/components/modals/custom-modal';
import { Badge } from '@/components/ui/badge';
import { useAnimal } from '@/hooks/use-animal';
import { useAnimalTaxonomy } from '@/hooks/use-animal-taxonomy';
import { useImage } from '@/hooks/use-image-asset';
import { useTranslation } from 'react-i18next';

const statusVariant: Record<string, 'default' | 'secondary' | 'outline'> = {
    available: 'default',
    pending: 'secondary',
    healing: 'secondary',
    adopted: 'outline',
    unknown: 'outline',
};

export default function AnimalsShow({
    animalId,
    onClose,
}: {
    animalId: number | null;
    onClose: () => void;
}) {
    const { t } = useTranslation('animals');
    const { animal, loading, error } = useAnimal(animalId);
    const {
        statusOptions,
        specieOptions,
        furColorOptions,
        breedOptionsBySpecie,
    } = useAnimalTaxonomy();

    const status = animal
        ? statusOptions.find((o) => o.value === String(animal.statusId))
        : undefined;
    const specieLabel = animal
        ? specieOptions.find((o) => o.value === String(animal.specieId))?.label
        : undefined;
    const breedLabel =
        animal?.breedId != null
            ? breedOptionsBySpecie(String(animal.specieId)).find(
                  (o) => o.value === String(animal.breedId),
              )?.label
            : undefined;
    const furColorLabel =
        animal?.furColorId != null
            ? furColorOptions.find((o) => o.value === String(animal.furColorId))
                  ?.label
            : undefined;
    const secondaryFurColorLabel =
        animal?.secondaryFurColorId != null
            ? furColorOptions.find(
                  (o) => o.value === String(animal.secondaryFurColorId),
              )?.label
            : undefined;

    const image = useImage(animal?.image, 'full');

    return (
        <CustomModal
            showModal={animalId !== null}
            onClose={onClose}
            className="sm:max-w-2xl"
        >
            {loading && (
                <p className="text-muted-foreground">{t('show.loading')}</p>
            )}

            {error && <p className="text-destructive">{t('show.error')}</p>}

            {animal && (
                <div className="flex flex-col gap-6 sm:flex-row">
                    {image && (
                        <img
                            src={image.src}
                            srcSet={image.srcSet}
                            sizes="(min-width: 640px) 10rem, 100vw"
                            alt={animal.name}
                            className="h-48 w-full shrink-0 rounded-md object-cover sm:h-auto sm:w-40 sm:self-stretch"
                        />
                    )}

                    <div className="flex min-w-0 flex-1 flex-col gap-4">
                        <ModalHeader className="flex-row items-start gap-4 pe-10">
                            <div className="min-w-0">
                                <ModalTitle>{animal.name}</ModalTitle>
                                <ModalDescription>
                                    {animal.chip}
                                </ModalDescription>
                            </div>
                            {status && (
                                <Badge
                                    className="ml-auto shrink-0"
                                    variant={
                                        statusVariant[status.name ?? ''] ??
                                        'outline'
                                    }
                                >
                                    {status.label}
                                </Badge>
                            )}
                        </ModalHeader>

                        <dl className="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                            <div>
                                <dt className="text-muted-foreground">
                                    {t('show.gender')}
                                </dt>
                                <dd>{t(`gender.${animal.gender}`)}</dd>
                            </div>
                            <div>
                                <dt className="text-muted-foreground">
                                    {t('show.bornAt')}
                                </dt>
                                <dd>{animal.bornAt.toString}</dd>
                            </div>
                            <div>
                                <dt className="text-muted-foreground">
                                    {t('show.specie')}
                                </dt>
                                <dd>
                                    {specieLabel}
                                    {breedLabel && ` · ${breedLabel}`}
                                </dd>
                            </div>
                            {(furColorLabel || secondaryFurColorLabel) && (
                                <div>
                                    <dt className="text-muted-foreground">
                                        {t('show.furColor')}
                                    </dt>
                                    <dd>
                                        {[furColorLabel, secondaryFurColorLabel]
                                            .filter(Boolean)
                                            .join(' · ')}
                                    </dd>
                                </div>
                            )}
                            <div className="col-span-2">
                                <dt className="text-muted-foreground">
                                    {t('show.personality')}
                                </dt>
                                <dd>{animal.personality}</dd>
                            </div>
                            <div className="col-span-2">
                                <dt className="text-muted-foreground">
                                    {t('show.vaccines')}
                                </dt>
                                <dd>
                                    {animal.vaccines.length > 0 ? (
                                        <ul className="list-inside list-disc">
                                            {animal.vaccines.map((vaccine) => (
                                                <li key={vaccine.id}>
                                                    {t(
                                                        `vaccine.${vaccine.vaccineType.name}`,
                                                    )}{' '}
                                                    <span className="text-muted-foreground">
                                                        (
                                                        {new Date(
                                                            vaccine.vaccinatedAt,
                                                        ).toLocaleDateString()}
                                                        )
                                                    </span>
                                                </li>
                                            ))}
                                        </ul>
                                    ) : (
                                        <span className="text-muted-foreground">
                                            {t('show.noVaccines')}
                                        </span>
                                    )}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            )}
        </CustomModal>
    );
}
