import { Link, usePage } from '@inertiajs/react';
import { PropsWithChildren, ReactNode, useEffect, useRef, useState } from 'react';
import Navbar from '@/Components/Navbar';

type SuccessMessage = {
    id: number;
    message: string;
};

export default function AuthenticatedLayout({
    header,
    children,
}: PropsWithChildren<{ header?: ReactNode }>) {
    const props = usePage().props as {
        auth: { user: any };
        success?: { message: string; time: number };
        error?: string;
    };
    const user = props.auth.user;

    const [successMessages, setSuccessMessages] = useState<SuccessMessage[]>([]);
    const timeoutRefs = useRef<{ [key: number]: ReturnType<typeof setTimeout> }>({});

    const [showingNavigationDropdown, setShowingNavigationDropdown] =
        useState(false);

    useEffect(() => {
        if (props.success?.message) {
            const newMessage: SuccessMessage = {
                id: props.success.time,
                message: props.success.message,
            };

            setSuccessMessages((prev) => {
                // avoid duplicates
                if (prev.some((msg) => msg.id === newMessage.id)) return prev;
                return [newMessage, ...prev];
            });

            const timeoutId = setTimeout(() => {
                setSuccessMessages((prev) => prev.filter((msg) => msg.id !== newMessage.id));
                delete timeoutRefs.current[newMessage.id];
            }, 5000);

            timeoutRefs.current[newMessage.id] = timeoutId;
        }

        // cleanup timeouts when unmounting
        return () => {
            Object.values(timeoutRefs.current).forEach(clearTimeout);
        };
    }, [props.success]);

    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
            <Navbar />

            {props.error && (
                <div className="container mx-auto mt-8">
                    <div className="alert alert-error">{props.error}</div>
                </div>
            )}

            {successMessages.length > 0 && (
                <div className="toast toast-top toast-end z-[1000] mt-16">
                    {successMessages.map((msg) => (
                        <div className="alert alert-success" key={msg.id}>
                            <span>{msg.message}</span>
                        </div>
                    ))}
                </div>
            )}

            <main>{children}</main>
        </div>
    );
}
