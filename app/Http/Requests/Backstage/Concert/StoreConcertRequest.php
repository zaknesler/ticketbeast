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
            'title' => ['required'],
            'subtitle' => ['nullable'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:g:ia'],
            'venue' => ['required'],
            'venue_address' => ['required'],
            'city' => ['required'],
            'state' => ['required'],
            'zip' => ['required'],
            'additional_information' => ['nullable'],
            'ticket_price' => ['required', 'numeric', 'min:5'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
            'poster_image' => ['nullable', 'image'],
        ];
    }
}
