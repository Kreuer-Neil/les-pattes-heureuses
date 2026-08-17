import UserController from '@/actions/App/Http/Controllers/UserController';
import CustomModal, {
    ModalDescription,
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/modals/custom-modal';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel, FieldSet } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { type IUserAccount } from '@/types';
import { Form } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

export default function UserFormDialog({
    open,
    onClose,
    user,
}: {
    open: boolean;
    onClose: () => void;
    user?: IUserAccount | null;
}) {
    const { t } = useTranslation(['users', 'modals']);
    const isEdit = !!user;

    return (
        <CustomModal showModal={open} onClose={onClose}>
            <ModalHeader>
                <ModalTitle>
                    {isEdit ? t('edit.title') : t('create.title')}
                </ModalTitle>
                <ModalDescription>
                    {isEdit ? t('edit.description') : t('create.description')}
                </ModalDescription>
            </ModalHeader>

            <Form
                {...(isEdit
                    ? UserController.update.form(user.id)
                    : UserController.store.form())}
                resetOnSuccess
                onSuccess={onClose}
                className="flex flex-col gap-6"
            >
                {({ errors, processing }) => (
                    <>
                        <FieldSet className="gap-4">
                            <Field>
                                <FieldLabel htmlFor="name">
                                    {t('fields.name')}
                                </FieldLabel>
                                <Input
                                    id="name"
                                    name="name"
                                    defaultValue={user?.name}
                                    placeholder={t('fields.namePlaceholder')}
                                    aria-invalid={!!errors.name}
                                />
                                <FieldError
                                    errors={[{ message: errors.name }]}
                                />
                            </Field>

                            <Field>
                                <FieldLabel htmlFor="email">
                                    {t('fields.email')}
                                </FieldLabel>
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    defaultValue={user?.email}
                                    placeholder={t('fields.emailPlaceholder')}
                                    aria-invalid={!!errors.email}
                                />
                                <FieldError
                                    errors={[{ message: errors.email }]}
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
                                {isEdit
                                    ? t('actions.save')
                                    : t('actions.create')}
                            </Button>
                        </ModalFooter>
                    </>
                )}
            </Form>
        </CustomModal>
    );
}
