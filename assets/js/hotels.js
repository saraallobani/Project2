

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('hotelSearch');
    const starFilter = document.getElementById('starFilter');
    const hotelCards = document.querySelectorAll('.hotel-card:not(#noResultsMessage)');
    const noResults = document.getElementById('noResultsMessage');
    if (!searchInput || !starFilter) return;

    function normalizeText(text) {
        if (!text) return '';
        return text.toString().toLowerCase()
            .trim()
            .replace(/[أإآ]/g, 'ا')
            .replace(/ة/g, 'ه')
            .replace(/ى/g, 'ي')
            .replace(/[\u064B-\u065F]/g, ''); 
    }
    

    function applyFilters() {
        const searchQuery = normalizeText(searchInput.value);
        const selectedRating = starFilter.value ? parseInt(starFilter.value) : 0;
        let matchedCount = 0;

        hotelCards.forEach(card => {
            const name = normalizeText(card.getAttribute('data-name') || '');
            const stars = parseInt(card.getAttribute('data-stars')) || 0;
           
            const matchesSearch = !searchQuery || name.includes(searchQuery);
           
            const matchesRating = !selectedRating || (stars === selectedRating);

            if (matchesSearch && matchesRating) {
                card.style.display = 'block';
                card.classList.remove('animate__fadeOut');
                card.classList.add('animate__fadeInUp');
                matchedCount++;
            } else {
                card.style.display = 'none';
            }
        });

       
        if (matchedCount === 0) {
            if (noResults) {
                noResults.style.display = 'block';
                noResults.classList.add('animate__animated', 'animate__fadeIn');
            }
        } else {
            if (noResults) {
                noResults.style.display = 'none';
            }
        }
    }

    
    searchInput.addEventListener('input', applyFilters);
   
    starFilter.addEventListener('change', applyFilters);
});
