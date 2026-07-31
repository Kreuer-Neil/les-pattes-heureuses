import { IAttentionItem } from '@/types';
import { useTranslation } from 'react-i18next';

export function AttentionItemLabel({ item }: { item: IAttentionItem }) {
    const { t } = useTranslation('notifications');

    if (item.type === 'adoption_request') {
        return (
            <>
                {t('types.adoption_request', {
                    animal: item.animal.name,
                    adopter: `${item.adopterProfile.firstName} ${item.adopterProfile.lastName}`,
                })}
            </>
        );
    }

    return (
        <>
            {t('types.animal_change', {
                proposer: item.proposerName ?? '—',
                action: t(`changeActions.${item.action}`),
                animal: item.animalName ?? '—',
            })}
        </>
    );
}