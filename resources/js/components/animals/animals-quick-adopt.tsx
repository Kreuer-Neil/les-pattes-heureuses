import AdoptionRequestController from '@/actions/App/Http/Controllers/AdoptionRequestController';
import CustomModal, {
    ModalDescription,
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/modals/custom-modal';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Form } from '@inertiajs/react';
import { TriangleAlert } from 'lucide-react';
import { useTranslation } from 'react-i18next';

export default function AnimalsQuickAdopt({
    animalId,
    animalName,
    hasActiveAdoptionRequest,
    open,
    onClose,
    onSuccess,
}: {
    animalId: number;
    animalName: string;
    hasActiveAdoptionRequest: boolean;
    open: boolean;
    onClose: () => void;
    onSuccess: () => void;
}) {
    const { t } = useTranslation(['animals', 'adoption-requests']);

    return (
        <CustomModal showModal={open} onClose={onClose}>
            <ModalHeader>
                <ModalTitle>
                    {t('animals:quickAdopt.title', { animal: animalName })}
                </ModalTitle>
                <ModalDescription>
                    {t('animals:quickAdopt.description')}
                </ModalDescription>
            </ModalHeader>

            {hasActiveAdoptionRequest && (
                <Alert variant="destructive">
                    <TriangleAlert />
                    <AlertDescription>
                        {t('animals:quickAdopt.activeRequestWarning')}
                    </AlertDescription>
                </Alert>
            )}

            <Form
                {...AdoptionRequestController.storeQuickAdopt.form()}
                resetOnSuccess
                onSuccess={onSuccess}
                className="flex flex-col gap-6"
            >
                {({ errors, processing }) => (
                    <>
                        <input
                            type="hidden"
                            name="animal_id"
                            value={animalId}
                            readOnly
                        />

                        <FieldSet className="gap-4">
                            <Field>
                                <FieldLabel htmlFor="first_name">
                                    {t('adoption-requests:create.firstName')}
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
                                    {t('adoption-requests:create.lastName')}
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
                                    {t('adoption-requests:create.email')}
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
                                    {t(
                                        'adoption-requests:create.otherContact',
                                    )}
                                </FieldLabel>
                                <Input
                                    id="other_contact"
                                    name="other_contact"
                                    placeholder={t(
                                        'adoption-requests:create.otherContactPlaceholder',
                                    )}
                                    aria-invalid={!!errors.other_contact}
                                />
                                <FieldError
                                    errors={[
                                        { message: errors.other_contact },
                                    ]}
                                />
                            </Field>

                            <Field>
                                <FieldLabel htmlFor="content">
                                    {t('animals:quickAdopt.content')}
                                </FieldLabel>
                                <Textarea
                                    id="content"
                                    name="content"
                                    placeholder={t(
                                        'animals:quickAdopt.contentPlaceholder',
                                    )}
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
                                disabled={processing}
                            >
                                {t('animals:edit.cancel')}
                            </Button>
                            <Button type="submit" disabled={processing}>
                                {t('animals:quickAdopt.submit')}
                            </Button>
                        </ModalFooter>
                    </>
                )}
            </Form>
        </CustomModal>
    );
}
