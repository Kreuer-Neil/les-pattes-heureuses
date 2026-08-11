import AdoptionRequestController from '@/actions/App/Http/Controllers/AdoptionRequestController';
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
import { Form } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';

export default function AdoptionRequestFormDialog({
    open,
    onClose,
    animals,
}: {
    open: boolean;
    onClose: () => void;
    animals: { id: number; name: string }[];
}) {
    const { t } = useTranslation(['adoption-requests', 'modals']);
    const [animalId, setAnimalId] = useState<string | null>(null);

    const animalOptions = useMemo(
        () =>
            animals.map((animal) => ({
                value: String(animal.id),
                label: animal.name,
            })),
        [animals],
    );

    return (
        <CustomModal showModal={open} onClose={onClose}>
            <ModalHeader>
                <ModalTitle>{t('create.title')}</ModalTitle>
                <ModalDescription>{t('create.description')}</ModalDescription>
            </ModalHeader>

            <Form
                {...AdoptionRequestController.storeManual.form()}
                resetOnSuccess
                onSuccess={() => {
                    setAnimalId(null);
                    onClose();
                }}
                className="flex flex-col gap-6"
            >
                {({ errors, processing }) => (
                    <>
                        <FieldSet className="gap-4">
                            <Field>
                                <FieldLabel htmlFor="animal_id">
                                    {t('create.animal')}
                                </FieldLabel>
                                <ItemCombobox
                                    id="animal_id"
                                    name="animal_id"
                                    options={animalOptions}
                                    value={animalId}
                                    onValueChange={setAnimalId}
                                    placeholder={t('create.animalPlaceholder')}
                                    emptyText={t('create.noResults')}
                                    required
                                    aria-invalid={!!errors.animal_id}
                                />
                                <FieldError
                                    errors={[{ message: errors.animal_id }]}
                                />
                            </Field>

                            <Field>
                                <FieldLabel htmlFor="first_name">
                                    {t('create.firstName')}
                                </FieldLabel>
                                <Input
                                    id="first_name"
                                    name="first_name"
                                    aria-invalid={!!errors.first_name}
                                />
                                <FieldError
                                    errors={[{ message: errors.first_name }]}
                                />
                            </Field>

                            <Field>
                                <FieldLabel htmlFor="last_name">
                                    {t('create.lastName')}
                                </FieldLabel>
                                <Input
                                    id="last_name"
                                    name="last_name"
                                    aria-invalid={!!errors.last_name}
                                />
                                <FieldError
                                    errors={[{ message: errors.last_name }]}
                                />
                            </Field>

                            <Field>
                                <FieldLabel htmlFor="email">
                                    {t('create.email')}
                                </FieldLabel>
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    aria-invalid={!!errors.email}
                                />
                                <FieldError
                                    errors={[{ message: errors.email }]}
                                />
                            </Field>

                            <Field>
                                <FieldLabel htmlFor="other_contact">
                                    {t('create.otherContact')}
                                </FieldLabel>
                                <Input
                                    id="other_contact"
                                    name="other_contact"
                                    placeholder={t(
                                        'create.otherContactPlaceholder',
                                    )}
                                    aria-invalid={!!errors.other_contact}
                                />
                                <FieldError
                                    errors={[{ message: errors.other_contact }]}
                                />
                            </Field>

                            <Field>
                                <FieldLabel htmlFor="content">
                                    {t('create.content')}
                                </FieldLabel>
                                <Textarea
                                    id="content"
                                    name="content"
                                    placeholder={t('create.contentPlaceholder')}
                                    aria-invalid={!!errors.content}
                                />
                                <FieldError
                                    errors={[{ message: errors.content }]}
                                />
                            </Field>
                        </FieldSet>

                        <ModalFooter>
                            <Button
                                type="button"
                                variant="secondary"
                                onClick={onClose}
                            >
                                {t('modals:cancel')}
                            </Button>
                            <Button type="submit" disabled={processing}>
                                {t('actions.add')}
                            </Button>
                        </ModalFooter>
                    </>
                )}
            </Form>
        </CustomModal>
    );
}
