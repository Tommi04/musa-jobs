<?php

namespace App\Http\Requests;

use App\Traits\ApiTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CompanyRegistrationRequest extends FormRequest
{
    use ApiTrait;
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
                'first_name'        => 'required|max:255',
                'last_name'         => 'required|max:255',
                'company_name'      => 'required|min:3|max:255',
                'email'             => 'required|email|max:255|unique:users,email',
                'password'          => 'required|min:8|max:255|alpha_num',
                'confirm_password'  => 'required|same:password',
                'category'          => 'required|integer|exists:categories,id'
        ];
    }
    
    //failValidation è un hook per gestire gli errori, prende un'istanza di Validator 
    protected function failedValidation(Validator $validator)
    {
        //siccome stiamo gestendo le API mettiamo tutto dentro un expectsJson()
        if( $this->expectsJson()){
            //dentro la variabile $errors vengono salvati gli errori errors() del validatore
            //estrae l'error bag del validatore
            $errors = (new ValidationException($validator))->errors();
            dd($errors);
            //lanciamo un'eccezione di tipo HttpResponseException a cui passiamo una response di tipo json che si aspetta un array di dati
            throw new HttpResponseException(
                $this->errorResponse($errors, JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }else{
            //se non siamo da API gli ritorniamo semplicemente il failedValidator del parent, FormRequest
            return parent::failedValidation($validator);
        }
    }
}
 