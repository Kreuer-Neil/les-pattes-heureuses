import ContactMessageController from '@/actions/App/Http/Controllers/ContactMessageController';
import ReplyDialog from '@/components/messages/reply-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import contactMessages from '@/routes/contact-messages';
import { type BreadcrumbItem, type IContactMessage } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { CheckCircle2, CircleDashed, XCircle } from 'lucide-react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

type PageProps = {
    contactMessages: IContactMessage[];
};

function StatusIcon({ status }: { status: IContactMessage['status'] }) {
    const { t } = useTranslation('contact-messages');

    if (status === 'answered') {
        return (
            <CheckCircle2
                className="size-4 text-green-600 dark:text-green-400"
                aria-label={t('status.answered')}
            />
        );
    }

    if (status === 'ignored') {
        return (
            <XCircle
                className="size-4 text-muted-foreground"
                aria-label={t('status.ignored')}
            />
        );
    }

    return (
        <CircleDashed
            className="size-4 text-amber-500"
            aria-label={t('status.unanswered')}
        />
    );
}

function ContactMessageRow({
    message,
    onClick,
}: {
    message: IContactMessage;
    onClick: () => void;
}) {
    const { t } = useTranslation('contact-messages');

    return (
        <tr
            onClick={onClick}
            className="cursor-pointer align-top hover:bg-muted/50"
        >
            <td className="p-3">
                <StatusIcon status={message.status} />
            </td>
            <td className="p-3">
                <div className="font-medium">
                    {message.firstName} {message.lastName}
                </div>
                <div className="text-sm text-muted-foreground">
                    {message.email}
                </div>
            </td>
            <td className="p-3">
                <Badge variant="secondary">{t(`type.${message.type}`)}</Badge>
            </td>
            <td className="max-w-xs truncate p-3 text-muted-foreground">
                {message.content}
            </td>
            <td className="p-3 text-sm text-muted-foreground">
                {new Date(message.createdAt).toLocaleDateString()}
            </td>
            <td className="p-3">
                <Button
                    size="sm"
                    variant="ghost"
                    onClick={(event) => {
                        event.stopPropagation();
                        router.patch(
                            contactMessages.markIgnored(message.id).url,
                        );
                    }}
                >
                    {t('actions.ignore')}
                </Button>
            </td>
        </tr>
    );
}

export default function ContactMessagesIndex() {
    const { contactMessages: messages } = usePage<PageProps>().props;
    const { t } = useTranslation('contact-messages');
    const [selectedId, setSelectedId] = useState<number | null>(null);
    const selected = messages.find((m) => m.id === selectedId) ?? null;

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard().url },
        { title: t('title'), href: contactMessages.index().url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('title')} />

            <div className="flex flex-col gap-4 p-4">
                <h1 className="text-xl font-semibold">{t('title')}</h1>

                {messages.length > 0 ? (
                    <div className="overflow-x-auto rounded-md border">
                        <table className="w-full text-sm">
                            <thead className="bg-muted/50 text-left">
                                <tr>
                                    <th className="p-3 font-medium">
                                        {t('table.status')}
                                    </th>
                                    <th className="p-3 font-medium">
                                        {t('table.from')}
                                    </th>
                                    <th className="p-3 font-medium">
                                        {t('table.type')}
                                    </th>
                                    <th className="p-3 font-medium">
                                        {t('table.message')}
                                    </th>
                                    <th className="p-3 font-medium">
                                        {t('table.date')}
                                    </th>
                                    <th className="p-3 font-medium">
                                        {t('table.actions')}
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {messages.map((message) => (
                                    <ContactMessageRow
                                        key={message.id}
                                        message={message}
                                        onClick={() =>
                                            setSelectedId(message.id)
                                        }
                                    />
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <p className="text-muted-foreground">{t('empty')}</p>
                )}
            </div>

            {selected && (
                <ReplyDialog
                    open={selected !== null}
                    onClose={() => setSelectedId(null)}
                    recipientLabel={`${selected.firstName} ${selected.lastName}`}
                    recipientEmail={selected.email}
                    originalContent={selected.content}
                    replyForm={ContactMessageController.reply.form(selected.id)}
                />
            )}
        </AppLayout>
    );
}
