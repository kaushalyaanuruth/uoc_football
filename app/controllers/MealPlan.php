<?php

class MealPlan extends Controller
{

    public function index()
    {
        $data = [
            'selected_category' => 'Breakfast',
            'meal_data' => [
                'Breakfast' => [
                    'Monday' => ['items' => ['Basmati Rice', 'Scrambled Eggs', 'Fresh Fruits', 'Green Tea'], 'calories' => 480],
                    'Tuesday' => ['items' => ['Oatmeal', 'Boiled Eggs', 'Banana', 'Milk'], 'calories' => 520],
                    'Wednesday' => ['items' => ['Whole Wheat Toast', 'Omelet', 'Orange Juice', 'Yogurt'], 'calories' => 495],
                    'Thursday' => ['items' => ['Quinoa Bowl', 'Poached Eggs', 'Avocado', 'Herbal Tea'], 'calories' => 510],
                    'Friday' => ['items' => ['Pancakes', 'Honey', 'Berries', 'Coffee'], 'calories' => 475],
                    'Saturday' => ['items' => ['Smoothie Bowl', 'Granola', 'Nuts', 'Protein Shake'], 'calories' => 530]
                ],
                'Lunch' => [
                    'Monday' => ['items' => ['Red Rice', 'Grilled Chicken', 'Steamed Veggies', 'Lentil Soup'], 'calories' => 750],
                    'Tuesday' => ['items' => ['Pasta Primavera', 'Chicken Breast', 'Garden Salad', 'Fruit Medley'], 'calories' => 680],
                    'Wednesday' => ['items' => ['Brown Rice', 'Fish Curry', 'Mixed Greens', 'Dahl'], 'calories' => 720],
                    'Thursday' => ['items' => ['Steak Salad', 'Baked Potato', 'Grilled Corn', 'Yogurt'], 'calories' => 810],
                    'Friday' => ['items' => ['Club Sandwich', 'Sweet Potato Fries', 'Cole Slaw', 'Fresh Apple'], 'calories' => 650],
                    'Saturday' => ['items' => ['Tuna Wrap', 'Chickpea Salad', 'Quinoa', 'Greek Yogurt'], 'calories' => 690]
                ],
                'Dinner' => [
                    'Monday' => ['items' => ['Grilled Salmon', 'Asparagus', 'Wild Rice', 'Herbal Tea'], 'calories' => 620],
                    'Tuesday' => ['items' => ['Chicken Stir-fry', 'Broccoli', 'Brown Rice', 'Dark Chocolate'], 'calories' => 580],
                    'Wednesday' => ['items' => ['Lentil Stew', 'Spinach Salad', 'Whole Grain Roll', 'Almonds'], 'calories' => 540],
                    'Thursday' => ['items' => ['Turkey Burger', 'Zucchini Noodles', 'Side Salad', 'Warm Milk'], 'calories' => 600],
                    'Friday' => ['items' => ['Taco Bowl', 'Avocado', 'Salsa', 'Pear'], 'calories' => 640],
                    'Saturday' => ['items' => ['Lean Steak', 'Mashed Sweet Potato', 'Green Beans', 'Cottage Cheese'], 'calories' => 670]
                ]
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
