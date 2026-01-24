<?php

class MealPlan extends Controller
{

    public function index()
    {
        $data = [
            'selected_category' => 'Breakfast',
            'daily_plans' => [
                'Monday' => ['items' => ['Basmati Rice', 'Scrambled Eggs', 'Fresh Fruits', 'Green Tea'], 'calories' => 480],
                'Tuesday' => ['items' => ['Oatmeal', 'Boiled Eggs', 'Banana', 'Milk'], 'calories' => 520],
                'Wednesday' => ['items' => ['Whole Wheat Toast', 'Omelet', 'Orange Juice', 'Yogurt'], 'calories' => 495],
                'Thursday' => ['items' => ['Quinoa Bowl', 'Poached Eggs', 'Avocado', 'Herbal Tea'], 'calories' => 510],
                'Friday' => ['items' => ['Pancakes', 'Honey', 'Berries', 'Coffee'], 'calories' => 475],
                'Saturday' => ['items' => ['Smoothie Bowl', 'Granola', 'Nuts', 'Protein Shake'], 'calories' => 530]
            ],
            'nutrition' => [
                'Calories' => '2,850',
                'Protein' => '185g',
                'Carbs' => '320g',
                'Fat' => '95g'
            ],
            'updates' => [
                [
                    'type' => 'update',
                    'title' => 'Meal Plan Update',
                    'content' => 'Added extra protein for Thursday due to intensive training session.',
                    'time' => '2 hours ago'
                ],
                [
                    'type' => 'goal',
                    'title' => 'Nutrition Goal',
                    'content' => 'Great job maintaining your meal schedule this week!',
                    'time' => '1 day ago'
                ]
            ]
        ];

        $this->view('mealPlan', $data);
    }
}
