<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Meal;

class CategoryMealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample categories
        $categories = [
            [
                'name_en' => 'Breakfast',
                'name_ar' => 'الإفطار',
                'status' => '1',
                'grouped' => '0',
                'order' => 1,
                'is_ramadan' => '1',
            ],
            [
                'name_en' => 'Lunch',
                'name_ar' => 'الغداء',
                'status' => '1',
                'grouped' => '0',
                'order' => 2,
                'is_ramadan' => '0',
            ],
            [
                'name_en' => 'Dinner',
                'name_ar' => 'العشاء',
                'status' => '1',
                'grouped' => '0',
                'order' => 3,
                'is_ramadan' => '0',
            ]
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create($categoryData);

            // Sample meals for each category
            $meals = [
                [
                    'name_en' => 'Cheese Omelette',
                    'name_ar' => 'عجة بالجبن',
                    'description_en' => 'A delicious cheese omelette.',
                    'description_ar' => 'عجة شهية بالجبن.',
                    'calories' => '250',
                    'price' => '10.99',
                    'image' => 'cheese_omelette.jpg',
                    'category_id' => $category->id,
                    'status' => '1',
                    'order' => 1,
                    'is_ramadan' => $category->is_ramadan
                ],
                [
                    'name_en' => 'Grilled Chicken',
                    'name_ar' => 'دجاج مشوي',
                    'description_en' => 'A healthy grilled chicken meal.',
                    'description_ar' => 'وجبة دجاج مشوي صحية.',
                    'calories' => '450',
                    'price' => '15.99',
                    'image' => 'grilled_chicken.jpg',
                    'category_id' => $category->id,
                    'status' => '1',
                    'order' => 2,
                    'is_ramadan' => $category->is_ramadan
                ],
            ];

            foreach ($meals as $mealData) {
                Meal::create($mealData);
            }
        }
    }
}
