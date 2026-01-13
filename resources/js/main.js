// Navigation scroll effect
(function() {
    const navigation = document.getElementById('navigation');
    
    if (!navigation) return;
    
    function handleScroll() {
        if (window.scrollY > 20) {
            navigation.classList.add('bg-background/80', 'backdrop-blur-lg', 'border-b', 'border-border');
            navigation.classList.remove('bg-transparent');
        } else {
            navigation.classList.remove('bg-background/80', 'backdrop-blur-lg', 'border-b', 'border-border');
            navigation.classList.add('bg-transparent');
        }
    }
    
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Initial check
})();

// Smooth scroll for anchor links
(function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
})();

