<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //non funziona questa cosa, perchè non abbiamo nulla nel join in User, la classe viene istanziata al ->find() dentro il
        //modello e non al join (->with('details')). User::with('details')->find(2);
        //mentre qua dentro siamo al -with('details').
        //$user = User::with('details')->find(2);
        //SERVE RELAZIONE POLIMORFICA CON CUI POSSO PRENDERE SIA user_details che company_details
        // andando a scrivere nel DB il model da prendere nella colonna details_type

        
        
        $users = User::byRole(2)->get();
        return view('admin.users.list', compact('users'));
        

        //facciamo una sola query per prendere tutti i dettagli
        // $users = User::with('details')->limit(3)->get();
        // dd($users);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
