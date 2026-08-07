<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexStateRequest extends FormRequest
{
    public const DEFAULT_LENGTH = 10;

    public const MAX_LENGTH = 100;

    /** @var list<string> */
    private const SORT_COLUMNS = ['state_code', 'name', 'short_name', 'total_population'];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'draw' => ['nullable', 'integer', 'min:0'],
            'start' => ['nullable', 'integer', 'min:0'],
            'length' => ['nullable', 'integer', 'between:1,'.self::MAX_LENGTH],
            'search' => ['nullable', 'array'],
            'search.value' => ['nullable', 'string', 'max:120'],
            'order' => ['nullable', 'array'],
            'order.0.column' => ['nullable', 'integer', 'min:0'],
            'order.0.dir' => ['nullable', 'string'],
        ];
    }

    public function draw(): int
    {
        return (int) $this->validated('draw', 0);
    }

    public function start(): int
    {
        return (int) $this->validated('start', 0);
    }

    public function length(): int
    {
        return (int) $this->validated('length', self::DEFAULT_LENGTH);
    }

    public function search(): ?string
    {
        return $this->validated('search.value');
    }

    public function sortColumn(): string
    {
        $column = (int) $this->validated('order.0.column', 0);

        return self::SORT_COLUMNS[$column] ?? self::SORT_COLUMNS[0];
    }

    public function sortDirection(): string
    {
        return $this->validated('order.0.dir') === 'desc' ? 'desc' : 'asc';
    }
}
