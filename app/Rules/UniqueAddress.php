<?php

namespace App\Rules;

use App\Models\Address;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Evita direcciones duplicadas: misma calle y número + ciudad + código postal.
 * La comparación depende de la collation de la BD (en MySQL *_ci ignora mayúsculas y acentos).
 */
class UniqueAddress implements ValidationRule
{
    public function __construct(
        private mixed $cityId,
        private mixed $postalCode,
        private ?int $ignoreId = null,
    ) {
    }

    public static function normalize(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(preg_replace('/\s+/u', ' ', (string) $value));

        return $value === '' ? null : $value;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $postalCode = self::normalize($this->postalCode);

        $exists = Address::query()
            ->where('address', self::normalize($value))
            ->where('city_id', $this->cityId)
            ->where('postal_code', $postalCode)
            ->when($this->ignoreId, fn ($query) => $query->whereKeyNot($this->ignoreId))
            ->exists();

        if ($exists) {
            $fail('Ya existe una dirección registrada con la misma calle y número, ciudad y código postal.');
        }
    }
}
