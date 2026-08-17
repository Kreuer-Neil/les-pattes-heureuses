import NotificationController from '@/actions/App/Http/Controllers/Settings/NotificationController';
import { type BreadcrumbItem } from '@/types';
import { Transition } from '@headlessui/react';
import { Form, Head, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { edit } from '@/routes/notification-preferences';

type PageProps = {
    notifyAdoptionRequests: boolean;
    notifyContactMessages: boolean;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Notification settings',
        href: edit().url,
    },
];

export default function Notifications() {
    const { notifyAdoptionRequests, notifyContactMessages } =
        usePage<PageProps>().props;

    // Radix's Switch only bubbles a native checkbox input when checked, so unchecking one
    // would drop the key from the submitted FormData entirely. Track state ourselves and
    // submit it through always-present hidden inputs instead.
    const [adoptionRequests, setAdoptionRequests] = useState(
        notifyAdoptionRequests,
    );
    const [contactMessages, setContactMessages] = useState(
        notifyContactMessages,
    );

    const { t } = useTranslation('auth');

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('notifications.title')} />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title={t('notifications.title')}
                        description={t('notifications.description')}
                    />

                    <Form
                        {...NotificationController.update.form()}
                        options={{
                            preserveScroll: true,
                        }}
                        className="space-y-6"
                    >
                        {({ processing, recentlySuccessful }) => (
                            <>
                                <input
                                    type="hidden"
                                    name="notify_adoption_requests"
                                    value={adoptionRequests ? '1' : '0'}
                                />
                                <input
                                    type="hidden"
                                    name="notify_contact_messages"
                                    value={contactMessages ? '1' : '0'}
                                />

                                <div className="flex items-center justify-between gap-4 rounded-lg border p-4">
                                    <div className="space-y-0.5">
                                        <Label htmlFor="notify_adoption_requests_switch">
                                            {t(
                                                'notifications.adoptionRequests',
                                            )}
                                        </Label>
                                        <p className="text-sm text-muted-foreground">
                                            {t(
                                                'notifications.adoptionRequestsDescription',
                                            )}
                                        </p>
                                    </div>

                                    <Switch
                                        id="notify_adoption_requests_switch"
                                        checked={adoptionRequests}
                                        onCheckedChange={setAdoptionRequests}
                                    />
                                </div>

                                <div className="flex items-center justify-between gap-4 rounded-lg border p-4">
                                    <div className="space-y-0.5">
                                        <Label htmlFor="notify_contact_messages_switch">
                                            {t('notifications.contactMessages')}
                                        </Label>
                                        <p className="text-sm text-muted-foreground">
                                            {t(
                                                'notifications.contactMessagesDescription',
                                            )}
                                        </p>
                                    </div>

                                    <Switch
                                        id="notify_contact_messages_switch"
                                        checked={contactMessages}
                                        onCheckedChange={setContactMessages}
                                    />
                                </div>

                                <div className="flex items-center gap-4">
                                    <Button
                                        disabled={processing}
                                        data-test="update-notifications-button"
                                    >
                                        {t('notifications.save')}
                                    </Button>

                                    <Transition
                                        show={recentlySuccessful}
                                        enter="transition ease-in-out"
                                        enterFrom="opacity-0"
                                        leave="transition ease-in-out"
                                        leaveTo="opacity-0"
                                    >
                                        <p className="text-sm text-neutral-600">
                                            {t('notifications.saved')}
                                        </p>
                                    </Transition>
                                </div>
                            </>
                        )}
                    </Form>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
