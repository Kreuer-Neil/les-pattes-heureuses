import AdopterProfileController from '@/actions/App/Http/Controllers/AdopterProfileController';
import InputError from '@/components/input-error';
import CustomModal, {
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/modals/custom-modal';
import { Button } from '@/components/ui/button';
import { Field } from '@/components/ui/field';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import adoptionRequests from '@/routes/adoption-requests';
import { AdoptionRequestStatus, IAdoptionRequest } from '@/types';
import { Form, router } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

function updateStatus(
    id: number,
    status: AdoptionRequestStatus,
    onDone: () => void,
) {
    router.patch(
        adoptionRequests.updateStatus(id).url,
        { status },
        { onSuccess: onDone },
    );
}

export default function AdoptionRequestDetail({
    request,
    onClose,
}: {
    request: IAdoptionRequest | null;
    onClose: () => void;
}) {
    const { t } = useTranslation('adoption-requests');

    const [adopterUpdateSuccess, setAdopterUpdateSuccess] =
        useState<boolean>(false);
    const onUpdateAdopterSuccess = (): void => {
        setAdopterUpdateSuccess(true);
    };

    return (
        <CustomModal showModal={request !== null} onClose={onClose}>
            {request && (
                <>
                    <ModalHeader>
                        <ModalTitle>{request.animal.name}</ModalTitle>
                    </ModalHeader>

                    <dl className="my-3 grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 text-sm">
                        <dt className="text-muted-foreground">
                            {t('table.adopter')}
                        </dt>
                        <dd>
                            {request.adopterProfile.firstName}{' '}
                            {request.adopterProfile.lastName}
                        </dd>

                        <dt className="text-muted-foreground">Email</dt>
                        <dd>{request.adopterProfile.email}</dd>

                        <Form
                            id="adopter-details-form"
                            {...AdopterProfileController.update.form(
                                request.adopterProfile.id,
                            )}
                            onSuccess={onUpdateAdopterSuccess}
                        >
                            {({ errors }) => (
                                <Field>
                                    <Label htmlFor="details">
                                        {t('table.adopter_details')}
                                    </Label>
                                    <Textarea
                                        name="details"
                                        id="details"
                                        placeholder={t(
                                            'table.adopter_details_placeholder',
                                        )}
                                        defaultValue={
                                            request.adopterProfile.details
                                        }
                                        onBlur={() => {
                                            (
                                                document.getElementById(
                                                    'adopter-details-form',
                                                ) as HTMLFormElement
                                            ).requestSubmit();
                                        }}
                                    />
                                    <InputError
                                        message={
                                            errors.details ??
                                            (adopterUpdateSuccess // on success, staying a few seconds
                                                ? t(
                                                      'table.adopter_update_success',
                                                  )
                                                : undefined)
                                        }
                                        className={
                                            adopterUpdateSuccess
                                                ? 'text-green-600 dark:text-green-400'
                                                : ''
                                        }
                                    />
                                </Field>
                            )}
                        </Form>
                    </dl>

                    <article>
                        <h2 className="font-semibold">
                            {t('table.adopter_message')}
                        </h2>
                        <p className="text-sm whitespace-pre-wrap">
                            {request.content}
                        </p>
                    </article>

                    {request.status === 'unattended' && (
                        <ModalFooter>
                            <Button
                                variant="destructive"
                                onClick={() =>
                                    updateStatus(
                                        request.id,
                                        'rejected',
                                        onClose,
                                    )
                                }
                            >
                                {t('actions.reject')}
                            </Button>
                            <Button
                                onClick={() =>
                                    updateStatus(request.id, 'pending', onClose)
                                }
                            >
                                {t('actions.markContacted')}
                            </Button>
                        </ModalFooter>
                    )}

                    {request.status === 'pending' && (
                        <ModalFooter>
                            <Button
                                variant="destructive"
                                onClick={() =>
                                    updateStatus(
                                        request.id,
                                        'rejected',
                                        onClose,
                                    )
                                }
                            >
                                {t('actions.reject')}
                            </Button>
                            <Button
                                onClick={() =>
                                    updateStatus(
                                        request.id,
                                        'approved',
                                        onClose,
                                    )
                                }
                            >
                                {t('actions.accept')}
                            </Button>
                        </ModalFooter>
                    )}
                </>
            )}
        </CustomModal>
    );
}
