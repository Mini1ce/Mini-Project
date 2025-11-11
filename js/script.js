document.addEventListener('DOMContentLoaded', () => {
    const track = document.querySelector('.slider-track');
    const cards = Array.from(track.children);
    const nextButton = document.getElementById('next-btn');
    const prevButton = document.getElementById('prev-btn');

    const cardsVisible = 4;
    const cardWidth = cards[0].getBoundingClientRect().width + 30;

    let currentIndex = 0;

    const moveToSlide = (targetIndex) => {
        track.style.transform = 'translateX(-' + cardWidth * targetIndex + 'px)';
        currentIndex = targetIndex;
    }

    nextButton.addEventListener('click', () => {
        if (currentIndex < cards.length - cardsVisible) {
            moveToSlide(currentIndex + 1);
        }
    });

    prevButton.addEventListener('click', () => {
        if (currentIndex > 0) {
            moveToSlide(currentIndex - 1);
        }
    });
});



