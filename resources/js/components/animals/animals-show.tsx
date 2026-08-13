import AnimalController from '@/actions/App/Http/Controllers/AnimalController';
import AnimalFields, {
    IAnimalFieldsDefaultValues,
} from '@/components/animals/animal-fields';
import AnimalNotes from '@/components/animals/animal-notes';
import AnimalsChangeStatus from '@/components/animals/animals-change-status';
import AnimalsQuickAdopt from '@/components/animals/animals-quick-adopt';
import CustomModal, {
    ModalDescription,
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/modals/custom-modal';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useAnimal } from '@/hooks/use-animal';
import { useAnimalLabels } from '@/hooks/use-animal-labels';
import { useImage } from '@/hooks/use-image-asset';
import { AnimalStatus } from '@/lib/animal-enums';
import { IAnimal } from '@/types';
import { Form } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

const statusVariant: Record<string, 'default' | 'secondary' | 'outline'> = {
    available: 'default',
    pending: 'secondary',
    healing: 'secondary',
    adopted: 'outline',
    unknown: 'outline',
};

function editDefaultValues(animal: IAnimal): IAnimalFieldsDefaultValues {
    return {
        name: animal.name,
        chip: animal.chip,
        gender: animal.gender,
        bornAt: animal.bornAt.value.slice(0, 10),
        statusId: String(animal.statusId),
        specieId: String(animal.specieId),
        breedId: animal.breedId != null ? String(animal.breedId) : null,
        furColorId:
            animal.furColorId != null ? String(animal.furColorId) : null,
        secondaryFurColorId:
            animal.secondaryFurColorId != null
                ? String(animal.secondaryFurColorId)
                : null,
        furPatternId:
            animal.furPatternId != null ? String(animal.furPatternId) : null,
        personality: animal.personality,
        vaccines: animal.vaccines.map((vaccine) => ({
            vaccineId: String(vaccine.vaccineType.id),
            date: vaccine.vaccinatedAt,
        })),
    };
}

export default function AnimalsShow({
    animalId,
    onClose,
}: {
    animalId: number | null;
    onClose: () => void;
}) {
    const { t } = useTranslation('animals');
    const { animal, loading, error, refresh } = useAnimal(animalId);
    const {
        status,
        specieLabel,
        breedLabel,
        furColorLabel,
        secondaryFurColorLabel,
    } = useAnimalLabels(animal);

    const image = useImage(animal?.image, 'full');

    const [trackedAnimalId, setTrackedAnimalId] = useState(animalId);
    const [isEditing, setIsEditing] = useState(false);
    const [isChangingStatus, setIsChangingStatus] = useState(false);
    const [isQuickAdopting, setIsQuickAdopting] = useState(false);

    // Reset synchronously during render when the id changes, so switching
    // to a different animal never leaves the previous edit session open.
    if (animalId !== trackedAnimalId) {
        setTrackedAnimalId(animalId);
        setIsEditing(false);
        setIsChangingStatus(false);
        setIsQuickAdopting(false);
    }

    return (
        <>
            <CustomModal
                showModal={animalId !== null}
                onClose={onClose}
                className="sm:max-w-2xl"
            >
                {loading && (
                    <p className="text-muted-foreground">{t('show.loading')}</p>
                )}
                {error && <p className="text-destructive">{t('show.error')}</p>}
                {animal && !isEditing && (
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
                            <ModalHeader className="flex-row items-start gap-4">
                                <div className="min-w-0">
                                    <ModalTitle>{animal.name}</ModalTitle>
                                    <ModalDescription>
                                        {animal.chip}
                                    </ModalDescription>
                                </div>
                                {status && (
                                    <Badge
                                        className="ml-auto shrink-0 self-center"
                                        variant={
                                            statusVariant[status.name ?? ''] ??
                                            'outline'
                                        }
                                    >
                                        {status.label}
                                    </Badge>
                                )}
                            </ModalHeader>

                            {animal.hasActiveAdoptionRequest && (
                                <p className="-mt-2 text-sm text-amber-600 dark:text-amber-400">
                                    {t('quickAdopt.activeRequestNotice')}
                                </p>
                            )}

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
                                            {[
                                                furColorLabel,
                                                secondaryFurColorLabel,
                                            ]
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
                                                {animal.vaccines.map(
                                                    (vaccine) => (
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
                                                    ),
                                                )}
                                            </ul>
                                        ) : (
                                            <span className="text-muted-foreground">
                                                {t('show.noVaccines')}
                                            </span>
                                        )}
                                    </dd>
                                </div>
                                <AnimalNotes
                                    animalId={animal.id}
                                    notes={animal.notes}
                                    onRefresh={refresh}
                                />
                            </dl>

                            <ModalFooter className="sm:justify-end">
                                {status &&
                                    status.name === AnimalStatus.Adopted && (
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={() =>
                                                setIsChangingStatus(true)
                                            }
                                        >
                                            {t('changeStatus.trigger')}
                                        </Button>
                                    )}
                                {status &&
                                    status.name !== AnimalStatus.Adopted &&
                                    status.name !== AnimalStatus.Deceased && (
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={() =>
                                                setIsQuickAdopting(true)
                                            }
                                        >
                                            {t('quickAdopt.trigger')}
                                        </Button>
                                    )}
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => setIsEditing(true)}
                                >
                                    {t('show.edit')}
                                </Button>
                            </ModalFooter>
                        </div>
                    </div>
                )}
                {animal && isEditing && (
                    <>
                        <ModalHeader>
                            <ModalTitle>{t('edit.title')}</ModalTitle>
                            <ModalDescription>
                                {t('edit.description')}
                            </ModalDescription>
                        </ModalHeader>

                        <Form
                            {...AnimalController.update.form(animal.id)}
                            onSuccess={() => {
                                refresh();
                                setIsEditing(false);
                            }}
                            className="flex flex-col gap-6"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <AnimalFields
                                        errors={errors}
                                        defaultValues={editDefaultValues(
                                            animal,
                                        )}
                                    />

                                    <ModalFooter>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={() => setIsEditing(false)}
                                            disabled={processing}
                                        >
                                            {t('edit.cancel')}
                                        </Button>
                                        <Button
                                            type="submit"
                                            disabled={processing}
                                        >
                                            {t('edit.submit')}
                                        </Button>
                                    </ModalFooter>
                                </>
                            )}
                        </Form>
                    </>
                )}
            </CustomModal>
            {animal && (
                <AnimalsChangeStatus
                    animalId={animal.id}
                    currentStatusId={String(animal.statusId)}
                    open={isChangingStatus}
                    onClose={() => setIsChangingStatus(false)}
                    onSuccess={() => {
                        refresh();
                        setIsChangingStatus(false);
                    }}
                />
            )}
            {animal && (
                <AnimalsQuickAdopt
                    animalId={animal.id}
                    animalName={animal.name}
                    hasActiveAdoptionRequest={animal.hasActiveAdoptionRequest}
                    open={isQuickAdopting}
                    onClose={() => setIsQuickAdopting(false)}
                    onSuccess={() => {
                        refresh();
                        setIsQuickAdopting(false);
                    }}
                />
            )}
        </>
    );
}
