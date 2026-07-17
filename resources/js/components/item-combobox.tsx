import {
    Combobox,
    ComboboxContent,
    ComboboxEmpty,
    ComboboxInput,
    ComboboxItem,
    ComboboxList,
} from '@/components/ui/combobox';
import { useMemo } from 'react';

export interface ItemComboboxOption {
    value: string;
    label: string;
    swatch?: string;
}

export function ItemCombobox({
    id,
    name,
    options,
    value,
    onValueChange,
    placeholder,
    emptyText,
    disabled,
    clearable = true,
    required,
    'aria-invalid': ariaInvalid,
}: {
    id?: string;
    name: string;
    options: ItemComboboxOption[];
    value: string | null;
    onValueChange: (value: string | null) => void;
    placeholder?: string;
    emptyText?: string;
    disabled?: boolean;
    clearable?: boolean;
    required?: boolean;
    'aria-invalid'?: boolean;
}) {
    const optionByValue = useMemo(
        () => new Map(options.map((option) => [option.value, option])),
        [options],
    );

    return (
        <Combobox<string>
            items={options.map((option) => option.value)}
            value={value}
            onValueChange={onValueChange}
            itemToStringLabel={(itemValue) =>
                optionByValue.get(itemValue)?.label ?? itemValue
            }
            name={name}
            disabled={disabled}
            required={required}
        >
            <ComboboxInput
                id={id}
                placeholder={placeholder}
                showClear={clearable}
                disabled={disabled}
                aria-invalid={ariaInvalid}
            />
            <ComboboxContent>
                <ComboboxEmpty>{emptyText}</ComboboxEmpty>
                <ComboboxList>
                    {(itemValue: string) => {
                        const option = optionByValue.get(itemValue);
                        return (
                            <ComboboxItem key={itemValue} value={itemValue}>
                                {option?.swatch && (
                                    <span
                                        className="size-3 shrink-0 rounded-full border"
                                        style={{
                                            backgroundColor: option.swatch,
                                        }}
                                    />
                                )}
                                {option?.label}
                            </ComboboxItem>
                        );
                    }}
                </ComboboxList>
            </ComboboxContent>
        </Combobox>
    );
}
