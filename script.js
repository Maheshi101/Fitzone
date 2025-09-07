// Function to toggle the mobile navigation menu's active class
function toggleMenu() {
  const navLinks = document.getElementById("navLinks");
  navLinks.classList.toggle("active");
}

// Event listener to close the menu when a link is clicked
document.addEventListener("DOMContentLoaded", () => {
  const navLinks = document.getElementById("navLinks");
  const links = navLinks.querySelectorAll("a"); // Selects all <a> tags inside the navLinks div

  links.forEach(link => {
    link.addEventListener("click", () => {
      if (navLinks.classList.contains("active")) {
        navLinks.classList.remove("active");
      }
    });
  });

  // Event listener to close the menu when a click happens outside
  const burger = document.querySelector(".burger");
  document.addEventListener("click", function(event) {
    const isClickInsideMenu = navLinks.contains(event.target);
    const isClickInsideBurger = burger.contains(event.target);

    if (navLinks.classList.contains("active") && !isClickInsideMenu && !isClickInsideBurger) {
      navLinks.classList.remove("active");
    }
  });
});