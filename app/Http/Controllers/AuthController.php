<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistrationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth')->only('logout');
    }

    // public function registerUser(Request $request){
    public function registerUser(UserRegistrationRequest $request){
        // dd($request->all());

        /*SOSTITUITO CON FORM REQUEST
        $validator = Validator::make($request->all(),
            [
                'first_name'        => 'required|max:255',
                'last_name'         => 'required|max:255',
                'email'             => 'required|email|max:255|unique:users,email',
                'password'          => 'required|min:8|max:255|alpha_num',
                'confirm_password'  => 'required|same:password'
            ]
        );

        if($validator->fails()){
            return response()->json(['error' => true, 'message' => $validator->errors(), 'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        */

        try{
            DB::beginTransaction();
            
            $username = strtolower(str_replace(' ', '.', $request->first_name)) . '.' . strtolower(str_replace(' ', '.', $request->get('last_name')));

            $input_data = [
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'username'      => $username,
                'role_id'       => 2
            ];

            $user = User::create($input_data);

            $input_details_data = [
                'bio'           => '',
                'birth_date'    => null,
                'phone'         => null,
                'gender'        => null,
                'city'          => null
            ];

            $user_details = UserDetails::create($input_details_data);

            //associamo dettagli e utente
            $user->details()->associate($user_details);

            return response()->json(['result' => $user, 'code' => JsonResponse::HTTP_CREATED], JsonResponse::HTTP_CREATED);

            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(
                    [
                        'error' => true, 
                        'message' => 'Errore nello stabilire la connessione al DB. Riprovare più tardi o ricontattare l\'amministratore', 'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
                    ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function userLogin(Request $request){
        // dd($request->all());

        $validator = Validator::make($request->all(),
            [
                'email'             => 'required|email',
                'password'          => 'required'
            ]
        );

        if ($validator->fails()){
            return response()->json(
                [
                    'error' => true, 'message' => $validator->errors(), 
                    'status' => JsonResponse::HTTP_BAD_REQUEST
                ], 
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role_id' => 2 ])){
            //valorizzerà user come utente appena loggato
            $user = Auth::user();

            $result = [
                'token'     => $user->createToken('token')->accessToken,
                'userData'  => ''
            ];
            return response()->json(['resulte' => $result, 'code' => JsonResponse::HTTP_OK], JsonResponse::HTTP_OK);
            //tutto ciò creerà un token da passare poi ogni richiesta fatta


        }else{
            return response()->json(['error' => true, 'message' => 'Login non valido', 'code', JsonResponse::HTTP_UNAUTHORIZED], JsonResponse::HTTP_UNAUTHORIZED);
        }

    }

    public function registerCompany(Request $request){

    }

    public function companyLogin(Request $request){

    }

    public function logout(){

    }

    public function myProfile(){
        // dd('my profile');

        $user = Auth::user();
        $result = [
            'userData'  => $user,
        ];
        return response()->json(['resulte' => $result, 'code' => JsonResponse::HTTP_OK], JsonResponse::HTTP_OK);

    }
}
