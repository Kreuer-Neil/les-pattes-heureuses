import AnimalController from '@/actions/App/Http/Controllers/AnimalController';
import { ItemCombobox } from '@/components/item-combobox';
import CustomModal, {
    ModalDescription,
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/modals/custom-modal';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { useAnimalTaxonomy } from '@/hooks/use-animal-taxonomy';
import { AnimalStatus } from '@/lib/animal-enums';
import { Form } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

const activeStatuses: string[] = [
    AnimalStatus.Available,
    AnimalStatus.Healing,
    AnimalStatus.Pending,
];

export default function AnimalsChangeStatus({
    animalId,
    currentStatusId,
    open,
    onClose,
    onSuccess,
}: {
    animalId: number;
    currentStatusId: string;
    open: boolean;
    onClose: () => void;
    onSuccess: () => void;
}) {
    const { t } = useTranslation('animals');
    const { statusOptions } = useAnimalTaxonomy();

    const options = statusOptions.filter(
        (option) => option.name && activeStatuses.includes(option.name),
    );

    const [statusId, setStatusId] = useState<string | null>(
        options.some((option) => option.value === currentStatusId)
            ? currentStatusId
            : null,
    );

    return (
        <CustomModal showModal={open} onClose={onClose}>
            <ModalHeader>
                <ModalTitle>{t('changeStatus.title')}</ModalTitle>
                <ModalDescription>
                    {t('changeStatus.description')}
                </ModalDescription>
            </ModalHeader>

            <Form
                {...AnimalController.recoverAnimal.form(animalId)}
                onSuccess={onSuccess}
                className="flex flex-col gap-6"
            >
                {({ errors, processing }) => (
                    <>
                        <Field>
                            <FieldLabel htmlFor="animal_status_id">
                                {t('create.status')}
                            </FieldLabel>
                            <ItemCombobox
                                id="animal_status_id"
                                name="animal_status_id"
                                options={options}
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

                        <ModalFooter>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={onClose}
                                disabled={processing}
                            >
                                {t('edit.cancel')}
                            </Button>
                            <Button type="submit" disabled={processing}>
                                {t('changeStatus.submit')}
                            </Button>
                        </ModalFooter>
                    </>
                )}
            </Form>
        </CustomModal>
    );
}