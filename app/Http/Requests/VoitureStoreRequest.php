<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoitureStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //return false;
        return true ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if(request()->isMethod('post')){
            return[
                'marque'=>'required|string|max:258',
                'modele'=>'required|string|max:258',
                'type'=>'required|string|max:258',
                'matricule'=>'required|string|max:258',
                'VIN'=>'required|string|max:258',
                'image'=>'required|image|mimes:jpeg,png,jpg,gif,svg',
                'date_de_vignette' => 'required|date', // Add this line
                'date_d_assurance' => 'required|date',
            ];
        } else {
            return[
                'marque'=>'required|string|max:258',
                'modele'=>'required|string|max:258',
                'type'=>'required|string|max:258',
                'matricule'=>'required|string|max:258',
                'VIN'=>'required|string|max:258',
                'image'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'date_de_vignette' => 'required|date', // Add this line
                'date_d_assurance' => 'required|date',

            ];
        }
    }
     
    public function messages(){
        if(request()->isMethod('post')){
            return[
                'marque.required'=>'La marque est obligatoire',
                'modele.required'=>'Le modèle est obligatoire',
                'type.required'=>'Le type est obligatoire',
                'matricule.required'=>'La matricule est obligatoire',
                'VIN.required'=>'Le VIN est obligatoire',
                'image.required'=>'La photo est obligatoire',
                'date_de_vignette' => 'required|date', // Add this line
                'date_d_assurance' => 'required|date',
            ];
        } else{
            return[
                'marque.required'=>'La marque est obligatoire',
                'modele.required'=>'Le modèle est obligatoire',
                'type.required'=>'Le type est obligatoire',
                'matricule.required'=>'La matricule est obligatoire',
                'VIN.required'=>'Le VIN est obligatoire',
                'date_de_vignette' => 'required|date', // Add this line
                'date_d_assurance' => 'required|date',
            ];
        }
    }
}
