document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('newsModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalClose = document.getElementById('modalClose');
    const readMoreButtons = document.querySelectorAll('.read-more-btn');

    // Function to open modal
    function openModal(newsData) {
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        const modalDate = document.getElementById('modalDate');
        const modalText = document.getElementById('modalText');

        // Set modal content
        modalTitle.textContent = newsData.title;
        modalDate.textContent = newsData.date;
        modalText.textContent = newsData.content;

        // Set image or placeholder
        if (newsData.image) {
            const img = document.createElement('img');
            img.src = newsData.image;
            img.alt = newsData.title;
            modalImage.innerHTML = '';
            modalImage.appendChild(img);
            modalImage.classList.remove('no-image');
        } else {
            modalImage.innerHTML = '📰';
            modalImage.classList.add('no-image');
        }

        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    // Function to close modal
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    }

    // Add click event to all "Read More" buttons
    readMoreButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const newsData = {
                id: this.getAttribute('data-id'),
                title: this.getAttribute('data-title'),
                date: this.getAttribute('data-date'),
                image: this.getAttribute('data-image') ? 
                       window.location.origin + '/UOC_Football/public/uploads/news_images/' + this.getAttribute('data-image') : '',
                content: this.getAttribute('data-content')
            };

            openModal(newsData);
        });
    });

    // Close modal when clicking close button
    modalClose.addEventListener('click', function(e) {
        e.stopPropagation();
        closeModal();
    });

    // Close modal when clicking overlay
    modalOverlay.addEventListener('click', function() {
        closeModal();
    });

    // Close modal when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });

    // Prevent modal content from closing when clicked
    const modalContent = document.querySelector('.modal-content');
    modalContent.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
