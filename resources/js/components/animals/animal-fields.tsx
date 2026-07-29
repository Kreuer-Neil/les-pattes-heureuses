import { ItemCombobox } from '@/components/item-combobox';
import { Button } from '@/components/ui/button';
import {
    Field,
    FieldError,
    FieldLabel,
    FieldLegend,
    FieldSet,
} from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { useAnimalTaxonomy } from '@/hooks/use-animal-taxonomy';
import { AnimalStatus } from '@/lib/animal-enums';
import { Minus, Plus } from 'lucide-react';
import { forwardRef, useImperativeHandle, useRef, useState } from 'react';
import { useTranslation } from 'react-i18next';

interface IVaccineEntry {
    key: number;
    vaccineId: string | null;
    date?: string;
}

export interface IAnimalFieldsDefaultValues {
    name?: string;
    chip?: string;
    gender?: 'M' | 'F';
    bornAt?: string;
    statusId?: string | null;
    specieId?: string | null;
    breedId?: string | null;
    furColorId?: string | null;
    secondaryFurColorId?: string | null;
    furPatternId?: string | null;
    personality?: string;
    vaccines?: { vaccineId: string | null; date: string }[];
}

export interface AnimalFieldsHandle {
    reset: () => void;
}

// Field markup shared by the create form and the edit form: same input
// names/ids either way, only the initial values and the wrapping <Form>
// (store vs update) differ between the two callers.
const AnimalFields = forwardRef<
    AnimalFieldsHandle,
    {
        errors: Record<string, string | undefined>;
        defaultValues?: IAnimalFieldsDefaultValues;
    }
>(function AnimalFields({ errors, defaultValues }, ref) {
    const { t } = useTranslation('animals');
    const {
        statusOptions,
        specieOptions,
        furColorOptions,
        furPatternOptions,
        vaccineOptions,
        breedOptionsBySpecie,
    } = useAnimalTaxonomy();

    // Adopted/deceased can't be picked here — those are set through their
    // own dedicated workflows — but an animal already in one of those
    // states must still show its current value.
    const selectableStatusOptions = statusOptions.filter(
        (option) =>
            (option.name !== AnimalStatus.Adopted &&
                option.name !== AnimalStatus.Deceased) ||
            option.value === defaultValues?.statusId,
    );

    const availableStatusId =
        statusOptions.find((option) => option.name === AnimalStatus.Available)
            ?.value ?? null;
    const initialStatusId = defaultValues?.statusId ?? availableStatusId;

    const [statusId, setStatusId] = useState<string | null>(initialStatusId);
    const [specieId, setSpecieId] = useState<string | null>(
        defaultValues?.specieId ?? null,
    );
    const [breedId, setBreedId] = useState<string | null>(
        defaultValues?.breedId ?? null,
    );
    const [furColorId, setFurColorId] = useState<string | null>(
        defaultValues?.furColorId ?? null,
    );
    const [secondaryFurColorId, setSecondaryFurColorId] = useState<
        string | null
    >(defaultValues?.secondaryFurColorId ?? null);
    const [furPatternId, setFurPatternId] = useState<string | null>(
        defaultValues?.furPatternId ?? null,
    );

    const [vaccineEntries, setVaccineEntries] = useState<IVaccineEntry[]>(() =>
        (defaultValues?.vaccines ?? []).map((vaccine, index) => ({
            key: index,
            vaccineId: vaccine.vaccineId,
            date: vaccine.date,
        })),
    );
    const nextVaccineKey = useRef(vaccineEntries.length);

    useImperativeHandle(ref, () => ({
        reset() {
            setStatusId(initialStatusId);
            setSpecieId(defaultValues?.specieId ?? null);
            setBreedId(defaultValues?.breedId ?? null);
            setFurColorId(defaultValues?.furColorId ?? null);
            setSecondaryFurColorId(defaultValues?.secondaryFurColorId ?? null);
            setFurPatternId(defaultValues?.furPatternId ?? null);
            setVaccineEntries(
                (defaultValues?.vaccines ?? []).map((vaccine) => ({
                    key: nextVaccineKey.current++,
                    vaccineId: vaccine.vaccineId,
                    date: vaccine.date,
                })),
            );
        },
    }));

    function addVaccineEntry() {
        setVaccineEntries((entries) => [
            ...entries,
            { key: nextVaccineKey.current++, vaccineId: null },
        ]);
    }

    function removeVaccineEntry(key: number) {
        setVaccineEntries((entries) =>
            entries.filter((entry) => entry.key !== key),
        );
    }

    function setVaccineEntryId(key: number, vaccineId: string | null) {
        setVaccineEntries((entries) =>
            entries.map((entry) =>
                entry.key === key ? { ...entry, vaccineId } : entry,
            ),
        );
    }

    const breedOptions = breedOptionsBySpecie(specieId);

    const secondaryFurColorOptions = furColorOptions.filter(
        (option) => option.value !== furColorId,
    );
    const primaryFurColorOptions = furColorOptions.filter(
        (option) => option.value !== secondaryFurColorId,
    );

    return (
        <>
            {/* Pet base info */}
            <FieldSet className="field-group mt-6 grid gap-4 gap-y-6 sm:grid-cols-2">
                <Field>
                    <FieldLabel htmlFor="name">{t('create.name')}</FieldLabel>
                    <Input
                        id="name"
                        name="name"
                        defaultValue={defaultValues?.name}
                        placeholder={t('create.namePlaceholder')}
                        aria-invalid={!!errors.name}
                    />
                    <FieldError errors={[{ message: errors.name }]} />
                </Field>

                <Field>
                    <FieldLabel htmlFor="chip">{t('create.chip')}</FieldLabel>
                    <Input
                        id="chip"
                        name="chip"
                        defaultValue={defaultValues?.chip}
                        placeholder={t('create.chipPlaceholder')}
                        aria-invalid={!!errors.chip}
                    />
                    <FieldError errors={[{ message: errors.chip }]} />
                </Field>

                <Field>
                    <FieldLabel htmlFor="gender">
                        {t('create.gender')}
                    </FieldLabel>
                    <select
                        id="gender"
                        name="gender"
                        defaultValue={defaultValues?.gender ?? ''}
                        aria-invalid={!!errors.gender}
                        className="h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 aria-invalid:border-destructive md:text-sm dark:bg-input/30"
                    >
                        <option value="" disabled>
                            {t('create.selectPlaceholder')}
                        </option>
                        <option value="M">{t('gender.M')}</option>
                        <option value="F">{t('gender.F')}</option>
                    </select>
                    <FieldError errors={[{ message: errors.gender }]} />
                </Field>

                <Field>
                    <FieldLabel htmlFor="born_at">
                        {t('create.bornAt')}
                    </FieldLabel>
                    <Input
                        id="born_at"
                        name="born_at"
                        type="date"
                        defaultValue={defaultValues?.bornAt}
                        aria-invalid={!!errors.born_at}
                    />
                    <FieldError errors={[{ message: errors.born_at }]} />
                </Field>
            </FieldSet>

            {/* Taxonomy */}
            <FieldSet className="field-group grid gap-4 gap-y-6 sm:grid-cols-2">
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
                        placeholder={t('create.selectPlaceholder')}
                        emptyText={t('create.noResults')}
                        aria-invalid={!!errors.specie_id}
                    />
                    <FieldError errors={[{ message: errors.specie_id }]} />
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
                        placeholder={t('create.selectPlaceholder')}
                        emptyText={t('create.noResults')}
                        disabled={!specieId}
                        aria-invalid={!!errors.breed_id}
                    />
                    <FieldError errors={[{ message: errors.breed_id }]} />
                </Field>

                <Field>
                    <FieldLabel htmlFor="fur_color_id">
                        {t('create.furColor')}
                    </FieldLabel>
                    <ItemCombobox
                        id="fur_color_id"
                        name="fur_color_id"
                        options={primaryFurColorOptions}
                        value={furColorId}
                        onValueChange={setFurColorId}
                        placeholder={t('create.selectPlaceholder')}
                        emptyText={t('create.noResults')}
                        aria-invalid={!!errors.fur_color_id}
                    />
                    <FieldError errors={[{ message: errors.fur_color_id }]} />
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
                        placeholder={t('create.selectPlaceholder')}
                        emptyText={t('create.noResults')}
                        aria-invalid={!!errors.secondary_fur_color_id}
                    />
                    <FieldError
                        errors={[{ message: errors.secondary_fur_color_id }]}
                    />
                </Field>

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
                        placeholder={t('create.selectPlaceholder')}
                        emptyText={t('create.noResults')}
                        aria-invalid={!!errors.fur_pattern_id}
                    />
                    <FieldError errors={[{ message: errors.fur_pattern_id }]} />
                </Field>
            </FieldSet>

            {/* img + personnality + status */}
            <FieldSet>
                <Field>
                    <FieldLabel htmlFor="image">{t('create.image')}</FieldLabel>
                    {/* TODO: preview thumbnail + drag & drop once the basic flow is confirmed working */}
                    <Input
                        id="image"
                        name="image"
                        type="file"
                        accept="image/png, image/jpg, image/jpeg, image/webp, image/gif"
                        aria-invalid={!!errors.image}
                    />
                    <FieldError errors={[{ message: errors.image }]} />
                </Field>

                <Field>
                    <FieldLabel htmlFor="personality">
                        {t('create.personality')}
                    </FieldLabel>
                    <Textarea
                        id="personality"
                        name="personality"
                        defaultValue={defaultValues?.personality}
                        placeholder={t('create.personalityPlaceholder')}
                        aria-invalid={!!errors.personality}
                    />
                    <FieldError errors={[{ message: errors.personality }]} />
                </Field>

                <Field>
                    <FieldLabel htmlFor="animal_status_id">
                        {t('create.status')}
                    </FieldLabel>
                    <ItemCombobox
                        id="animal_status_id"
                        name="animal_status_id"
                        options={selectableStatusOptions}
                        value={statusId}
                        onValueChange={setStatusId}
                        placeholder={t('create.selectPlaceholder')}
                        emptyText={t('create.noResults')}
                        clearable={false}
                        required
                        aria-invalid={!!errors.animal_status_id}
                    />
                    <FieldError
                        errors={[{ message: errors.animal_status_id }]}
                    />
                </Field>
            </FieldSet>

            {/* Vaccines */}
            <FieldSet className="field-group gap-4">
                <FieldLegend variant="label">
                    {t('create.vaccines')}
                </FieldLegend>

                {vaccineEntries.map((entry, index) => (
                    <div key={entry.key} className="flex gap-4">
                        <div className="grid w-full items-end gap-4 sm:grid-cols-2">
                            <Field>
                                <FieldLabel htmlFor={`vaccine_id_${entry.key}`}>
                                    {t('create.vaccine')}
                                </FieldLabel>
                                <ItemCombobox
                                    id={`vaccine_id_${entry.key}`}
                                    name={`vaccines[${index}][id]`}
                                    options={vaccineOptions}
                                    value={entry.vaccineId}
                                    onValueChange={(next) =>
                                        setVaccineEntryId(entry.key, next)
                                    }
                                    placeholder={t('create.selectPlaceholder')}
                                    emptyText={t('create.noResults')}
                                    aria-invalid={
                                        !!errors[`vaccines.${index}.id`]
                                    }
                                />
                                <FieldError
                                    errors={[
                                        {
                                            message:
                                                errors[`vaccines.${index}.id`],
                                        },
                                    ]}
                                />
                            </Field>

                            <Field>
                                <FieldLabel
                                    htmlFor={`vaccine_date_${entry.key}`}
                                >
                                    {t('create.vaccineDate')}
                                </FieldLabel>
                                <Input
                                    id={`vaccine_date_${entry.key}`}
                                    name={`vaccines[${index}][date]`}
                                    type="date"
                                    defaultValue={entry.date}
                                    aria-invalid={
                                        !!errors[`vaccines.${index}.date`]
                                    }
                                />
                                <FieldError
                                    errors={[
                                        {
                                            message:
                                                errors[
                                                    `vaccines.${index}.date`
                                                ],
                                        },
                                    ]}
                                />
                            </Field>
                        </div>

                        <Button
                            type="button"
                            variant="outline"
                            size="icon"
                            onClick={() => removeVaccineEntry(entry.key)}
                            aria-label={t('create.removeVaccine')}
                            className="self-center"
                        >
                            <Minus />
                        </Button>
                    </div>
                ))}

                <Button
                    type="button"
                    variant="outline"
                    onClick={addVaccineEntry}
                    className="self-start"
                >
                    <Plus />
                    {t('create.addVaccine')}
                </Button>
            </FieldSet>
        </>
    );
});

export default AnimalFields;
