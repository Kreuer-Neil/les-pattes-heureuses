<?php

namespace App\Enums\Animals;

enum Vaccines: string
{
    case Rabies = 'rabies'; // Dog, Cat, Horse

    // Dogs
    case Distemper = 'distemper'; // Dog
    case CanineParvovirus = 'canine parvovirus'; // Dog
    case Leptospirosis = 'leptospirosis'; // Dog
    case Bordetella = 'bordetella'; // Dog (kennel cough)

    // Cats
    case FelineViralRhinotracheitis = 'feline viral rhinotracheitis'; // Cat
    case FelineCalicivirus = 'feline calicivirus'; // Cat
    case FelinePanleukopenia = 'feline panleukopenia'; // Cat
    case FelineLeukemia = 'feline leukemia'; // Cat

    // Birds
    case AvianPolyomavirus = 'avian polyomavirus'; // Bird
    case PachecoDisease = 'pacheco disease'; // Bird

    // Horses
    case Tetanus = 'tetanus'; // Horse
    case EquineInfluenza = 'equine influenza'; // Horse
    case EquineHerpesvirus = 'equine herpesvirus'; // Horse
    case WestNileVirus = 'west nile virus'; // Horse

    // Reptiles have no core vaccines in routine veterinary practice — no cases here.
}
