<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\Dto\Target;
use Illuminate\Foundation\Http\FormRequest;

class ParseBestChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'directions' => 'required|array',
            'directions.*.from' => 'required|integer',
            'directions.*.to' => 'required|integer',
            'directions.*.city' => 'required|integer',
        ];
    }

    /**
     * @return Target[]
     */
    public function targets(): array
    {
        $targets = [];

        foreach ($this->input('directions') as $direction) {
            $target = Target::fromArray($direction);
            $targets[$target->uid()] = $target;
        }

        return $targets;
    }
}
