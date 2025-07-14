<?php

namespace App\Filament\Resources\TravelPackageResource\Api\Requests;

use App\Rules\ImageUploadRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateTravelPackageRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'name' => 'required|string',
			'slug' => 'required|string',
			'description' => 'required|string',
			'price' => 'required|integer',
			'capacity' => 'required|integer',
			'duration' => 'required|string',
			'thumbnail' => ['nullable', new ImageUploadRule(['png', 'jpg', 'jpeg'], 2048)],
			'gallery' => 'nullable|array',
			'gallery.*' => ['nullable', new ImageUploadRule(['png', 'jpg', 'jpeg'], 2048)],
			'deleted_at' => 'required'
		];
    }
}
