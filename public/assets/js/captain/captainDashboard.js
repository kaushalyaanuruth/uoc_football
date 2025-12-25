 // Navigation handling
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update active nav link
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                // Hide all pages
                document.querySelectorAll('.page-content').forEach(page => page.classList.remove('active'));
                
                // Show selected page
                const page = this.dataset.page;
                document.getElementById(`${page}-page`).classList.add('active');

                // if navigating to meal plan, ensure correct meal filter is applied
                if (page === 'meal-plan') {
                    applyMealFilterFromActiveTab();
                }
            });
        });

        // Quick links from home
        document.getElementById('meal-plan-preview').addEventListener('click', function() {
            document.querySelectorAll('.page-content').forEach(page => page.classList.remove('active'));
            document.getElementById('meal-plan-page').classList.add('active');

            // clear nav active state (quick actions are outside main nav)
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            // apply default/active meal filter
            applyMealFilterFromActiveTab();
        });

        // Performance quick-link opens performance analysis view
        const perfPreview = document.getElementById('performance-preview');
        if (perfPreview) {
            perfPreview.addEventListener('click', function() {
                document.querySelectorAll('.page-content').forEach(page => page.classList.remove('active'));
                document.getElementById('analyze-page').classList.add('active');
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            });
        }

        // Meal plan tabs: filter meal cards (breakfast/lunch/dinner) in the same page
        document.querySelectorAll('.meal-plan-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.meal-plan-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const meal = this.dataset.meal; // breakfast | lunch | dinner
                document.querySelectorAll('#meals-grid .meal-card').forEach(card => {
                    if (card.dataset.meal === meal) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Helper to apply current active meal tab filter
        function applyMealFilterFromActiveTab() {
            const activeTab = document.querySelector('.meal-plan-tab.active');
            const meal = activeTab ? activeTab.dataset.meal : 'breakfast';
            document.querySelectorAll('#meals-grid .meal-card').forEach(card => {
                card.style.display = (card.dataset.meal === meal) ? '' : 'none';
            });
        }

        // Ensure filter is applied on initial load (breakfast is the default active tab)
        applyMealFilterFromActiveTab();

        // Schedule filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                document.querySelectorAll('.schedule-item').forEach(item => {
                    if (filter === 'all' || item.dataset.type === filter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Simple meal plan tabs
        document.querySelectorAll('.meal-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.meal-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Calendar removed: no calendar event handlers necessary

        // Logout button handler
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function() {
                const ok = confirm('Are you sure you want to logout?');
                if (!ok) return;

                // Clear a common auth key if present and any other session data
                try {
                    localStorage.removeItem('authToken');
                    // add other cleanup here if needed
                } catch (e) {
                    // ignore
                }

                // Redirect to landing page
                window.location.href = '<?php echo ROOT; ?>/LandingPage';
            });
        }