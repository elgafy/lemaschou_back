<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\App;

class SendContactRequest extends FormRequest
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
        $lang = getLang();
        App::setLocale($lang);
        return [
            'name' => 'required',
            'email' => 'required|string|email',
            'phone' => 'required',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator): void
    {
        $response = response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'data' => []
        ], 200);

        throw new HttpResponseException($response);
    }
}
