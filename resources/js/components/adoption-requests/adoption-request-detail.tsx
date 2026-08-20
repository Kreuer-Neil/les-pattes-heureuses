import AdopterProfileController from '@/actions/App/Http/Controllers/AdopterProfileController';
import AdoptionRequestController from '@/actions/App/Http/Controllers/AdoptionRequestController';
import InputError from '@/components/input-error';
import ReplyDialog from '@/components/messages/reply-dialog';
import CustomModal, {
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/modals/custom-modal';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
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

    const [showReply, setShowReply] = useState<boolean>(false);

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

                        <Form
                            id="adopter-details-form"
                            {...AdopterProfileController.update.form(
                                request.adopterProfile.id,
                            )}
                            onSuccess={onUpdateAdopterSuccess}
                            className="flex flex-col gap-2"
                        >
                            {({ errors }) => (
                                <>
                                    <Field>
                                        <Label htmlFor="email">
                                            {t('table.adopter_email')}
                                        </Label>
                                        <Input
                                            name="email"
                                            id="email"
                                            type="email"
                                            defaultValue={
                                                request.adopterProfile.email ??
                                                ''
                                            }
                                            onBlur={() => {
                                                (
                                                    document.getElementById(
                                                        'adopter-details-form',
                                                    ) as HTMLFormElement
                                                ).requestSubmit();
                                            }}
                                        />
                                        <FieldError
                                            errors={[{ message: errors.email }]}
                                        />
                                    </Field>

                                    <Field>
                                        <Label htmlFor="otherContact">
                                            {t('table.adopter_other_contact')}
                                        </Label>
                                        <Input
                                            name="other_contact"
                                            id="otherContact"
                                            placeholder={t(
                                                'table.adopter_other_contact_placeholder',
                                            )}
                                            defaultValue={
                                                request.adopterProfile
                                                    .otherContact ?? ''
                                            }
                                            onBlur={() => {
                                                (
                                                    document.getElementById(
                                                        'adopter-details-form',
                                                    ) as HTMLFormElement
                                                ).requestSubmit();
                                            }}
                                        />
                                        <FieldError
                                            errors={[
                                                {
                                                    message:
                                                        errors.other_contact,
                                                },
                                            ]}
                                        />
                                    </Field>

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
                                </>
                            )}
                        </Form>
                    </dl>

                    <article className="mb-2">
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
                                variant="secondary"
                                onClick={() => setShowReply(true)}
                            >
                                {t('actions.reply')}
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
                                variant="secondary"
                                onClick={() => setShowReply(true)}
                            >
                                {t('actions.reply')}
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

                    <ReplyDialog
                        open={showReply}
                        onClose={() => setShowReply(false)}
                        recipientLabel={`${request.adopterProfile.firstName} ${request.adopterProfile.lastName}`}
                        recipientEmail={request.adopterProfile.email}
                        originalContent={request.content}
                        replyForm={AdoptionRequestController.reply.form(
                            request.id,
                        )}
                        extraField={({ errors }) => (
                            <Field>
                                <FieldLabel htmlFor="outcome">
                                    {t('outcome.label')}
                                </FieldLabel>
                                <select
                                    id="outcome"
                                    name="outcome"
                                    required
                                    defaultValue=""
                                    aria-invalid={!!errors.outcome}
                                    className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 aria-invalid:border-destructive"
                                >
                                    <option value="" disabled>
                                        {t('outcome.placeholder')}
                                    </option>
                                    <option value="positive">
                                        {t('outcome.positive')}
                                    </option>
                                    <option value="negative">
                                        {t('outcome.negative')}
                                    </option>
                                </select>
                                <FieldError
                                    errors={[{ message: errors.outcome }]}
                                />
                            </Field>
                        )}
                    />
                </>
            )}
        </CustomModal>
    );
}
