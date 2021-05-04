<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createCompanies('20');
    }

    private function createCompanies($qty){
        $faker = Faker\Factory::create('it_IT');
        $now = Carbon::now();

        //all() è senza where e/o relationship
        //altrimenti usiamo get()

        $categories = Category::all();

        for ($i=0; $i < $qty; $i++) { 
            try {
                DB::beginTransaction();
                $random_categories = $categories->random(1);
    
                $company_data = [
                'name'          => $faker->company,
                'description'   => $faker->text(200),
                'city'          => $faker->city,
                'website'       => $faker->url,
                'logo'          => '',
                'category_id'   => $random_categories[0]->id,
                ];
    
                $company = Company::create($company_data);
    
                $user_data =[
                    'first_name'        => $faker->firstName,
                    'last_name'         => $faker->lastName,
                    'email'             => $faker->email,
                    'username'          => $faker->userName,
                    'password'          => Hash::make('password'),
                    'privacy'           => 1,
                    'role_id'           => 3,
                    'email_verified_at' => $now,
                    // 'details_id'        => $company->id,
                    // 'details_type'      => 'App\Models\Company',
                ];
    
                $user = User::create($user_data);
    
                $company->user()->save($user);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        }
    }
}
