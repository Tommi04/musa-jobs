<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLogoRequest;
use App\Http\Requests\UsersBySkillRequest;
use App\Http\Requests\UserSkillDeletionRequest;
use App\Http\Requests\UserSkillManagementRequest;
use App\Models\User;
use App\Models\UserDetails;
use App\Traits\ApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;
use PHPUnit\Util\Json;

class UsersController extends Controller
{
    use ApiTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //restituisce in array quello che trova nella url (la request) dopo il ?
        $queryString = request()->query();
        // dd($queryString);
        
        $sort = isset($queryString['sort']) ? $queryString['sort'] : null;
        $pageSize= isset($queryString['page_size']) ? $queryString['page_size'] : 5;

        //cambiamo $users sotto per seguire la logica della $queryString
        $users_query = User::byType('user')->with('details.skills');

        if(!is_null($sort)){
            $users_query->ordered($sort);
        }
        $users = $users_query->paginate($pageSize);

        //guardare da postman cosa da paginate(), tutte le informazioni sulle pagine
        // $users = User::byType('user')->with('details')->paginate(5);

        //dalla api dobbiamo tornare una response().
        //La response() è un oggetto http in generale quindi dobbiamo concatenarlo con ->json()
        //il code dentro all'array è meno importante del secondo parametro, se ne può fare a meno
        return response()->json(['result' => $users, 'code' => 200], 200);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //non funziona perche non si trova la relationship skills
        // $user = User::with('details.skills')->findOrFail($id);

        //o così
        // $user = UserDetails::with(['details', 'skills'])->findOrFail($id);

        //o così
        //con il ->find(=) gestisco l'eccezione qua dentro
        /*
        $user = User::byType('user')->with('details.skills')->find($id);
        if (!is_null($user)){
            return response()->json(['result' => $user, 'code' => 200], 200);
        }else{
            return response()->json(['errors' => true, 'message' => 'User not found!', 'code' => 404], 404);
        }
        */

        //mentre con il ->findOrFail() l'eccezione è gestita in app>Exceptions>Handler
        $user = User::byType('user')->with('details.skills')->findOrFail($id);

        return response()->json(['result' => $user, 'code' => 200], 200);
    }
    
    //cambio da get a post e metto la request creata da me UsersBySkillRequest
    // public function UsersBySkill( $skill_id){
    public function UsersBySkill(UsersBySkillRequest $request){
        /* Noi partiamo da User e andiamo su un proprietà di morphing quindi non funziona
        $users = User::byType('user')
                    ->with( [
                        'details' => function($query) use($skill_id){
                            $query->whereHas('skills', function($q) use($skill_id){
                                $q->where('skill_id', $skill_id);
                            })->with('skills');
                        },
                    ] )
                    ->get();
        */
        /*
        $users = User::byType('user')
                    ->whereHasMorph('details', function($query){
                        $query->whereHas('skills', function($q){
                            $q->where('skill_id', 1);
                        });
                    })->get();
        */

        // $users = UserDetails::whereHas('skills', function($q) use($skill_id){
            // $q->where('skill_id', $skill_id);
        // })->with(['user', 'skills'])->get();

        $query_inputs = $request->get('skills');

        //quindi non funziona da User con la proprietà di morphing ma funziona questa con whereHasMorph
        $users = User:://byType('user')
                        //possiamo non usare lo scopeByType visto che gli passiamo UserDetails
                        whereHasMorph('details', [UserDetails::class], function($q) use($query_inputs){
                            foreach ($query_inputs as $qi => $query_input) {
                                //il whereHas è dove questo join non è nullo
                                $q->whereHas('skills', function($query) use ($query_input){
                                    $query->where('skill_id', $query_input['id']);
                                    $query->where('level', '>=', $query_input['min_lvl']);
                                    $query->where('level', '<=', $query_input['max_lvl']);
                                    $query->where('experience_year', '>=', $query_input['min_exp']);
                                });
                            };
                        })
                        ->with('details.skills')
                        ->get();

                        
        return response()->json(['result' => $users, 'code' => JsonResponse::HTTP_OK], JsonResponse::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function uploadLogo(UserLogoRequest $request){
        $user = Auth::user();
        $user->load('details');

        if($user->hasRole('user')){
            try {
                $user_details = $user->details;

                if($user_details && $request->hasFile('logo')){   
                    DB::beginTransaction();

                    $file = $request->file('logo');
                    $filename = time() . '.' . $file->getClientOriginalExtension();
                    $file_path = 'users/' . $user_details->id . '/logo';

                    $uploaded_file = $file->storeAs($file_path, $filename, 'public');

                    $user_details->logo = $uploaded_file;

                    $user_details->save();

                    DB::commit();

                    return $this->successResponse(['message' => 'Logo ok.'], JsonResponse::HTTP_CREATED);
                }
            } catch (\Exception $e) {
                return $this->errorResponse('Errore caricamento logo utente', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }else{
            return $this->errorResponse('Errore caricamento logo utente', JsonResponse::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id);
        $user = User::findOrFail($id);
        $deleted = $user->delete();
        if ($deleted){
            return response()->json(['result' => 'Utente cancellato correttamente', 'code' => JsonResponse::HTTP_NO_CONTENT], JsonResponse::HTTP_NO_CONTENT);
        }else{
            return response()->json(
                ['error' => true, 'message' => 'Errore cancellazione utente', 'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR], 
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function restore($id){
        //per recuperare dei modelli cancellati in soft deleting gli va dato withTrashed()
        $user = User::withTrashed()->findOrFail($id);
        $user_restored = $user->restore();
        // dd($user_restored);
        if ($user_restored){
            return response()->json(
                [ 'result' => 'Utente ripristinato correttamente', 'code' => JsonResponse::HTTP_OK],
                JsonResponse::HTTP_OK);
            }else{
            return response()->json(
                [ 'error' => true, 'message' => 'Errore ripristino utente', 'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addSkill(UserSkillManagementRequest $request){
        // dd($request->all());    
        // Auh::User(); //torna lo user loggato
        // Auth::id(); //torna l'id dell'utente loggato

        //scope byType che torna solo 'user'
        $user = User::byTyep('user')
                    //fai il with con details che a sua volta lo fa con skills
                    ->with('details.skills')
                    //findOrFail trova o fallisce la query
                    ->findOrFail(Auth::id());
        
        $extras = [
            'level'             => $request->lvl,
            'experience_year'   => $request->exp_years,
        ];

        try {
            // $result = $user->details->skills()->attach($request->skill_id, $extras);

            // $result = $user->details->skills()->sync([$request->skill_id => $extras], false);
            //uguali
            //sta facendo l'update, evitiamo di gestire attach o detach, se ha già la skill farà da solo update
            $result = $user->details->skills()->syncWithoutDetaching([$request->skill_id => $extras]);
            
            if(count($result['attached']) > 0){
                return $this->successResponse('Skill aggiunta con successo', JsonResponse::HTTP_CREATED);
            }else if (count($result['update']) > 0){
                return $this->successResponse('Skill aggiornata con successo', JsonResponse::HTTP_OK);
            }else{
                return $this->successResponse('Nessuna skill coninvolta', JsonResponse::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Errore nel salvataggio skill utante', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            // $result = $e->getMessage();
        }
        dd($result);
    }

    public function removeSkill(UserSkillDeletionRequest $request){
        $user = User::byType('user')
                    ->with('details.skills')
                    ->findOrFail(Auth::all());

        //al detach va passato un array
        $result = $user->details->skills()->detach($request->skill_id);
        dd($result);

        // === 1 perchè rimuoviamo una sola skill
        if ($result === 1 ){
            return $this->successResponse('Skill rimossa con successo', JsonResponse::HTTP_OK);
        }else{
            return $this->errorResponse('Errore rimozione skill', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
