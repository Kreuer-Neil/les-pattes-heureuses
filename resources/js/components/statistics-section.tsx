import { Button } from '@/components/ui/button';
import {
    Card,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes';
import reports from '@/routes/reports';
import { router } from '@inertiajs/react';
import { useRef } from 'react';
import { useTranslation } from 'react-i18next';

export type StatisticsData = {
    start: string;
    end: string;
    animalsReceived: number;
    successfulAdoptions: number;
    animalsStillPresent: number;
};

function formatDate(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

function lastMonthRange(): { start: string; end: string } {
    const now = new Date();
    const firstOfThisMonth = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastMonthEnd = new Date(firstOfThisMonth.getTime() - 1);
    const lastMonthStart = new Date(
        lastMonthEnd.getFullYear(),
        lastMonthEnd.getMonth(),
        1,
    );

    return { start: formatDate(lastMonthStart), end: formatDate(lastMonthEnd) };
}

function visitPeriod(params: { start?: string; end?: string }) {
    router.get(dashboard().url, params, {
        preserveState: true,
        preserveScroll: true,
        only: ['statistics'],
    });
}

export function StatisticsSection({
    statistics,
}: {
    statistics: StatisticsData;
}) {
    const { t } = useTranslation('dashboard');
    const startRef = useRef<HTMLInputElement>(null);
    const endRef = useRef<HTMLInputElement>(null);

    const applyCustomRange = () => {
        if (!startRef.current?.value || !endRef.current?.value) {
            return;
        }

        visitPeriod({
            start: startRef.current.value,
            end: endRef.current.value,
        });
    };

    return (
        <div className="flex flex-col gap-4">
            <div className="flex flex-wrap items-end justify-between gap-3">
                <h2 className="font-medium">{t('statistics.title')}</h2>
                <div className="flex flex-wrap items-end gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => visitPeriod({})}
                    >
                        {t('statistics.thisMonth')}
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => visitPeriod(lastMonthRange())}
                    >
                        {t('statistics.lastMonth')}
                    </Button>
                    <div
                        key={`${statistics.start}-${statistics.end}`}
                        className="flex items-end gap-2"
                    >
                        <div className="flex flex-col gap-1">
                            <label
                                htmlFor="stats-start"
                                className="text-xs text-muted-foreground"
                            >
                                {t('statistics.from')}
                            </label>
                            <Input
                                id="stats-start"
                                type="date"
                                defaultValue={statistics.start}
                                ref={startRef}
                            />
                        </div>
                        <div className="flex flex-col gap-1">
                            <label
                                htmlFor="stats-end"
                                className="text-xs text-muted-foreground"
                            >
                                {t('statistics.to')}
                            </label>
                            <Input
                                id="stats-end"
                                type="date"
                                defaultValue={statistics.end}
                                ref={endRef}
                            />
                        </div>
                        <Button
                            variant="secondary"
                            size="sm"
                            onClick={applyCustomRange}
                        >
                            {t('statistics.apply')}
                        </Button>
                    </div>
                </div>
            </div>

            <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader>
                        <CardDescription>
                            {t('statistics.animalsReceived')}
                        </CardDescription>
                        <CardTitle className="text-3xl">
                            {statistics.animalsReceived}
                        </CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader>
                        <CardDescription>
                            {t('statistics.successfulAdoptions')}
                        </CardDescription>
                        <CardTitle className="text-3xl">
                            {statistics.successfulAdoptions}
                        </CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader>
                        <CardDescription>
                            {t('statistics.animalsStillPresent')}
                        </CardDescription>
                        <CardTitle className="text-3xl">
                            {statistics.animalsStillPresent}
                        </CardTitle>
                    </CardHeader>
                </Card>
            </div>

            <Button asChild variant="outline" size="sm" className="w-fit">
                <a
                    href={
                        reports.export({
                            query: {
                                start: statistics.start,
                                end: statistics.end,
                            },
                        }).url
                    }
                >
                    {t('statistics.downloadPdf')}
                </a>
            </Button>
        </div>
    );
}
