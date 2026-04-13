document.addEventListener('DOMContentLoaded', function() {
    const customSelects = document.querySelectorAll('.custom-select');

    customSelects.forEach(function(customSelect) {
        const selectSelected = customSelect.querySelector('.select-selected');
        const selectItems = customSelect.querySelector('.select-items');
        const options = selectItems.querySelectorAll('.dataType');

        selectSelected.addEventListener('click', function(e) {
            e.stopPropagation();
            selectItems.classList.toggle('select-hide');
        });

        options.forEach(function(option) {
            option.addEventListener('click', function(e) {
                e.stopPropagation();

                selectSelected.textContent = this.textContent;

                options.forEach(opt => opt.classList.remove('same-as-selected'));

                this.classList.add('same-as-selected');

                selectItems.classList.add('select-hide');

                const value = this.getAttribute('data-value');
                console.log('Selected value:', value);
            });
        });

        document.addEventListener('click', function() {
            selectItems.classList.add('select-hide');
        });

        if (options[0]) {
            options[0].classList.add('same-as-selected');
        }
    });
});
