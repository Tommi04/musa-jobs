<?php

use App\Models\JobOfferStatus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JobOffersStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $status = [
            [
                'label'      => 'Pubblicato',
                'code'       => 'pubblicato',
                'created_at' => $now,
                'updated_at' => $now
                //non lo mettiamo perchè possiamo fare delle policies
                // 'company_can_edit'
            ],
            [
                'label'      => 'Attenzione',
                'code'       => 'attenzione',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'label'      => 'In moderazione',
                'code'       => 'moderazione',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'label'      => 'Bloccato',
                'code'       => 'bloccato',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];

        JobOfferStatus::insert($status);
    }
}
