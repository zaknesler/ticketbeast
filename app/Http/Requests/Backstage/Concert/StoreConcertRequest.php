<?php

namespace App\Http\Requests\Backstage\Concert;

use Illuminate\Foundation\Http\FormRequest;

class StoreConcertRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'subtitle' => 'nullable|string',
            'date' => 'required|string',
            'time' => 'required|string',
            'ticket_price' => 'required|numeric|min:0',
            'venue' => 'required|string',
            'venue_address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip' => 'required|string',
            'additional_information' => 'nullable|string',
            'ticket_quantity' => 'required|integer|min:1',
        ];
    }
}
