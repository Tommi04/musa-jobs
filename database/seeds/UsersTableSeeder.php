<?php

use App\Models\Role;
use App\Models\User;
use App\Models\UserDetails;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createAdmins();
        $this->createSomeUsers(20);
    }

    public function createAdmins(){
        $admin_role = Role::where('code', 'admin')->first();

        $user_data = [
            'first_name'        => 'Tommaso',
            'last_name'         => 'Piccinini',
            'email'             => 'tommaso.piccio95@gmail.com',
            'username'          => 'tommaso.piccinini',
            'password'          => Hash::make('password'),
            'privacy'           => 1,
            'role_id'           => $admin_role->id,
            'email_verified_at' => Carbon::now(),
        ];
        
        User::create($user_data);
    }

    private function createSomeUsers($qty){
        $faker = Faker\Factory::create('it_IT');
        $user_role = Role::where('code', 'user')->first();
        // $users = [];
        $now = Carbon::now();

        for ($i=0; $i < $qty; $i++) { 

            try{
                DB::beginTransaction();       
                
                /*
                $user_details = [
                    'bio'           => $faker->text(300),
                    'birth_date'    => null,
                    'phone'         => null,
                    'gender'        => null,
                    'city'          => $faker->city()
                ];

                $user_details = UserDetails::create($user_details);

                $first_name = $faker->firstName;
                $last_name = $faker->lastName;
                $username = strtolower(str_replace(' ', '.', $first_name)) . '.' . strtolower(str_replace(' ', '.', $last_name)) . '@' . $faker->freeEmailDomain;
                $email = $username . '@' . $faker->freeEmailDomain;

                $user_data= [
                    'first_name'        => $first_name,
                    'last_name'         => $last_name,
                    'email'             => $email,
                    'username'          => $username,
                    'password'          => Hash::make('password'),
                    'privacy'           => 1,
                    'role_id'           => $user_role->id,
                    'email_verified_at' => $now,
                    // 'details_id'        => $user_details->id,
                    // 'details_type'      => 'App\Models\UserDetails',
                ];

                $user = User::create($user_data);

                $user_details->user()->save($user);   
                */
                
                //dobbiamo però pensare al front-end in cui prima si crea l'utente e poi lo si associa al dettaglio

                $first_name = $faker->firstName;
                $last_name = $faker->lastName;
                $username = strtolower(str_replace(' ', '.', $first_name)) . '.' . strtolower(str_replace(' ', '.', $last_name)) . '@' . $faker->freeEmailDomain;
                $email = $username . '@' . $faker->freeEmailDomain;

                $user_data= [
                    'first_name'        => $first_name,
                    'last_name'         => $last_name,
                    'email'             => $email,
                    'username'          => $username,
                    'password'          => Hash::make('password'),
                    'privacy'           => 1,
                    'role_id'           => $user_role->id,
                    'email_verified_at' => $now,
                    // 'details_id'        => $user_details->id,
                    // 'details_type'      => 'App\Models\UserDetails',
                ];

                $user = User::create($user_data);
                
                $user_details_data = [
                    'bio'           => $faker->text(300),
                    'birth_date'    => null,
                    'phone'         => null,
                    'gender'        => null,
                    'city'          => $faker->city()
                ];

                $user_details = UserDetails::create($user_details_data);

                //in questo modo creerà comunque prima il dettaglio, 
                //mma non persiste nel DB se non facciamo ->save()
                $user->details()->associate($user_details)->save();

                
                DB::commit();
            } catch (\Exception $e){
                DB::rollBack();
            }

        }

        // User::insert($users);
    }
}
