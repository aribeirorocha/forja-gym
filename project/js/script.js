document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll(".main-header nav ul li a");
    
    // Descobre a página atual e o hash (ex: index.php#aulas)
    let currentPage = window.location.pathname.split("/").pop();
    if (currentPage === "") currentPage = "index.php"; // Fallback
    let currentHash = window.location.hash;
    
    links.forEach(link => {
        let linkHref = link.getAttribute("href");
                
        // Condição 1: O link é exatamente igual à página + hash (ex: index.php#aulas)
        if (linkHref === (currentPage + currentHash)) {
            link.classList.add("active");
        }

        // Condição 2: Estamos numa página base sem hash (ex: index.php)
        else if (!currentHash && linkHref === currentPage) {
            link.classList.add("active");
        }
                
        // Muda o destaque instantaneamente ao clicar
        link.addEventListener("click", function() {
            links.forEach(l => l.classList.remove("active"));
            this.classList.add("active");
        });
    });
});