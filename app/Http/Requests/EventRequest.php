<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $verb = $this->method();

        $routeURI = $this->route()->getName();

        if ($verb === 'GET') {

            if ($routeURI === 'dashboard') {
                return [
                    'start_date' => 'sometimes|date',
                    'end_date' => 'sometimes|date',
                    'keywords' => 'sometimes|string',
                ];
            }
        } elseif ($verb === 'POST' || $verb === 'PUT') {
            return [
                'title' => 'required|string',
                'description' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'color' => 'required|string',
            ];
        }

        return [];
    }
}
