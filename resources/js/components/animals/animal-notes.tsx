import AnimalNoteController from '@/actions/App/Http/Controllers/AnimalNoteController';
import { Button } from '@/components/ui/button';
import { Field, FieldError } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { IAnimalNote, SharedData } from '@/types';
import { Form, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

function canManage(auth: SharedData['auth'], note: IAnimalNote): boolean {
    return auth.user.role === 'admin' || auth.user.id === note.authorId;
}

export default function AnimalNotes({
    animalId,
    notes,
    onRefresh,
}: {
    animalId: number;
    notes: IAnimalNote[];
    onRefresh: () => void;
}) {
    const { t } = useTranslation('animals');
    const { auth } = usePage<SharedData>().props;

    const [editingNoteId, setEditingNoteId] = useState<number | null>(null);
    const [confirmDeleteId, setConfirmDeleteId] = useState<number | null>(null);

    return (
        <div className="col-span-2 flex flex-col gap-3 border-t pt-4">
            <h3 className="text-sm font-medium">{t('notes.heading')}</h3>

            {notes.length === 0 && (
                <p className="text-sm text-muted-foreground">
                    {t('notes.empty')}
                </p>
            )}

            {notes.length > 0 && (
                <ul className="flex flex-col gap-3">
                    {notes.map((note) => (
                        <li
                            key={note.id}
                            className="rounded-md border p-3 text-sm"
                        >
                            {editingNoteId === note.id ? (
                                <Form
                                    {...AnimalNoteController.update.form(
                                        note.id,
                                    )}
                                    onSuccess={() => {
                                        setEditingNoteId(null);
                                        onRefresh();
                                    }}
                                    className="flex flex-col gap-2"
                                >
                                    {({ errors, processing }) => (
                                        <>
                                            <Field>
                                                <Input
                                                    name="title"
                                                    defaultValue={note.title}
                                                    aria-invalid={
                                                        !!errors.title
                                                    }
                                                />
                                                <FieldError
                                                    errors={[
                                                        {
                                                            message:
                                                                errors.title,
                                                        },
                                                    ]}
                                                />
                                            </Field>
                                            <Field>
                                                <Textarea
                                                    name="text"
                                                    defaultValue={note.text}
                                                    aria-invalid={!!errors.text}
                                                />
                                                <FieldError
                                                    errors={[
                                                        {
                                                            message:
                                                                errors.text,
                                                        },
                                                    ]}
                                                />
                                            </Field>
                                            <div className="flex justify-end gap-2">
                                                <Button
                                                    type="button"
                                                    size="sm"
                                                    variant="outline"
                                                    disabled={processing}
                                                    onClick={() =>
                                                        setEditingNoteId(null)
                                                    }
                                                >
                                                    {t('edit.cancel')}
                                                </Button>
                                                <Button
                                                    type="submit"
                                                    size="sm"
                                                    disabled={processing}
                                                >
                                                    {t('edit.submit')}
                                                </Button>
                                            </div>
                                        </>
                                    )}
                                </Form>
                            ) : (
                                <div className="flex flex-col gap-1">
                                    <div className="flex items-start justify-between gap-2">
                                        <p className="font-medium">
                                            {note.title}
                                        </p>
                                        {canManage(auth, note) &&
                                            confirmDeleteId !== note.id && (
                                                <div className="flex shrink-0 gap-1">
                                                    <Button
                                                        type="button"
                                                        size="sm"
                                                        variant="ghost"
                                                        onClick={() =>
                                                            setEditingNoteId(
                                                                note.id,
                                                            )
                                                        }
                                                    >
                                                        {t('notes.edit')}
                                                    </Button>
                                                    <Button
                                                        type="button"
                                                        size="sm"
                                                        variant="ghost"
                                                        onClick={() =>
                                                            setConfirmDeleteId(
                                                                note.id,
                                                            )
                                                        }
                                                    >
                                                        {t('notes.delete')}
                                                    </Button>
                                                </div>
                                            )}
                                    </div>
                                    <p className="whitespace-pre-wrap">
                                        {note.text}
                                    </p>
                                    <p className="text-xs text-muted-foreground">
                                        {t('notes.meta', {
                                            name:
                                                note.authorName ??
                                                t('notes.unknownAuthor'),
                                            date: new Date(
                                                note.createdAt,
                                            ).toLocaleDateString(),
                                        })}
                                    </p>
                                    {confirmDeleteId === note.id && (
                                        <Form
                                            {...AnimalNoteController.destroy.form(
                                                note.id,
                                            )}
                                            onSuccess={onRefresh}
                                            className="mt-1 flex items-center gap-2"
                                        >
                                            {({ processing }) => (
                                                <>
                                                    <span className="text-xs text-muted-foreground">
                                                        {t(
                                                            'notes.confirmDelete',
                                                        )}
                                                    </span>
                                                    <Button
                                                        type="submit"
                                                        size="sm"
                                                        variant="destructive"
                                                        disabled={processing}
                                                    >
                                                        {t(
                                                            'notes.confirmDeleteYes',
                                                        )}
                                                    </Button>
                                                    <Button
                                                        type="button"
                                                        size="sm"
                                                        variant="outline"
                                                        disabled={processing}
                                                        onClick={() =>
                                                            setConfirmDeleteId(
                                                                null,
                                                            )
                                                        }
                                                    >
                                                        {t('edit.cancel')}
                                                    </Button>
                                                </>
                                            )}
                                        </Form>
                                    )}
                                </div>
                            )}
                        </li>
                    ))}
                </ul>
            )}

            <Form
                {...AnimalNoteController.store.form(animalId)}
                resetOnSuccess
                onSuccess={onRefresh}
                className="flex flex-col gap-2"
            >
                {({ errors, processing }) => (
                    <>
                        <Field>
                            <Input
                                name="title"
                                placeholder={t('notes.titlePlaceholder')}
                                aria-invalid={!!errors.title}
                            />
                            <FieldError errors={[{ message: errors.title }]} />
                        </Field>
                        <Field>
                            <Textarea
                                name="text"
                                placeholder={t('notes.textPlaceholder')}
                                aria-invalid={!!errors.text}
                            />
                            <FieldError errors={[{ message: errors.text }]} />
                        </Field>
                        <Button
                            type="submit"
                            size="sm"
                            disabled={processing}
                            className="self-end"
                        >
                            {t('notes.add')}
                        </Button>
                    </>
                )}
            </Form>
        </div>
    );
}
