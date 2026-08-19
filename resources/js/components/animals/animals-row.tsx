import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import {
    Item,
    ItemContent,
    ItemDescription,
    ItemHeader,
    ItemMedia,
    ItemTitle,
} from '@/components/ui/item';
import { useAnimalLabels } from '@/hooks/use-animal-labels';
import { useImage } from '@/hooks/use-image-asset';
import { statusVariant } from '@/lib/animal-enums';
import { IAnimalMiniature } from '@/types';
import { useTranslation } from 'react-i18next';

export function AnimalRow({
    animal,
    onClick,
}: {
    animal: IAnimalMiniature;
    onClick: () => void;
}) {
    const { t } = useTranslation('animals');
    const { status, specieLabel } = useAnimalLabels(animal);

    const image = useImage(animal.image, 'icon');

    return (
        <tr onClick={onClick} className="cursor-pointer hover:bg-muted/50">
            <td className="flex items-center gap-3 p-3">
                <Avatar>
                    {image && (
                        <AvatarImage
                            src={image.src}
                            srcSet={image.srcSet}
                            alt={animal.name}
                        />
                    )}
                    <AvatarFallback>
                        {animal.name.slice(0, 2).toUpperCase()}
                    </AvatarFallback>
                </Avatar>
                <span className="font-medium">{animal.name}</span>
            </td>
            <td className="p-3">{specieLabel}</td>
            <td className="p-3">{t(`gender.${animal.gender}`)}</td>
            <td className="p-3 font-mono text-xs">{animal.chip}</td>
            <td className="p-3">
                {status && (
                    <Badge
                        variant={statusVariant[status.name ?? ''] ?? 'outline'}
                    >
                        {status.label}
                    </Badge>
                )}
            </td>
            <td className="max-w-xs truncate p-3 text-muted-foreground">
                {animal.personality}
            </td>
        </tr>
    );
}

export function AnimalCard({
    animal,
    onClick,
}: {
    animal: IAnimalMiniature;
    onClick: () => void;
}) {
    const { t } = useTranslation('animals');
    const { status, specieLabel, breedLabel } = useAnimalLabels(animal);

    const image = useImage(animal.image, 'icon');

    return (
        <Item
            onClick={onClick}
            variant="outline"
            className="cursor-pointer [a]:hover:bg-accent/50"
        >
            <ItemMedia>
                <Avatar>
                    {image && (
                        <AvatarImage
                            src={image.src}
                            srcSet={image.srcSet}
                            alt={animal.name}
                        />
                    )}
                    <AvatarFallback>
                        {animal.name.slice(0, 2).toUpperCase()}
                    </AvatarFallback>
                </Avatar>
            </ItemMedia>
            <ItemContent>
                <ItemHeader>
                    <ItemTitle>{animal.name}</ItemTitle>
                    {status && (
                        <Badge
                            variant={
                                statusVariant[status.name ?? ''] ?? 'outline'
                            }
                        >
                            {status.label}
                        </Badge>
                    )}
                </ItemHeader>
                <ItemDescription>
                    {specieLabel}
                    {breedLabel && <> · {breedLabel}</>}
                    {' — '}
                    {t(`gender.${animal.gender}`)}
                    {animal.chip && <> · {animal.chip}</>}
                </ItemDescription>
                {animal.personality && (
                    <ItemDescription>{animal.personality}</ItemDescription>
                )}
            </ItemContent>
        </Item>
    );
}
