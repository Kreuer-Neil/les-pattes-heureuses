type ImageType = 'animals' | 'users';
type ImageSize = 'icon' | 'full';

export interface IImageSource {
    src: string;
    srcSet?: string;
}

export function useImage(
    image: string | null | undefined,
    size: ImageSize,
    type: ImageType = 'animals',
): IImageSource | null {
    if (!image) {
        return null;
    }

    const path = (variant: string) =>
        `/storage/images/${type}/${variant}/${image}.webp`;

    if (size === 'full') {
        return {
            src: path('full'),
            srcSet: `${path('full-xs')} 320w, ${path('full')} 720w`,
        };
    }

    return {
        src: path('small'),
        srcSet: `${path('small')} 1x, ${path('medium')} 2x, ${path('large')} 3x`,
    };
}
