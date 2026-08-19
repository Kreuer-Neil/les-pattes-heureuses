import { AttentionItemLabel } from '@/components/attention-item-label';
import { RecentAnimalsWidget } from '@/components/animals/recent-animals';
import {
    StatisticsSection,
    type StatisticsData,
} from '@/components/statistics-section';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import notifications from '@/routes/notifications';
import { IAttentionItem, IDashboardAnimal, type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

type PageProps = {
    needsAttention: IAttentionItem[];
    unreadMessageCount: number;
    statistics: StatisticsData | null;
    recentlyAddedAnimals: IDashboardAnimal[];
};

type NeedsAttentionProps = {
    needsAttention: IAttentionItem[];
    unreadMessageCount: number;
};

function NeedsAttentionWidget({
    needsAttention,
    unreadMessageCount,
}: NeedsAttentionProps) {
    const { t } = useTranslation('notifications');

    return (
        <div className="flex flex-col gap-3 rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
            <div className="flex items-center justify-between">
                <h2 className="font-medium">{t('widget.title')}</h2>
                <Link
                    href={notifications.index().url}
                    className="text-sm text-muted-foreground hover:underline text-right"
                >
                    {t('widget.viewAll')}
                </Link>
            </div>

            {unreadMessageCount > 0 && (
                <div className="flex items-center gap-2 text-sm">
                    <Badge variant="secondary">{unreadMessageCount}</Badge>
                    <span className="text-muted-foreground">
                        {t('unreadMessages', { count: unreadMessageCount })}
                    </span>
                </div>
            )}

            {needsAttention.length > 0 ? (
                <ul className="flex flex-col gap-2">
                    {needsAttention.map((item) => (
                        <li
                            key={`${item.type}-${item.id}`}
                            className="flex items-center gap-2 text-sm"
                        >
                            <Badge variant="destructive">!</Badge>
                            <span>
                                <AttentionItemLabel item={item} />
                            </span>
                        </li>
                    ))}
                </ul>
            ) : (
                unreadMessageCount === 0 && (
                    <p className="text-sm text-muted-foreground">
                        {t('widget.empty')}
                    </p>
                )
            )}
        </div>
    );
}

export default function Dashboard() {
    const { needsAttention, unreadMessageCount, statistics, recentlyAddedAnimals } =
        usePage<PageProps>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <NeedsAttentionWidget
                    needsAttention={needsAttention}
                    unreadMessageCount={unreadMessageCount}
                />
                {statistics && <StatisticsSection statistics={statistics} />}
                <RecentAnimalsWidget animals={recentlyAddedAnimals} />
            </div>
        </AppLayout>
    );
}
