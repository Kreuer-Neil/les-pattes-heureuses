import AnimalController from '@/actions/App/Http/Controllers/AnimalController';
import AnimalFields, {
    AnimalFieldsHandle,
} from '@/components/animals/animal-fields';
import CustomModal, {
    ModalDescription,
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/modals/custom-modal';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Form } from '@inertiajs/react';
import { Dispatch, SetStateAction, useRef } from 'react';
import { useTranslation } from 'react-i18next';

export default function AnimalsCreate({
    showModal,
    setShowModal,
}: {
    showModal: boolean;
    setShowModal: Dispatch<SetStateAction<boolean>>;
}) {
    const { t } = useTranslation('animals');
    const fieldsRef = useRef<AnimalFieldsHandle>(null);

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
                    fieldsRef.current?.reset();
                    setShowModal(false);
                }}
                className="flex flex-col gap-6"
            >
                {({ errors, processing }) => (
                    <>
                        <FieldSet>
                            <Field>
                                <FieldLabel htmlFor="recovered_at">
                                    {t('create.recoveredAt')}
                                </FieldLabel>
                                <Input
                                    id="recovered_at"
                                    name="recovered_at"
                                    type="date"
                                    aria-invalid={!!errors.recovered_at}
                                />
                                <FieldError
                                    errors={[{ message: errors.recovered_at }]}
                                />
                            </Field>
                        </FieldSet>

                        <AnimalFields ref={fieldsRef} errors={errors} />

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
