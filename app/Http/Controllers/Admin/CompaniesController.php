<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        $companies = Company::get();
        //qua dentro ho l'utente proprietario della compagnia
        // $companies = CompanyDetails::with('user')->get();
        // dd($companies);

        return view('admin.companies.list', compact('companies'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::with([
            'user', 
            'category',
            //per annidiare le query
            'jobOffers' => function($q){
                $q->with( [
                    'statusHistory' => function($q){
                        $q->OrderByStatusHistory($q);
                    }
                ]);
            }
        ])->findOrFail($id);

        return view('admin.companies.details', compact('company'));
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
}
