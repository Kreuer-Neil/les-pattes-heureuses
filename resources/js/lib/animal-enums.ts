// Mirrors app/Enums/Animals/Status.php and app/Enums/Animals/Specie.php.
// Keep in sync by hand when a PHP case is added/renamed/removed.

export const AnimalStatus = {
    Unknown: 'unknown',
    Available: 'available',
    Pending: 'pending',
    Adopted: 'adopted',
    Healing: 'healing',
    Deceased: 'deceased',
} as const;

export const Specie = {
    Dog: 'dog',
    Cat: 'cat',
    Bird: 'bird',
    Horse: 'horse',
    Reptile: 'reptile',
} as const;