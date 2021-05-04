<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CreateJobOfferRequest extends FormRequest
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
            'role'          => 'required|string|max:255',
            'description'   => 'required|string',
            // 'status'     => 'required|in:0,1', //lasciamolo stare per ora, non costringiamo l'utente a passarlo tramite api
            'company_id'    => 'required|exists:companies,id',
            'valid_from'    => 'required|date|after_or_equal:now|date_format:Y-m-d H:i',
            'valid_to'  => 'required|date|after:valid_from|date_format:Y-m-d H:i',
            //per mettere anche le skills in chiamata API
            'skills'    => 'required|array|min:1',
            //in questo modo gli dichiamo che qualunque id degli elementi dell'array di skills è richiesto e deve esistere nella colonna id di skills
            //la validazione è per blocco di array, il primo magari passa e il secondo no e il terzo si
            //distinct serve per dire che deve essere univoco
            'skills.*.id' => 'requred|exists:skills,id|distinct',
            'skills.*.min_level' => 'required|in: 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5',
            //gte sta per greaterthanequal e validerà sull'array in cui sta con *
            'skills.*.max_level' => 'required|in: 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5|gte:skill.*.min_level',
            'skills.*.min_years' => 'required|integer|between:1,10',
        ];
    }

    //scriviamo extra logica che ci permetta di gestire i dati dal controller
    //quando da una api chiamiamo un controller che ha una request, fa solo il codice della request
    public function message(){
        return [
          'required' => ':attribute è richiesto!'
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
            throw new HttpResponseException(response()
                ->json([
                        'errors' => true, 
                        'error _messages' => $errors, 
                        'code' => 422
                    // JsonResponse estende delle varianti di classe ed ognuna corrisponde ad un codice HTTP,
                    // per vedere queste varianti entrare dentro dentro dentro
                    //con :: possiamo chiamare sia i metodi statici che le costanti di classe
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
                    //SAREBBE IL CODICE 422
        }else{
            //se non siamo da API gli ritorniamo semplicemente il failedValidator del parent, FormRequest
            return parent::failedValidation($validator);
        }
    }
}
