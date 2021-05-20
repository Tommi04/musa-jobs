<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistrationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\UserDetails;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use ApiTrait;
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
            return $this->errorResponse($validator->errors(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
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

            // return response()->json(['result' => $user, 'code' => JsonResponse::HTTP_CREATED], JsonResponse::HTTP_CREATED);
            return $this->successResponse($user, JsonResponse::HTTP_CREATED);
            
            DB::commit();
            
        }catch(\Exception $e){
            DB::rollBack();
            return $this->errorResponse('Errore nello stabilire la connessione al DB. Riprovare più tardi o ricontattare l\'amministratore', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            /*return response()->json(
                    [
                        'error' => true, 
                        'message' => 'Errore nello stabilire la connessione al DB. Riprovare più tardi o ricontattare l\'amministratore', 'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
                    ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR); */
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
            return $this->errorResponse($validator->errors(), JsonResponse::HTTP_BAD_REQUEST);
            /*return response()->json(
                [
                    'error' => true, 'message' => $validator->errors(), 
                    'status' => JsonResponse::HTTP_BAD_REQUEST
                ], 
                JsonResponse::HTTP_BAD_REQUEST
            );*/
        }

        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role_id' => 2 ])){
            //valorizzerà user come utente appena loggato
            $user = Auth::user();

            $result = [
                'token'     => $user->createToken('token')->accessToken,
                'userData'  => ''
            ];

            //potremmo anche mandare un evento al listener AppServiceProvider per registrare l'utente
            // event('UserRegisterd');

            // return response()->json(['result' => $result, 'code' => JsonResponse::HTTP_OK], JsonResponse::HTTP_OK);
            return $this->successResponse($result, JsonResponse::HTTP_OK);
            //tutto ciò creerà un token da passare poi ogni richiesta fatta
            
            
        }else{
            return $this->errorResponse('Login non valido', JsonResponse::HTTP_UNAUTHORIZED);
            // return response()->json(['error' => true, 'message' => 'Login non valido', 'code', JsonResponse::HTTP_UNAUTHORIZED], JsonResponse::HTTP_UNAUTHORIZED);
        }

    }

    public function registerCompany(Request $request){

    }

    public function companyLogin(Request $request){

    }

    public function logout(Request $request){
        // dd($request->user()); //fa da solo il decode del token
        // dd($request->user()->token()); //restituisce la riga del db con il token associato
        // dd($request->user()->token()->revoke()); //revochiamo il token
        //revochiamo il token ma non viene cancellato
        /* $revoked = $request->user()->token()->revoke();
        if ($revoked) {
            return response()->json(['result' => 'Utente sloggato con successo', 'code' => JsonResponse::HTTP_OK], JsonResponse::HTTP_OK);
        }else{
            return response()->json(['result' => 'Errore in logout', 'code' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        */

        //cancelliamo il token
        //dichiaro una variabile di controllo che parte da 0
        $revoked_token_counter = 0;
        //prendiamo la proprietà array token e scorre ogni token all'interno dell'array con il metodo each()
        //passiamo la proprietà dentro al control con use ma anteponiamo al nome della variabile la "&"
        //questo significa che io non sto passando esattamente la variabile ma l'allocazione di memoria fisica di quella variabile, una referenza un puntatore alla variabile
        $request->user()->token->each(function($token, $key) use(&$revoked_token_counter){
            //qua andiamo a cancellare il token
            //se il delete() è andato a buon fine torna true, altrimenti torna false
            $deleted = $token->delete();
            if ($deleted){
                //user $revoked_token_counter come una variabile normale e non come un contatore, si fa uno scope locale
                //il +1 lo fa però sull'allocazione di memoria che gli abbiamo passato in use
                $revoked_token_counter++;
            }
        });
        dd($revoked_token_counter);
        
        if ($revoked_token_counter) {
            return $this->successResponse('Utente sloggato con successo', JsonResponse::HTTP_OK);
            // return response()->json(['result' => 'Utente sloggato con successo', 'code' => JsonResponse::HTTP_OK], JsonResponse::HTTP_OK);
        }else{
            return $this->errorResponse('Errore in logout', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            // return response()->json(['result' => 'Errore in logout', 'code' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function myProfile(){
        // dd('my profile');

        $user = Auth::user();
        $result = [
            'userData'  => $user,
        ];
        return $this->successResponse($result, JsonResponse::HTTP_OK);
        // return response()->json(['resulte' => $result, 'code' => JsonResponse::HTTP_OK], JsonResponse::HTTP_OK);

    }
}
