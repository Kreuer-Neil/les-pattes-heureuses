// import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import adoptionRequests from '@/routes/adoption-requests';
import animals from '@/routes/animals';
import contactMessages from '@/routes/contact-messages';
import notifications from '@/routes/notifications';
import users from '@/routes/users';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import {
    /*BookOpen, Folder,*/ Bell,
    HeartHandshake,
    LayoutGrid,
    Mail,
    PawPrint,
    Users,
} from 'lucide-react';
import AppLogo from './app-logo';

/*const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];*/

export function AppSidebar() {
    const { auth } = usePage<SharedData>().props;

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Animals',
            href: animals.index(),
            icon: PawPrint,
        },
        {
            title: 'Adoption requests',
            href: adoptionRequests.index(),
            icon: HeartHandshake,
        },
        {
            title: 'Contact messages',
            href: contactMessages.index(),
            icon: Mail,
        },
        {
            title: 'Notifications',
            href: notifications.index(),
            icon: Bell,
        },
        ...(auth.user.role === 'admin'
            ? [
                  {
                      title: 'Volunteers',
                      href: users.index(),
                      icon: Users,
                  },
              ]
            : []),
    ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                {/*<NavFooter items={footerNavItems} className="mt-auto" />*/}
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
