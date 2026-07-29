import CustomModal, { ModalFooter, ModalHeader, ModalTitle } from '@/components/modals/custom-modal';
import { Button } from '@/components/ui/button';
import adoptionRequests from '@/routes/adoption-requests';
import { AdoptionRequestStatus, IAdoptionRequest } from '@/types';
import { router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

function updateStatus(id: number, status: AdoptionRequestStatus, onDone: () => void) {
    router.patch(adoptionRequests.updateStatus(id).url, { status }, { onSuccess: onDone });
}

export default function AdoptionRequestDetail({ request, onClose }: { request: IAdoptionRequest | null; onClose: () => void }) {
    const { t } = useTranslation('adoption-requests');

    return (
        <CustomModal showModal={request !== null} onClose={onClose}>
            {request && (
                <>
                    <ModalHeader>
                        <ModalTitle>{request.animal.name}</ModalTitle>
                    </ModalHeader>

                    <dl className="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 text-sm">
                        <dt className="text-muted-foreground">{t('table.adopter')}</dt>
                        <dd>
                            {request.adopterProfile.firstName} {request.adopterProfile.lastName}
                        </dd>

                        <dt className="text-muted-foreground">Email</dt>
                        <dd>{request.adopterProfile.email}</dd>
                    </dl>

                    <p className="text-sm whitespace-pre-wrap">{request.content}</p>

                    {request.status === 'unattended' && (
                        <ModalFooter>
                            <Button variant="destructive" onClick={() => updateStatus(request.id, 'rejected', onClose)}>
                                {t('actions.reject')}
                            </Button>
                            <Button onClick={() => updateStatus(request.id, 'pending', onClose)}>{t('actions.markContacted')}</Button>
                        </ModalFooter>
                    )}

                    {request.status === 'pending' && (
                        <ModalFooter>
                            <Button variant="destructive" onClick={() => updateStatus(request.id, 'rejected', onClose)}>
                                {t('actions.reject')}
                            </Button>
                            <Button onClick={() => updateStatus(request.id, 'approved', onClose)}>{t('actions.accept')}</Button>
                        </ModalFooter>
                    )}
                </>
            )}
        </CustomModal>
    );
}