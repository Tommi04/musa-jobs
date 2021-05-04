<?php

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanyCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $categories_array = [];

        $categories = fopen(storage_path('app/categories.csv'), "r");
        $header = true;
        while ($csvLine = fgetcsv($categories, 1000, ",")) {
            if($header){
                $header = false;
            }else{
                $categories_array[] = [
                    'label' => $csvLine[0],
                    'code'  => Str::slug($csvLine[0]),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }
        Category::insert($categories_array);

        /* preso da csv
        for ($i=0; $i < 15; $i++) { 
            $categories[] = [
                'label' => 'Category' . $i,
                'code'  => 'caategory-' . $i,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        
        Category::insert($categories);
        */
    }
}
