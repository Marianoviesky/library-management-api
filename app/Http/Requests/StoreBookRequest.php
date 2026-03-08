<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'isbn' => 'required|string|unique:books,isbn',
            'author_id' => 'required|exists:authors,id',
            'price' => 'required|integer|min:0', // Le prix doit être en centimes
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id' // Vérifie que chaque ID de catégorie existe
        ];
    }
}
