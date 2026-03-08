<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends BaseFormRequest
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
        // On récupère le livre depuis l'URL (ex: /api/v1/books/1)
        $bookId = $this->route('book') ? $this->route('book')->id : null;

        return [
            'title' => 'sometimes|string',
            'isbn' => 'sometimes|string|unique:books,isbn,' . $bookId,
            'author_id' => 'sometimes|exists:authors,id',
            'price' => 'sometimes|integer|min:0',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id'
        ];
    }
}
