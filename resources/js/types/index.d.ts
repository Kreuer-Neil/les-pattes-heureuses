import { AnimalStatus, Specie as SpecieEnum } from '@/lib/animal-enums';
import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export type AnimalStatusName = (typeof AnimalStatus)[keyof typeof AnimalStatus];

export type SpecieName = (typeof SpecieEnum)[keyof typeof SpecieEnum];

export interface ISpecie {
    id: number;
    name: SpecieName;
}
export interface IBreed {
    id: number;
    name: string;
    specieId: number;
}

export interface IFurColor {
    id: number;
    name: string;
    color: string;
}

export interface IAnimalStatus {
    id: number;
    name: AnimalStatusName;
}

export interface IFurPattern {
    id: number;
    name: string;
}

export interface IVaccine {
    id: number;
    name: string;
}

export interface IAnimalTaxonomy {
    species: ISpecie[];
    breeds: IBreed[];
    statuses: IAnimalStatus[];
    furColors: IFurColor[];
    furPatterns: IFurPattern[];
    vaccines: IVaccine[];
}

export interface IAnimalMiniature {
    id: number;
    name: string;
    image: string | null;
    gender: 'M' | 'F';
    chip: string;
    statusId: number;
    specieId: number;
    breedId: number | null;
    furColorId: number | null;
    secondaryFurColorId: number | null;
    bornAt: {
        toString: string;
        value: string;
    };
    personality: string;
}

export interface IAnimal {
    name: string;
    image: string;
    gender: 'M' | 'F';
    chip: string;
    status: AnimalStatusName;
    specie: ISpecie;
    breed?: IBreed;
    furColor: IFurColor;
    secondaryFurColor?: IFurColor;
}
