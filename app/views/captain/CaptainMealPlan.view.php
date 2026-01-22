<!-- Meal Plan Page (Weekly View) -->
      <!DOCTYPE html>
      <html lang="en">
      <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
      </head>
      <body>
        
       <div id="meal-plan-page" class="page-content">
            <div class="page-header">
                <h1>Weekly Meal Plan</h1>
                <p>Your personalized nutrition plan for optimal performance</p>
            </div>

            <div class="meal-plan-header">
                <div class="meal-plan-tabs">
                    <button class="meal-plan-tab active" data-meal="breakfast">Breakfast</button>
                    <button class="meal-plan-tab" data-meal="lunch">Lunch</button>
                    <button class="meal-plan-tab" data-meal="dinner">Dinner</button>
                </div>
            </div>

            <!-- calendar removed per UX request (kept quick-access only) -->

            <div class="meals-grid" id="meals-grid">
                <div class="meal-card" data-meal="breakfast">
                    <h3>🌅 Breakfast</h3>
                    <ul>
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable (minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>

                <div class="meal-card" data-meal="lunch">
                    <h3>🍽️ Lunch</h3>
                    <ul>
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable (minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>

                <div class="meal-card" data-meal="dinner">
                    <h3>🌙 Dinner</h3>
                    <ul>
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable (minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Simple Meal Plan Page -->
        <div id="simple-meal-page" class="page-content">
            <div class="page-header">
                <h1>Daily Meal Plan</h1>
                <p>Your complete nutrition guide for the day</p>
            </div>

            <div class="meal-tabs-container">
                <div class="meal-tabs">
                    <button class="meal-tab active">🌅 Breakfast</button>
                    <button class="meal-tab">🍽️ Lunch</button>
                    <button class="meal-tab">🌙 Dinner</button>
                </div>
            </div>

            <div class="meal-categories-grid">
                <div class="meal-category-card">
                    <div class="meal-category-header">
                        <div class="meal-icon">🌅</div>
                        <h2>Breakfast</h2>
                    </div>
                    <ul class="meal-category-list">
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable(minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>

                <div class="meal-category-card">
                    <div class="meal-category-header">
                        <div class="meal-icon">🍽️</div>
                        <h2>Lunch</h2>
                    </div>
                    <ul class="meal-category-list">
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable(minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>

                <div class="meal-category-card">
                    <div class="meal-category-header">
                        <div class="meal-icon">🌙</div>
                        <h2>Dinner</h2>
                    </div>
                    <ul class="meal-category-list">
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable(minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>
            </div>
        </div>
 </body>
      </html>