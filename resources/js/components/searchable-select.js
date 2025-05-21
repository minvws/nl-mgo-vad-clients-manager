document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('.searchable-select');

    selects.forEach(select => {
        const input = select.querySelector('.searchable-select-input');
        const hiddenInput = select.querySelector('input[type="hidden"]');
        const dropdown = select.querySelector('.searchable-select-dropdown');
        const searchInput = select.querySelector('.searchable-select-search-input');
        const chevron = select.querySelector('.searchable-select-chevron');
        const options = select.querySelectorAll('.searchable-select-option');
        let selectedOption = null;

        // Ensure dropdown is hidden on page load
        dropdown.style.display = 'none';
        chevron.classList.remove('open');

        function toggleDropdown() {
            const isOpen = dropdown.style.display !== 'none';
            dropdown.style.display = isOpen ? 'none' : 'block';
            chevron.classList.toggle('open', !isOpen);
            if (!isOpen) {
                searchInput.value = '';
                filterOptions('');
                // Focus the search input when opening
                searchInput.focus();
            }
        }

        function selectOption(option) {
            const value = option.dataset.value;
            const label = option.dataset.label;
            hiddenInput.value = value;
            hiddenInput.dataset.label = label;
            input.value = label;
            selectedOption = option;
            options.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
            toggleDropdown();
        }

        function filterOptions(searchTerm) {
            searchTerm = searchTerm.toLowerCase();
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        }

        function handleKeyDown(e) {
            // Early return if the key is not relevant
            if (!['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) {
                return;
            }

            if (!dropdown.style.display || dropdown.style.display === 'none') {
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter') {
                    e.preventDefault();
                    toggleDropdown();
                    return;
                }
            }

            const visibleOptions = Array.from(options).filter(opt => opt.style.display !== 'none');
            if (visibleOptions.length === 0) return;

            const currentIndex = visibleOptions.findIndex(opt => opt.classList.contains('selected'));

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    if (currentIndex < visibleOptions.length - 1) {
                        visibleOptions.forEach(opt => opt.classList.remove('selected'));
                        visibleOptions[currentIndex + 1].classList.add('selected');
                        selectedOption = visibleOptions[currentIndex + 1];
                        selectedOption.focus();
                    }
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    if (currentIndex > 0) {
                        visibleOptions.forEach(opt => opt.classList.remove('selected'));
                        visibleOptions[currentIndex - 1].classList.add('selected');
                        selectedOption = visibleOptions[currentIndex - 1];
                        selectedOption.focus();
                    } else {
                        // If we're at the top, move focus to search input
                        searchInput.focus();
                    }
                    break;
                case 'Enter':
                    e.preventDefault();
                    const selected = visibleOptions.find(opt => opt.classList.contains('selected'));
                    if (selected) {
                        selectOption(selected);
                    }
                    break;
                case 'Escape':
                    toggleDropdown();
                    break;
            }
        }

        // Event Listeners
        input.addEventListener('click', toggleDropdown);
        input.addEventListener('keydown', handleKeyDown);
        searchInput.addEventListener('input', (e) => filterOptions(e.target.value));
        searchInput.addEventListener('keydown', handleKeyDown);

        // Use event delegation for options
        dropdown.addEventListener('click', (e) => {
            if (e.target.classList.contains('searchable-select-option')) {
                selectOption(e.target);
            }
        });

        dropdown.addEventListener('keydown', (e) => {
            if (e.target.classList.contains('searchable-select-option')) {
                handleKeyDown(e);
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!select.contains(e.target)) {
                dropdown.style.display = 'none';
                chevron.classList.remove('open');
            }
        });
    });
});