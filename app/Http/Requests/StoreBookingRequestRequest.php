<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photographer_id' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('slug', 'photographer')))],
            'album_id' => ['nullable', 'integer', Rule::exists('albums', 'id')],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'desired_date_start' => ['nullable', 'date', 'after_or_equal:today'],
            'desired_date_end' => ['nullable', 'date', 'after:desired_date_start'],
            'budget_notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
