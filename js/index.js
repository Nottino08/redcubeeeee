function createStars(numStars) {
    const body = document.body;
    for (let i = 0; i < numStars; i++) {
        const star= 
        document.createElement('div');
        star.classList.add('star')
        star.style.top = Math.random() * 100 + 'vh';
        star.style.left = Math.random() * 100 + 'vw';
        star.style.animationDuration = Math.random() * 5 + 5 + 's';
        body.appendChild(star);
    }
}
createStars(50);

function redirectToHome() {
    window.location.href = '/pages/home.html';
}
setTimeout(redirectToHome, 5000);