

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('restaurantSearch');
    const cuisineFilter = document.getElementById('cuisineFilter');
    const starFilter = document.getElementById('starFilter');
    const restaurantCards = document.querySelectorAll('.restaurant-card');
    const noResults = document.getElementById('noResultsMessage');
    if (!searchInput || !cuisineFilter || !starFilter) return;

    
    function normalizeText(text) {
        if (!text) return '';
        return text.toString().toLowerCase()
            .trim()
            .replace(/[أإآ]/g, 'ا')
            .replace(/ة/g, 'ه')
            .replace(/ى/g, 'ي')
            .replace(/[\u064B-\u065F]/g, ''); 
    }

    
    function matchesCuisine(cardCuisine, filterVal) {
        if (!filterVal) return true;
        const normalizedCard = normalizeText(cardCuisine);
        const normalizedFilter = normalizeText(filterVal);

       
        if (normalizedFilter === 'عربي') {
            return normalizedCard.includes('عربي') ||
                   normalizedCard.includes('شرقي') ||
                   normalizedCard.includes('لبناني') ||
                   normalizedCard.includes('اردني') ||
                   normalizedCard.includes('بدوي') ||
                   normalizedCard.includes('شامي') ||
                   normalizedCard.includes('شعبي');
        }
        if (normalizedFilter === 'بحري') {
            return normalizedCard.includes('بحري') || normalizedCard.includes('بحريه');
        }
        if (normalizedFilter === 'بدوي') {
            return normalizedCard.includes('بدوي');
        }
        if (normalizedFilter === 'بوفيه') {
            return normalizedCard.includes('بوفيه');
        }
        if (normalizedFilter === 'شاورما') {
            return normalizedCard.includes('شاورما') || normalizedCard.includes('سريعه') || normalizedCard.includes('سريع');
        }

        return normalizedCard.includes(normalizedFilter);
    }

    function applyFilters() {
        const searchQuery = normalizeText(searchInput.value);
        const selectedCuisine = cuisineFilter.value;
        const selectedRating = starFilter.value ? parseInt(starFilter.value) : 0;
        let matchedCount = 0;

        restaurantCards.forEach(card => {
            const name = normalizeText(card.getAttribute('data-name') || '');
            const stars = parseInt(card.getAttribute('data-stars')) || 0;
            const cuisine = card.getAttribute('data-cuisine') || '';

            const matchesSearch = !searchQuery || name.includes(searchQuery);
            const matchesRating = !selectedRating || (stars === selectedRating);
            const matchesCuisineFilter = matchesCuisine(cuisine, selectedCuisine);

            if (matchesSearch && matchesRating && matchesCuisineFilter) {
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
   
    cuisineFilter.addEventListener('change', applyFilters);
   
    starFilter.addEventListener('change', applyFilters);
});
