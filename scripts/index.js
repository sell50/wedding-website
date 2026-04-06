document.addEventListener("DOMContentLoaded", function() {
    // Navigation Scroll Into View
    const navLinks = document.querySelectorAll(".site-nav a");
    navLinks.forEach(link => {
        link.addEventListener("click", function(event) {
            event.preventDefault();
            console.log("Navigating to:", this.getAttribute("href"));
            const targetId = this.getAttribute("href").replace(".html", "");
            const targetSection = document.querySelector(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: "smooth" });
            }
        });
    });
});