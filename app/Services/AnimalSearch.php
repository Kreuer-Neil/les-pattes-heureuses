<?php

namespace App\Services;

use App\Enums\Animals\Gender;
use App\Models\FurColor;
use App\Models\Specie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

readonly class AnimalSearch
{
    // To return an object instead of an array
    private function __construct(
        public Collection $exact,
        public Collection $close,
        public bool       $hasCriteria,
    ) {}

    /**
     * Filters animals with a 1 criteria error span
     * Counts every criterion as 0 or 1 then checks if the sum >= the number of filled criteria - 1
     */
    public static function search(array $filters, Builder $animalQuery): self
    {
        $q = self::trimmedOrNull($filters['q'] ?? null);
        $breed = self::trimmedOrNull($filters['breed'] ?? null);
        $specieNames = self::resolveLabels($filters['specie'] ?? null, self::specieLabels());
        $colorNames = self::resolveLabels($filters['color'] ?? null, self::colorLabels());
        $genderCodes = self::resolveLabels($filters['gender'] ?? null, self::genderLabels());
        $age = isset($filters['age']) && $filters['age'] !== '' ? (float) $filters['age'] : null;

        // The array of cases where data matches existing animal and returns either 1 or 0
        $cases = [];
        // Using bindings so that values get never interpreted as SQL syntax -> avoid user hacking SLQ database.
        $bindings = [];
        $total = 0;

        if ($q !== null) {
            $cases[] = 'CASE WHEN animals.name LIKE ? THEN 1 ELSE 0 END';
            $bindings[] = '%'.$q.'%';
            $total++;
        }

        if ($filters['specie'] ?? null) {
            $cases[] = self::inCase('species.name', $specieNames);
            $bindings = [...$bindings, ...$specieNames];
            $total++;
        }

        // Breed names are stored exactly as displayed (just title-cased for display via
        // Breed::label()), so a direct LIKE works without a resolveLabels() detour.
        if ($breed !== null) {
            $cases[] = 'CASE WHEN breeds.name LIKE ? THEN 1 ELSE 0 END';
            $bindings[] = '%'.$breed.'%';
            $total++;
        }

        if ($filters['color'] ?? null) {
            $cases[] = self::inCase('fur_colors.name', $colorNames);
            $bindings = [...$bindings, ...$colorNames];
            $total++;
        }

        if ($filters['gender'] ?? null) {
            $cases[] = self::inCase('animals.gender', $genderCodes);
            $bindings = [...$bindings, ...$genderCodes];
            $total++;
        }

        // Checking age with an already +1/-1 year range, dates computed in PHP
        if ($age !== null) {
            $cases[] = 'CASE WHEN animals.born_at BETWEEN ? AND ? THEN 1 ELSE 0 END';
            $bindings[] = Carbon::now()->subYears($age + 1)->addDay()->toDateString();
            $bindings[] = Carbon::now()->subYears(max($age - 1, 0))->toDateString();
            $total++;
        }

        // LEFT JOIN: specie_id/breed_id/fur_color_id are nullable, and each is a belongsTo
        // (at most one row), so no risk of duplicate animal rows. select('animals.*') avoids
        // ambiguous columns now that species/breeds/fur_colors are joined in too.
        $query = $animalQuery
            ->leftJoin('species', 'species.id', '=', 'animals.specie_id')
            ->leftJoin('breeds', 'breeds.id', '=', 'animals.breed_id')
            ->leftJoin('fur_colors', 'fur_colors.id', '=', 'animals.fur_color_id')
            ->with(['specie', 'breed', 'furColor'])
            ->select('animals.*')
            ->orderBy('animals.name');

        // Returns normal query if no search params
        if ($total === 0) {
            return new self(exact: $query->get(), close: collect(), hasCriteria: false);
        }

        // Merging all cases together for the SQL request as an int column "match_score" on the returned results
        $scoreSql = implode(' + ', $cases);
        $query->selectRaw("({$scoreSql}) as match_score", $bindings)
        // GROUP BY animals.id is required for HAVING.
            ->groupBy('animals.id')
            ->having('match_score', '>=', max($total - 1, 0));

        $animals = $query->get();

        // Separates perfectly-matching and close-matching animals using the match_score
        return new self(
            exact: $animals->where('match_score', $total)->values(),
            // If at least 2 criteria, add "close" matches where match score is 1 away from matching exactly.
            close: $total >= 2 ? $animals->where('match_score', $total - 1)->values() : collect(),
            hasCriteria: true,
        );
    }

    /**
     * Returns a "CASE WHEN [column] IN [values]", or a false case if no values are passed.
     */
    private static function inCase(string $column, array $values): string
    {
        if ($values === []) {
            return 'CASE WHEN 1 = 0 THEN 1 ELSE 0 END';
        }

        // Makes a list of ? replaced with bindings (count($values) gets the number of values that'll be in bindings so the count stays right)
        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        return "CASE WHEN {$column} IN ({$placeholders}) THEN 1 ELSE 0 END";
    }

    /**
     * Resolves free text typed against a datalist (e.g. "blanc") back to the raw DB
     * values it loosely matches (e.g. "white").
     */
    private static function resolveLabels(?string $input, Collection $labels): array
    {
        $input = self::trimmedOrNull($input);

        if ($input === null) {
            return [];
        }

        return $labels
            ->filter(fn (string $label) => self::fuzzyMatches($label, $input))
            ->keys()
            ->values()
            ->all();
    }

    /**
     * Compares strings and returns true if strings are the same/similar enough.
     */
    private static function fuzzyMatches(string $target, string $value): bool
    {
        $target = Str::lower(Str::ascii($target));
        $value = Str::lower(Str::ascii($value));

        if (str_contains($target, $value) || str_contains($value, $target)) {
            return true;
        }

        // Changes strings diff tolerance depending on the length of the compared strings
        $tolerance = max(1, (int) floor(min(mb_strlen($target), mb_strlen($value)) / 3));

        // PHP fn to check characters difference between 2 strings
        return levenshtein($target, $value) <= $tolerance;
    }

    private static function trimmedOrNull(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /** @return Collection<string, string> raw specie name => translated label */
    private static function specieLabels(): Collection
    {
        return Specie::pluck('name')->mapWithKeys(fn (string $name) => [$name => __('client.species.'.$name)]);
    }

    /** @return Collection<string, string> raw fur color name => translated label */
    private static function colorLabels(): Collection
    {
        return FurColor::pluck('name')->mapWithKeys(fn (string $name) => [$name => __('client.colors.'.$name)]);
    }

    /** @return Collection<string, string> raw gender code => translated label */
    private static function genderLabels(): Collection
    {
        return collect(Gender::cases())->mapWithKeys(fn (Gender $gender) => [$gender->value => __('client.animal.genders.'.$gender->value)]);
    }
}
