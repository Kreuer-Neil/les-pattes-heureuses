import UserController from '@/actions/App/Http/Controllers/UserController';
import CustomModal, {
    ModalDescription,
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/modals/custom-modal';
import { Button } from '@/components/ui/button';
import { type IUserAccount } from '@/types';
import { Form } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

export default function UserDeleteDialog({
    user,
    onClose,
}: {
    user: IUserAccount | null;
    onClose: () => void;
}) {
    const { t } = useTranslation(['users', 'modals']);

    return (
        <CustomModal showModal={!!user} onClose={onClose} className="max-w-md">
            <ModalHeader>
                <ModalTitle>{t('delete.title')}</ModalTitle>
                <ModalDescription>
                    {user && t('delete.description', { name: user.name })}
                </ModalDescription>
            </ModalHeader>

            {user && (
                <Form
                    {...UserController.destroy.form(user.id)}
                    onSuccess={onClose}
                >
                    {({ processing }) => (
                        <ModalFooter>
                            <Button
                                type="button"
                                variant="secondary"
                                onClick={onClose}
                            >
                                {t('modals:cancel')}
                            </Button>
                            <Button
                                type="submit"
                                variant="destructive"
                                disabled={processing}
                            >
                                {t('actions.delete')}
                            </Button>
                        </ModalFooter>
                    )}
                </Form>
            )}
        </CustomModal>
    );
}
