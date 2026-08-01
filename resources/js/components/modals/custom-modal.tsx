import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { XIcon } from 'lucide-react';
import {
    createContext,
    ReactNode,
    useContext,
    useEffect,
    useRef,
    useState,
} from 'react';

const ModalContainerContext = createContext<HTMLElement | null>(null);

function useModalContainer() {
    return useContext(ModalContainerContext);
}

interface CustomReactModalProps {
    showModal: boolean;
    onClose: () => void;
    id?: string;
    className?: string;
    children: ReactNode | ReactNode[];
}

function ModalContent({
    className,
    children,
    ...props
}: React.ComponentProps<'div'>) {
    return (
        <div className={cn('grid gap-4', className)} {...props}>
            {children}
        </div>
    );
}

function ModalHeader({ className, ...props }: React.ComponentProps<'div'>) {
    return (
        <div
            className={cn(
                'flex flex-col gap-2 text-center sm:text-left mb-3 pe-10',
                className,
            )}
            {...props}
        />
    );
}

function ModalFooter({ className, ...props }: React.ComponentProps<'div'>) {
    return (
        <div
            className={cn(
                'flex flex-col items-center justify-center gap-2 sm:flex-row',
                className,
            )}
            {...props}
        />
    );
}

function ModalTitle({ className, ...props }: React.ComponentProps<'h2'>) {
    return (
        <h2
            className={cn('text-lg leading-none font-semibold', className)}
            {...props}
        />
    );
}

function ModalDescription({ className, ...props }: React.ComponentProps<'p'>) {
    return (
        <p
            className={cn('text-sm text-muted-foreground', className)}
            {...props}
        />
    );
}

export default function CustomModal({
    showModal,
    onClose,
    id,
    className = '',
    children,
}: CustomReactModalProps) {
    const dialogRef = useRef<HTMLDialogElement>(null);
    const [dialogNode, setDialogNode] = useState<HTMLDialogElement | null>(
        null,
    );

    useEffect(() => {
        if (showModal) {
            dialogRef.current?.showModal();
        } else {
            dialogRef.current?.close();
        }
    }, [showModal]);

    return (
        <dialog
            ref={(node) => {
                dialogRef.current = node;
                setDialogNode(node);
            }}
            closedby="any"
            id={id}
            onClose={onClose}
            className={cn('modal', className)}
        >
            <Button
                size="icon-sm"
                variant="ghost"
                onClick={onClose}
                className="absolute top-4 right-4 rounded-xs text-foreground opacity-70 transition-opacity hover:opacity-100 focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4"
            >
                <XIcon />
                <span className="sr-only">Close</span>
            </Button>
            <ModalContainerContext.Provider value={dialogNode}>
                {children}
            </ModalContainerContext.Provider>
        </dialog>
    );
}

export {
    ModalContent,
    ModalDescription,
    ModalFooter,
    ModalHeader,
    ModalTitle,
    useModalContainer,
};
