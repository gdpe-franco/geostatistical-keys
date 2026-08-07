<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['state_code', 'name', 'short_name', 'total_population'])]
class State extends Model
{
    use SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_population' => 'integer',
        ];
    }

    public function scopeMatching(Builder $query, ?string $search): void
    {
        if ($search === null || $search === '') {
            return;
        }

        $query->where(function (Builder $query) use ($search): void {
            $query->where('state_code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('short_name', 'like', "%{$search}%");
        });
    }
}
