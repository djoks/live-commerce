<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Product sort options for shop listings.
 */
enum ProductSortOption: string
{
    case Default = 'default';
    case Newest = 'newest';
    case PriceAsc = 'price_asc';
    case PriceDesc = 'price_desc';

    /**
     * Get human-readable label for display in dropdowns.
     */
    public function label(): string
    {
        return match ($this) {
            self::Default => 'Default',
            self::Newest => 'Newest',
            self::PriceAsc => 'Price: Low to High',
            self::PriceDesc => 'Price: High to Low',
        };
    }

    /**
     * Get all options as an associative array for select dropdowns.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
