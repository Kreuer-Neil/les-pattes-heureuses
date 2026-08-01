import UserDeleteDialog from '@/components/users/user-delete-dialog';
import UserFormDialog from '@/components/users/user-form-dialog';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useImage } from '@/hooks/use-image-asset';
import { useInitials } from '@/hooks/use-initials';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import users from '@/routes/users';
import { type BreadcrumbItem, type IUserAccount } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

type PageProps = {
    users: IUserAccount[];
};

function UserAvatar({ user }: { user: IUserAccount }) {
    const getInitials = useInitials();
    const image = useImage(
        user.avatar && user.avatar !== 'default' ? user.avatar : null,
        'icon',
        'users',
    );

    return (
        <Avatar className="h-8 w-8">
            {image && (
                <AvatarImage
                    src={image.src}
                    srcSet={image.srcSet}
                    alt={user.name}
                />
            )}
            <AvatarFallback>{getInitials(user.name)}</AvatarFallback>
        </Avatar>
    );
}

export default function UsersIndex() {
    const { users: accounts } = usePage<PageProps>().props;
    const { t } = useTranslation('users');

    const [showCreate, setShowCreate] = useState(false);
    const [editTarget, setEditTarget] = useState<IUserAccount | null>(null);
    const [deleteTarget, setDeleteTarget] = useState<IUserAccount | null>(
        null,
    );

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard().url },
        { title: t('title'), href: users.index().url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('title')} />

            <div className="flex flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold">{t('title')}</h1>
                    <Button onClick={() => setShowCreate(true)}>
                        {t('new')}
                    </Button>
                </div>

                {accounts.length > 0 ? (
                    <div className="overflow-x-auto rounded-md border">
                        <table className="w-full text-sm">
                            <thead className="bg-muted/50 text-left">
                                <tr>
                                    <th className="p-3 font-medium">
                                        {t('table.name')}
                                    </th>
                                    <th className="p-3 font-medium">
                                        {t('table.email')}
                                    </th>
                                    <th className="p-3 font-medium">
                                        {t('table.role')}
                                    </th>
                                    <th className="p-3 font-medium">
                                        {t('table.created')}
                                    </th>
                                    <th className="p-3 font-medium">
                                        {t('table.actions')}
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {accounts.map((account) => (
                                    <tr key={account.id} className="align-top">
                                        <td className="flex items-center gap-3 p-3">
                                            <UserAvatar user={account} />
                                            <div>
                                                <div className="font-medium">
                                                    {account.name}
                                                </div>
                                                {account.mustChangePassword && (
                                                    <div className="text-xs text-muted-foreground">
                                                        {t(
                                                            'table.pendingFirstLogin',
                                                        )}
                                                    </div>
                                                )}
                                            </div>
                                        </td>
                                        <td className="p-3 text-muted-foreground">
                                            {account.email}
                                        </td>
                                        <td className="p-3">
                                            <Badge
                                                variant={
                                                    account.role === 'admin'
                                                        ? 'default'
                                                        : 'secondary'
                                                }
                                            >
                                                {t(`roles.${account.role}`)}
                                            </Badge>
                                        </td>
                                        <td className="p-3 text-sm text-muted-foreground">
                                            {new Date(
                                                account.createdAt,
                                            ).toLocaleDateString()}
                                        </td>
                                        <td className="p-3">
                                            <div className="flex gap-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        setEditTarget(account)
                                                    }
                                                >
                                                    {t('actions.edit')}
                                                </Button>
                                                <Button
                                                    variant="destructive"
                                                    size="sm"
                                                    onClick={() =>
                                                        setDeleteTarget(
                                                            account,
                                                        )
                                                    }
                                                >
                                                    {t('actions.delete')}
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <p className="text-muted-foreground">{t('empty')}</p>
                )}
            </div>

            <UserFormDialog
                open={showCreate}
                onClose={() => setShowCreate(false)}
            />

            <UserFormDialog
                key={editTarget?.id ?? 'edit'}
                open={!!editTarget}
                onClose={() => setEditTarget(null)}
                user={editTarget}
            />

            <UserDeleteDialog
                user={deleteTarget}
                onClose={() => setDeleteTarget(null)}
            />
        </AppLayout>
    );
}