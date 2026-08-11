import AnimalChangeController from '@/actions/App/Http/Controllers/AnimalChangeController';
import AdoptionRequestDetail from '@/components/adoption-requests/adoption-request-detail';
import { AttentionItemLabel } from '@/components/attention-item-label';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Item,
    ItemActions,
    ItemContent,
    ItemGroup,
    ItemMedia,
    ItemTitle,
} from '@/components/ui/item';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import contactMessages from '@/routes/contact-messages';
import notifications from '@/routes/notifications';
import {
    IAdoptionRequestAttentionItem,
    IAttentionItem,
    type BreadcrumbItem,
} from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

type PageProps = {
    items: IAttentionItem[];
    unreadMessageCount: number;
};

function AnimalChangeActions({ id }: { id: number }) {
    const { t } = useTranslation('adoption-requests');

    return (
        <div className="flex flex-wrap items-center gap-2">
            <Button
                size="sm"
                onClick={() =>
                    router.patch(AnimalChangeController.acceptChange(id).url)
                }
            >
                {t('actions.accept')}
            </Button>
            <Button
                size="sm"
                variant="destructive"
                onClick={() =>
                    router.patch(AnimalChangeController.denyChange(id).url)
                }
            >
                {t('actions.reject')}
            </Button>
        </div>
    );
}

export default function NotificationsIndex() {
    const { items, unreadMessageCount } = usePage<PageProps>().props;
    const { t } = useTranslation('notifications');
    const [selectedRequest, setSelectedRequest] =
        useState<IAdoptionRequestAttentionItem | null>(null);

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
                    <Item
                        asChild
                        variant="outline"
                        size="sm"
                        className="cursor-pointer [a]:hover:bg-accent/50"
                    >
                        <Link href={contactMessages.index().url}>
                            <ItemMedia>
                                <Badge variant="secondary">
                                    {unreadMessageCount}
                                </Badge>
                            </ItemMedia>
                            <ItemContent>
                                <ItemTitle className="font-normal text-muted-foreground">
                                    {t('unreadMessages', {
                                        count: unreadMessageCount,
                                    })}
                                </ItemTitle>
                            </ItemContent>
                        </Link>
                    </Item>
                )}

                {items.length > 0 ? (
                    <ItemGroup className="gap-2">
                        {items.map((item) => (
                            <Item
                                key={`${item.type}-${item.id}`}
                                variant="outline"
                                size="sm"
                                onClick={() =>
                                    item.type === 'adoption_request' &&
                                    setSelectedRequest(item)
                                }
                                className={
                                    item.type === 'adoption_request'
                                        ? 'cursor-pointer [a]:hover:bg-accent/50'
                                        : ''
                                }
                            >
                                <ItemMedia>
                                    <Badge variant="destructive">!</Badge>
                                </ItemMedia>
                                <ItemContent>
                                    <ItemTitle className="font-normal">
                                        <AttentionItemLabel item={item} />
                                    </ItemTitle>
                                </ItemContent>
                                {item.type === 'animal_change' && (
                                    <ItemActions>
                                        <AnimalChangeActions id={item.id} />
                                    </ItemActions>
                                )}
                            </Item>
                        ))}
                    </ItemGroup>
                ) : (
                    unreadMessageCount === 0 && (
                        <p className="text-muted-foreground">{t('empty')}</p>
                    )
                )}
            </div>

            <AdoptionRequestDetail
                request={selectedRequest}
                onClose={() => setSelectedRequest(null)}
            />
        </AppLayout>
    );
}
