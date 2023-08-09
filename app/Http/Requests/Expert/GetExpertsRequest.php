<?php

namespace App\Http\Requests\Expert;

use App\Filter\Expert\ExpertFilter;
use Illuminate\Foundation\Http\FormRequest;

class GetExpertsRequest extends FormRequest
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
        return [
            //
        ];
    }

    public function generateFilter()
    {
        $expertFilter = new ExpertFilter();

        if ($this->filled('order_by')) {
            $expertFilter->setOrderBy($this->input('order_by'));
        }

        if ($this->filled('order')) {
            $expertFilter->setOrder($this->input('order'));
        }

        if ($this->filled('per_page')) {
            $expertFilter->setPerPage($this->input('per_page'));
        }
        return $expertFilter;
    }
}
