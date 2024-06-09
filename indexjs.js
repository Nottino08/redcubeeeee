document.addEventListener('DOMContentLoaded', () => {
    // Animazione di caricamento logo per homepage
    const logoContainer = document.querySelector('.logo-container');
    logoContainer.style.animation = 'riseUp 1s ease-out forwards';

    // Animazione testo
    const animatedTexts = document.querySelectorAll('.animated-text');
    animatedTexts.forEach(text => {
        text.style.animation = 'fadeIn 1s forwards';
    });
});
