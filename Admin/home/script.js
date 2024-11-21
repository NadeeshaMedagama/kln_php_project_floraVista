window.onclick = function(event) {
    if (!event.target.matches('.dropdown-btn')) {
        let dropdowns = document.getElementsByClassName("dropdown-content");
        for (let i = 0; i < dropdowns.length; i++) {
            let openDropdown = dropdowns[i];
            if (openDropdown.style.display === "block") {
                openDropdown.style.display = "none";
            }
        }
    }
}

const leftArrow = document.querySelector('.left-arrow');
const rightArrow = document.querySelector('.right-arrow');
const logosContainer = document.querySelector('.logos');

let scrollAmount = 0;

leftArrow.addEventListener('click', () => {
    scrollAmount -= 200; // Adjust this value to control how much the logos scroll
    logosContainer.style.transform = `translateX(${scrollAmount}px)`;
});

rightArrow.addEventListener('click', () => {
    scrollAmount += 200; // Adjust this value to control how much the logos scroll
    logosContainer.style.transform = `translateX(${scrollAmount}px)`;
});
