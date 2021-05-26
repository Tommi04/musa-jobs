<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotActiveUserNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:not-active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica gli utenti che si sono registrati da più di tre settimane e non si sono ancora attivati';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // dd('io sono il command'); //per provareù

        $now = Carbon::now();
        $limit = $now->subDays(14);
        $users = User::notActive()
                        // ->withTrashed() // quelli cancellati in softDelete
                        ->where('created_at', '<', $limit )
                        ->get();

        //count() funzione molto utile che abbiamo sulle collection che ci restituisce quanto è grosso il result set dal db
        dd($users->count());

        //se vogliamo cancellare chi non si è attivato dopo 3 settimane
        foreach ($users as $u => $user) {
            $user->forceDelete(); //per rimuovere definitivamente, non in softDelete
        }
    }
}
