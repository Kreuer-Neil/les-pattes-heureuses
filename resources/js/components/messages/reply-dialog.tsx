import CustomModal, {
    ModalFooter,
    ModalHeader,
    ModalTitle,
} from '@/components/modals/custom-modal';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Textarea } from '@/components/ui/textarea';
import { type RouteFormDefinition } from '@/wayfinder';
import { Form, usePage } from '@inertiajs/react';
import { type ReactNode, useState } from 'react';
import { useTranslation } from 'react-i18next';

type ReplyProps = {
    defaultSignature: string;
};

export default function ReplyDialog({
    open,
    onClose,
    recipientLabel,
    recipientEmail,
    originalContent,
    replyForm,
    extraField,
}: {
    open: boolean;
    onClose: () => void;
    recipientLabel: string;
    recipientEmail: string | null;
    originalContent: string;
    replyForm: RouteFormDefinition<'post'>;
    extraField?: (props: { errors: Record<string, string> }) => ReactNode;
}) {
    const { defaultSignature } = usePage<ReplyProps>().props;
    const { t } = useTranslation(['messages', 'modals']);
    const [editingSignature, setEditingSignature] = useState(false);

    return (
        <CustomModal showModal={open} onClose={onClose}>
            <ModalHeader>
                <ModalTitle>
                    {t('messages:reply.title', { recipient: recipientLabel })}
                </ModalTitle>
            </ModalHeader>

            <div className="mb-3 rounded-md border bg-muted/40 p-3 text-sm whitespace-pre-wrap">
                <p className="mb-1 font-medium text-muted-foreground">
                    {t('messages:reply.originalMessage')}
                </p>
                {originalContent}
            </div>

            {recipientEmail === null ? (
                <p className="text-sm text-destructive">
                    {t('messages:reply.noEmail')}
                </p>
            ) : (
                <Form
                    {...replyForm}
                    resetOnSuccess
                    onSuccess={onClose}
                    className="flex flex-col gap-4"
                >
                    {({ errors, processing }) => (
                        <>
                            <Field>
                                <FieldLabel htmlFor="message">
                                    {t('messages:reply.messageLabel')}
                                </FieldLabel>
                                <Textarea
                                    id="message"
                                    name="message"
                                    placeholder={t(
                                        'messages:reply.messagePlaceholder',
                                    )}
                                    aria-invalid={!!errors.message}
                                />
                                <FieldError
                                    errors={[{ message: errors.message }]}
                                />
                            </Field>

                            {extraField?.({ errors })}

                            <Field>
                                <FieldLabel htmlFor="signature">
                                    {t('messages:reply.signatureLabel')}
                                </FieldLabel>
                                {editingSignature ? (
                                    <Textarea
                                        id="signature"
                                        name="signature"
                                        defaultValue={defaultSignature}
                                        aria-invalid={!!errors.signature}
                                    />
                                ) : (
                                    <div className="flex items-center justify-between gap-2 text-sm">
                                        <span className="whitespace-pre-wrap">
                                            {defaultSignature}
                                        </span>
                                        <input
                                            type="hidden"
                                            name="signature"
                                            value={defaultSignature}
                                        />
                                        <Button
                                            type="button"
                                            variant="link"
                                            size="sm"
                                            onClick={() =>
                                                setEditingSignature(true)
                                            }
                                        >
                                            {t('messages:reply.editSignature')}
                                        </Button>
                                    </div>
                                )}
                                <FieldError
                                    errors={[{ message: errors.signature }]}
                                />
                            </Field>

                            <ModalFooter>
                                <Button
                                    type="button"
                                    variant="secondary"
                                    onClick={onClose}
                                >
                                    {t('modals:cancel')}
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {t('messages:reply.send')}
                                </Button>
                            </ModalFooter>
                        </>
                    )}
                </Form>
            )}
        </CustomModal>
    );
}
