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
    role: string;
    // MustChangePassword will only be passed trough the change password view. Better to pass the prop on that view only.
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

// Custom things
export interface IImageData {
    xs: string;
    sm: string;
    lg: string;
}

// Custom types

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
    furPatternId: number | null;
    personality: string;
}

export interface IAnimalVaccine {
    id: number;
    vaccineType: {
        id: number;
        name: string;
    };
    vaccinatedAt: string;
}

export interface IAnimal extends IAnimalMiniature {
    bornAt: {
        toString: string;
        value: string;
    };
    vaccines: IAnimalVaccine[];
}

export type AdoptionRequestStatus =
    | 'unattended'
    | 'pending'
    | 'approved'
    | 'rejected';

export interface IAdopterProfile {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    details: string;
}

export interface IAdoptionRequest {
    id: number;
    status: AdoptionRequestStatus;
    content: string;
    createdAt: string;
    animal: IAnimalMiniature;
    adopterProfile: IAdopterProfile;
}

export type AnimalChangeAction = 'store' | 'update' | 'delete';

export interface IAdoptionRequestAttentionItem extends IAdoptionRequest {
    type: 'adoption_request';
}

export interface IAnimalChangeAttentionItem {
    type: 'animal_change';
    id: number;
    action: AnimalChangeAction;
    animalName: string | null;
    proposerName: string | null;
    createdAt: string;
}

export type IAttentionItem =
    | IAdoptionRequestAttentionItem
    | IAnimalChangeAttentionItem;

export interface IUserAccount {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    role: string;
    userRoleId: number;
    mustChangePassword: boolean;
    createdAt: string;
}
