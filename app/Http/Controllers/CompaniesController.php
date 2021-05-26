<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyLogoRequest;
use App\Models\Category;
use App\Models\Company;
use App\Traits\ApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompaniesController extends Controller
{
    use ApiTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //guardare da postman cosa da paginate(), tutte le informazioni sulle pagine
        $companies = Company::paginate(5);

        return response()->json(['result' => $companies, 'code' => 200], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::findOrFail($id);

        return response()->json(['result' => $company, 'code' => 200], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    public function uploadLogo(CompanyLogoRequest $request){
        $user = Auth::user();
        $user->load('details');

        if( $user->hasRole('company')){
            try {                
                //ho creat una variabile $company che è uguale a quello che c'è dentro detail di user
                $company = $user->details;
                
                if ($company && $request->hasFile('logo')){
                    DB::beginTransaction();

                    $file = $request->file('logo');
                    $filename = time() . '.' . $file->getClientOriginalExtension();

                    $file_path = 'companies/' . $company->id . '/logo';

                    $uploaded_file = $file->storeAs($file_path, $filename, 'public');

                    $company->logo = $uploaded_file;

                    $company->save();
                    
                    DB::commit();

                    return $this->successResponse(['message' => 'Logo ok.'], JsonResponse::HTTP_CREATED);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->errorResponse('Errore caricamento logo azienda.', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }else{
            return $this->errorResponse('Errore caricamento logo azienda.', JsonResponse::HTTP_FORBIDDEN);
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
        //
    }

    public function allCategories(){
        //puck() estrae solo un oggetto con le colonne che gli passiamo. Include il get() quindi non dobbiamo concatenarlo
        // $categories = Category::puck('label', 'id');
        
        //ma noi sostituiamo il pluck() con select() perchè con pluck() torna un oggetto prop:valore
        //mentre a noi serve un array che torni key => valore. E questo lo fa select(). Vanno invertite le proprietà
        $categories = Category::select('id', 'label')->ordered()->get();
        // SELECT 'id', 'label' FROM Category

        //ciò lo dobbiamo fare perchè nel frontend leggiamo il ritorno com un array, non come un oggetto.


        return $this->successResponse(['categories' => $categories], JsonResponse::HTTP_OK);
    }
}
