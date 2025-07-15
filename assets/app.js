import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

console.log('This log comes from assets/app.js - welcome to AssetMapper!');

// Carrousel JS vanilla pour la page d'accueil
window.addEventListener('DOMContentLoaded', function () {
    const carousel = document.querySelector('#default-carousel');
    if (!carousel) return;
    const items = carousel.querySelectorAll('[data-carousel-item]');
    const indicators = carousel.querySelectorAll('.carousel-indicator');
    let current = 0;

    function show(index) {
        items.forEach((item, i) => {
            item.style.display = (i === index) ? 'block' : 'none';
            item.classList.toggle('active', i === index);
        });
        indicators.forEach((btn, i) => {
            btn.style.opacity = (i === index) ? '1' : '0.5';
        });
        current = index;
    }

    indicators.forEach((btn, i) => {
        btn.addEventListener('click', () => show(i));
    });

    const prev = carousel.querySelector('[data-carousel-prev]');
    const next = carousel.querySelector('[data-carousel-next]');
    if (prev) prev.addEventListener('click', () => show((current - 1 + items.length) % items.length));
    if (next) next.addEventListener('click', () => show((current + 1) % items.length));

    show(0);
});

// Carrousel d'actualités pour la page d'accueil
window.addEventListener('DOMContentLoaded', function () {
    const newsCarousel = document.querySelector('#news-carousel');
    if (!newsCarousel) return;
    const slides = newsCarousel.querySelectorAll('.news-slide');
    const indicators = newsCarousel.querySelectorAll('.news-indicator');
    const prev = newsCarousel.querySelector('.news-prev');
    const next = newsCarousel.querySelector('.news-next');
    let current = 0;

    function show(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        indicators.forEach((btn, i) => {
            btn.classList.toggle('active', i === index);
        });
        current = index;
    }

    indicators.forEach((btn, i) => {
        btn.addEventListener('click', () => show(i));
    });
    if (prev) prev.addEventListener('click', () => show((current - 1 + slides.length) % slides.length));
    if (next) next.addEventListener('click', () => show((current + 1) % slides.length));
    show(0);
});

// Carrousel type DaisyUI pour la page d'accueil
window.addEventListener('DOMContentLoaded', function () {
    const carousel = document.querySelector('.carousel');
    if (!carousel) return;
    const slides = carousel.querySelectorAll('.carousel-item');
    const prevBtns = carousel.querySelectorAll('[data-carousel-prev]');
    const nextBtns = carousel.querySelectorAll('[data-carousel-next]');
    let current = 0;

    function show(index) {
        slides.forEach((slide, i) => {
            slide.style.display = (i === index) ? 'block' : 'none';
        });
        current = index;
    }

    prevBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            show((current - 1 + slides.length) % slides.length);
        });
    });
    nextBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            show((current + 1) % slides.length);
        });
    });
    show(0);
});
