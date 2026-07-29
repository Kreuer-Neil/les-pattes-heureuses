import AdoptionRequestDetail from '@/components/adoption-requests/adoption-request-detail';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { useImage } from '@/hooks/use-image-asset';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import adoptionRequests from '@/routes/adoption-requests';
import { AdoptionRequestStatus, IAdoptionRequest, type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

type PageProps = {
    adoptionRequests: IAdoptionRequest[];
};

const statusVariant: Record<AdoptionRequestStatus, 'default' | 'secondary' | 'outline' | 'destructive'> = {
    unattended: 'default',
    pending: 'secondary',
    approved: 'outline',
    rejected: 'destructive',
};

function AdoptionRequestRow({ request, onClick }: { request: IAdoptionRequest; onClick: () => void }) {
    const { t } = useTranslation('adoption-requests');
    const image = useImage(request.animal.image, 'icon');

    return (
        <tr onClick={onClick} className="cursor-pointer align-top hover:bg-muted/50">
            <td className="flex items-center gap-3 p-3">
                <Avatar>
                    {image && <AvatarImage src={image.src} srcSet={image.srcSet} alt={request.animal.name} />}
                    <AvatarFallback>{request.animal.name.slice(0, 2).toUpperCase()}</AvatarFallback>
                </Avatar>
                <span className="font-medium">{request.animal.name}</span>
            </td>
            <td className="p-3">
                <div className="font-medium">
                    {request.adopterProfile.firstName} {request.adopterProfile.lastName}
                </div>
                <div className="text-sm text-muted-foreground">{request.adopterProfile.email}</div>
            </td>
            <td className="max-w-xs truncate p-3 text-muted-foreground">{request.content}</td>
            <td className="p-3">
                <Badge variant={statusVariant[request.status]}>{t(`status.${request.status}`)}</Badge>
            </td>
            <td className="p-3 text-sm text-muted-foreground">{new Date(request.createdAt).toLocaleDateString()}</td>
        </tr>
    );
}

export default function AdoptionRequestsIndex() {
    const { adoptionRequests: requests } = usePage<PageProps>().props;
    const { t } = useTranslation('adoption-requests');
    const [selectedId, setSelectedId] = useState<number | null>(null);
    const selectedRequest = requests.find((request) => request.id === selectedId) ?? null;

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard().url },
        { title: t('title'), href: adoptionRequests.index().url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('title')} />

            <div className="flex flex-col gap-4 p-4">
                <h1 className="text-xl font-semibold">{t('title')}</h1>

                {requests.length > 0 ? (
                    <div className="overflow-x-auto rounded-md border">
                        <table className="w-full text-sm">
                            <thead className="bg-muted/50 text-left">
                                <tr>
                                    <th className="p-3 font-medium">{t('table.animal')}</th>
                                    <th className="p-3 font-medium">{t('table.adopter')}</th>
                                    <th className="p-3 font-medium">{t('table.message')}</th>
                                    <th className="p-3 font-medium">{t('table.status')}</th>
                                    <th className="p-3 font-medium">{t('table.date')}</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {requests.map((request) => (
                                    <AdoptionRequestRow key={request.id} request={request} onClick={() => setSelectedId(request.id)} />
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <p className="text-muted-foreground">{t('empty')}</p>
                )}
            </div>

            <AdoptionRequestDetail request={selectedRequest} onClose={() => setSelectedId(null)} />
        </AppLayout>
    );
}