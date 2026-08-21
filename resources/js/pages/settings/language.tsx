import { Head } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

import HeadingSmall from '@/components/heading-small';
import { type BreadcrumbItem } from '@/types';

import { switchLanguage } from '@/actions/App/Http/Controllers/LanguageController';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { cn } from '@/lib/utils';
import { edit as editLanguage } from '@/routes/language';

export default function Language({
    currentLocale,
    locales,
}: {
    currentLocale: string;
    locales: string[];
}) {
    const { t } = useTranslation('auth');

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('language.title'),
            href: editLanguage().url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('language.title')} />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title={t('language.title')}
                        description={t('language.description')}
                    />

                    <div className="inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800">
                        {locales.map((locale) => (
                            <a
                                key={locale}
                                href={switchLanguage.url({
                                    query: { lang: locale },
                                })}
                                className={cn(
                                    'rounded-md px-3.5 py-1.5 text-sm transition-colors',
                                    currentLocale === locale
                                        ? 'bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100'
                                        : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
                                )}
                            >
                                {locale.toUpperCase()}
                            </a>
                        ))}
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
