import AnimalChangeController from '@/actions/App/Http/Controllers/AnimalChangeController';
import AdoptionRequestDetail from '@/components/adoption-requests/adoption-request-detail';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import notifications from '@/routes/notifications';
import { IAdoptionRequestAttentionItem, IAttentionItem, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

type PageProps = {
    items: IAttentionItem[];
    unreadMessageCount: number;
};

function NotificationLabel({ item }: { item: IAttentionItem }) {
    const { t } = useTranslation('notifications');

    if (item.type === 'adoption_request') {
        return (
            <>
                {t('types.adoption_request', {
                    animal: item.animal.name,
                    adopter: `${item.adopterProfile.firstName} ${item.adopterProfile.lastName}`,
                })}
            </>
        );
    }

    return (
        <>
            {t('types.animal_change', {
                proposer: item.proposerName ?? '—',
                action: t(`changeActions.${item.action}`),
                animal: item.animalName ?? '—',
            })}
        </>
    );
}

function AnimalChangeActions({ id }: { id: number }) {
    const { t } = useTranslation('adoption-requests');

    return (
        <div className="flex flex-wrap items-center gap-2">
            <Button size="sm" onClick={() => router.patch(AnimalChangeController.acceptChange(id).url)}>
                {t('actions.accept')}
            </Button>
            <Button size="sm" variant="destructive" onClick={() => router.patch(AnimalChangeController.denyChange(id).url)}>
                {t('actions.reject')}
            </Button>
        </div>
    );
}

export default function NotificationsIndex() {
    const { items, unreadMessageCount } = usePage<PageProps>().props;
    const { t } = useTranslation('notifications');
    const [selectedRequest, setSelectedRequest] = useState<IAdoptionRequestAttentionItem | null>(null);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard().url },
        { title: t('title'), href: notifications.index().url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('title')} />

            <div className="flex flex-col gap-4 p-4">
                <h1 className="text-xl font-semibold">{t('title')}</h1>

                {unreadMessageCount > 0 && (
                    <div className="flex items-center gap-3 rounded-md border p-3">
                        <Badge variant="secondary">{unreadMessageCount}</Badge>
                        <span className="text-muted-foreground">{t('unreadMessages', { count: unreadMessageCount })}</span>
                    </div>
                )}

                {items.length > 0 ? (
                    <ul className="flex flex-col gap-2">
                        {items.map((item) => (
                            <li
                                key={`${item.type}-${item.id}`}
                                onClick={() => item.type === 'adoption_request' && setSelectedRequest(item)}
                                className={`flex flex-wrap items-center justify-between gap-4 rounded-md border p-3 ${
                                    item.type === 'adoption_request' ? 'cursor-pointer hover:bg-muted/50' : ''
                                }`}
                            >
                                <div className="flex items-center gap-3">
                                    <Badge variant="destructive">!</Badge>
                                    <span>
                                        <NotificationLabel item={item} />
                                    </span>
                                </div>
                                {item.type === 'animal_change' && <AnimalChangeActions id={item.id} />}
                            </li>
                        ))}
                    </ul>
                ) : (
                    unreadMessageCount === 0 && <p className="text-muted-foreground">{t('empty')}</p>
                )}
            </div>

            <AdoptionRequestDetail request={selectedRequest} onClose={() => setSelectedRequest(null)} />
        </AppLayout>
    );
}