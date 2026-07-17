import AnimalController from '@/actions/App/Http/Controllers/AnimalController';
import { ItemCombobox } from '@/components/item-combobox';
import CustomModal, {
    ModalDescription,
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/modals/custom-modal';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { useAnimalTaxonomy } from '@/hooks/use-animal-taxonomy';
import { Form } from '@inertiajs/react';
import { Dispatch, SetStateAction, useState } from 'react';
import { useTranslation } from 'react-i18next';

export default function AnimalsCreate({
    showModal,
    setShowModal,
}: {
    showModal: boolean;
    setShowModal: Dispatch<SetStateAction<boolean>>;
}) {
    const { t } = useTranslation('animals');
    const {
        statusOptions,
        specieOptions,
        furColorOptions,
        furPatternOptions,
        breedOptionsBySpecie,
    } = useAnimalTaxonomy();

    const [statusId, setStatusId] = useState<string | null>(null);
    const [specieId, setSpecieId] = useState<string | null>(null);
    const [breedId, setBreedId] = useState<string | null>(null);
    const [furColorId, setFurColorId] = useState<string | null>(null);
    const [secondaryFurColorId, setSecondaryFurColorId] = useState<
        string | null
    >(null);
    const [furPatternId, setFurPatternId] = useState<string | null>(null);

    function resetTaxonomySelections() {
        setStatusId(null);
        setSpecieId(null);
        setBreedId(null);
        setFurColorId(null);
        setSecondaryFurColorId(null);
        setFurPatternId(null);
    }

    const breedOptions = breedOptionsBySpecie(specieId);

    const secondaryFurColorOptions = furColorOptions.filter(
        (option) => option.value !== furColorId,
    );

    return (
        <CustomModal showModal={showModal} onClose={() => setShowModal(false)}>
            <ModalHeader>
                <ModalTitle>{t('create.title')}</ModalTitle>
                <ModalDescription>{t('create.description')}</ModalDescription>
            </ModalHeader>

            <Form
                {...AnimalController.store.form()}
                resetOnSuccess
                onSuccess={() => {
                    resetTaxonomySelections();
                    setShowModal(false);
                }}
                className="flex flex-col gap-6"
            >
                {({ errors, processing }) => (
                    <>
                        <FieldSet>
                            <div className="grid grid-cols-2 gap-4">
                                <Field>
                                    <FieldLabel htmlFor="name">
                                        {t('create.name')}
                                    </FieldLabel>
                                    <Input
                                        id="name"
                                        name="name"
                                        placeholder={t(
                                            'create.namePlaceholder',
                                        )}
                                        aria-invalid={!!errors.name}
                                    />
                                    <FieldError
                                        errors={[{ message: errors.name }]}
                                    />
                                </Field>

                                <Field>
                                    <FieldLabel htmlFor="chip">
                                        {t('create.chip')}
                                    </FieldLabel>
                                    <Input
                                        id="chip"
                                        name="chip"
                                        placeholder={t(
                                            'create.chipPlaceholder',
                                        )}
                                        aria-invalid={!!errors.chip}
                                    />
                                    <FieldError
                                        errors={[{ message: errors.chip }]}
                                    />
                                </Field>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <Field>
                                    <FieldLabel htmlFor="gender">
                                        {t('create.gender')}
                                    </FieldLabel>
                                    <select
                                        id="gender"
                                        name="gender"
                                        defaultValue=""
                                        aria-invalid={!!errors.gender}
                                        className="h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 aria-invalid:border-destructive md:text-sm dark:bg-input/30"
                                    >
                                        <option value="" disabled>
                                            {t('create.selectPlaceholder')}
                                        </option>
                                        <option value="M">
                                            {t('gender.M')}
                                        </option>
                                        <option value="F">
                                            {t('gender.F')}
                                        </option>
                                    </select>
                                    <FieldError
                                        errors={[{ message: errors.gender }]}
                                    />
                                </Field>

                                <Field>
                                    <FieldLabel htmlFor="animal_status_id">
                                        {t('create.status')}
                                    </FieldLabel>
                                    <ItemCombobox
                                        id="animal_status_id"
                                        name="animal_status_id"
                                        options={statusOptions}
                                        value={statusId}
                                        onValueChange={setStatusId}
                                        placeholder={t(
                                            'create.selectPlaceholder',
                                        )}
                                        emptyText={t('create.noResults')}
                                        clearable={false}
                                        required
                                        aria-invalid={!!errors.animal_status_id}
                                    />
                                    <FieldError
                                        errors={[
                                            {
                                                message:
                                                    errors.animal_status_id,
                                            },
                                        ]}
                                    />
                                </Field>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <Field>
                                    <FieldLabel htmlFor="specie_id">
                                        {t('create.specie')}
                                    </FieldLabel>
                                    <ItemCombobox
                                        id="specie_id"
                                        name="specie_id"
                                        options={specieOptions}
                                        value={specieId}
                                        onValueChange={(next) => {
                                            setSpecieId(next);
                                            setBreedId(null);
                                        }}
                                        placeholder={t(
                                            'create.selectPlaceholder',
                                        )}
                                        emptyText={t('create.noResults')}
                                        aria-invalid={!!errors.specie_id}
                                    />
                                    <FieldError
                                        errors={[{ message: errors.specie_id }]}
                                    />
                                </Field>

                                <Field>
                                    <FieldLabel htmlFor="breed_id">
                                        {t('create.breed')}
                                    </FieldLabel>
                                    <ItemCombobox
                                        id="breed_id"
                                        name="breed_id"
                                        options={breedOptions}
                                        value={breedId}
                                        onValueChange={setBreedId}
                                        placeholder={t(
                                            'create.selectPlaceholder',
                                        )}
                                        emptyText={t('create.noResults')}
                                        disabled={!specieId}
                                        aria-invalid={!!errors.breed_id}
                                    />
                                    <FieldError
                                        errors={[{ message: errors.breed_id }]}
                                    />
                                </Field>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <Field>
                                    <FieldLabel htmlFor="fur_color_id">
                                        {t('create.furColor')}
                                    </FieldLabel>
                                    <ItemCombobox
                                        id="fur_color_id"
                                        name="fur_color_id"
                                        options={furColorOptions}
                                        value={furColorId}
                                        onValueChange={setFurColorId}
                                        placeholder={t(
                                            'create.selectPlaceholder',
                                        )}
                                        emptyText={t('create.noResults')}
                                        aria-invalid={!!errors.fur_color_id}
                                    />
                                    <FieldError
                                        errors={[
                                            { message: errors.fur_color_id },
                                        ]}
                                    />
                                </Field>

                                <Field>
                                    <FieldLabel htmlFor="secondary_fur_color_id">
                                        {t('create.secondaryFurColor')}
                                    </FieldLabel>
                                    <ItemCombobox
                                        id="secondary_fur_color_id"
                                        name="secondary_fur_color_id"
                                        options={secondaryFurColorOptions}
                                        value={secondaryFurColorId}
                                        onValueChange={setSecondaryFurColorId}
                                        placeholder={t(
                                            'create.selectPlaceholder',
                                        )}
                                        emptyText={t('create.noResults')}
                                        aria-invalid={
                                            !!errors.secondary_fur_color_id
                                        }
                                    />
                                    <FieldError
                                        errors={[
                                            {
                                                message:
                                                    errors.secondary_fur_color_id,
                                            },
                                        ]}
                                    />
                                </Field>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <Field>
                                    <FieldLabel htmlFor="fur_pattern_id">
                                        {t('create.furPattern')}
                                    </FieldLabel>
                                    <ItemCombobox
                                        id="fur_pattern_id"
                                        name="fur_pattern_id"
                                        options={furPatternOptions}
                                        value={furPatternId}
                                        onValueChange={setFurPatternId}
                                        placeholder={t(
                                            'create.selectPlaceholder',
                                        )}
                                        emptyText={t('create.noResults')}
                                        aria-invalid={!!errors.fur_pattern_id}
                                    />
                                    <FieldError
                                        errors={[
                                            {
                                                message: errors.fur_pattern_id,
                                            },
                                        ]}
                                    />
                                </Field>

                                <Field>
                                    <FieldLabel htmlFor="born_at">
                                        {t('create.bornAt')}
                                    </FieldLabel>
                                    <Input
                                        id="born_at"
                                        name="born_at"
                                        type="date"
                                        aria-invalid={!!errors.born_at}
                                    />
                                    <FieldError
                                        errors={[{ message: errors.born_at }]}
                                    />
                                </Field>
                            </div>

                            <Field>
                                <FieldLabel htmlFor="image">
                                    {t('create.image')}
                                </FieldLabel>
                                <Input
                                    id="image"
                                    name="image"
                                    type="url"
                                    placeholder={t('create.imagePlaceholder')}
                                    aria-invalid={!!errors.image}
                                />
                                <FieldError
                                    errors={[{ message: errors.image }]}
                                />
                            </Field>

                            <Field>
                                <FieldLabel htmlFor="personality">
                                    {t('create.personality')}
                                </FieldLabel>
                                <Textarea
                                    id="personality"
                                    name="personality"
                                    placeholder={t(
                                        'create.personalityPlaceholder',
                                    )}
                                    aria-invalid={!!errors.personality}
                                />
                                <FieldError
                                    errors={[{ message: errors.personality }]}
                                />
                            </Field>
                        </FieldSet>

                        <ModalFooter>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setShowModal(false)}
                                disabled={processing}
                            >
                                {t('create.cancel')}
                            </Button>
                            <Button type="submit" disabled={processing}>
                                {t('create.submit')}
                            </Button>
                        </ModalFooter>
                    </>
                )}
            </Form>
        </CustomModal>
    );
}
