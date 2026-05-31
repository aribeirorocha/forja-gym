
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
    link.addEventListener("click", function () {
        links.forEach(l => l.classList.remove("active"));
        this.classList.add("active");
    });
});

// equipment.php

const filterLocation = document.getElementById('filter-location');
const filterMuscle = document.getElementById('filter-muscle');
const filterStatus = document.getElementById('filter-status');
const allCards = document.querySelectorAll('.equipment-card');

function applyFilters() {
    const valLocation = filterLocation ? filterLocation.value : 'all';
    const valMuscle = filterMuscle ? filterMuscle.value : 'all';
    const valStatus = filterStatus ? filterStatus.value : 'all';

    allCards.forEach(card => {
        const matchLocation = (valLocation === 'all' || card.getAttribute('data-location') === valLocation);
        const matchMuscle = (valMuscle === 'all' || card.getAttribute('data-muscle') === valMuscle);
        const matchStatus = (valStatus === 'all' || card.getAttribute('data-status') === valStatus);

        card.style.display = (matchLocation && matchMuscle && matchStatus) ? 'flex' : 'none';
    });
}

if (filterLocation) filterLocation.addEventListener('change', applyFilters);
if (filterMuscle) filterMuscle.addEventListener('change', applyFilters);
if (filterStatus) filterStatus.addEventListener('change', applyFilters);


// trainers.php

const filterGender = document.getElementById('filter-gender');
const sortTrainers = document.getElementById('sort-trainers');
const grid = document.getElementById('trainers-grid-container');

// Exclui o card "+" da ordenação/filtragem

let cards = grid ? Array.from(grid.querySelectorAll('.trainer-card')) : null;

function updateGrid() {
    if (!cards) return;

    const valGender = filterGender.value;
    const valSort = sortTrainers.value;

    // 1. FILTRAR POR GÉNERO
    cards.forEach(card => {
        const cardGender = card.getAttribute('data-gender');
        card.style.display = (valGender === 'all' || cardGender === valGender) ? 'block' : 'none';
    });

    // 2. ORDENAR
    if (valSort !== 'default') {
        cards.sort((a, b) => {
            const ageA = parseInt(a.getAttribute('data-age'));
            const ageB = parseInt(b.getAttribute('data-age'));
            const yearA = parseInt(a.getAttribute('data-year'));
            const yearB = parseInt(b.getAttribute('data-year'));
            switch (valSort) {
                case 'age-asc': return ageA - ageB;
                case 'age-desc': return ageB - ageA;
                case 'time-desc': return yearA - yearB;
                case 'time-asc': return yearB - yearA;
                default: return 0;
            }
        });
    }

    // 3. APLICAR VISUALMENTE (o card "+" fica sempre no fim por estar fora do array)
    cards.forEach(card => grid.insertBefore(card, grid.querySelector('#trigger-add-trainer')));
}

if (filterGender) filterGender.addEventListener('change', updateGrid);
if (sortTrainers) sortTrainers.addEventListener('change', updateGrid);
// Live update for Class Enrollment
document.addEventListener("DOMContentLoaded", function () {
    const enrollForm = document.getElementById('enroll-form');
    if (enrollForm) {
        enrollForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            
            const submitBtn = enrollForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'A processar...';
            submitBtn.disabled = true;

            const formData = new FormData(enrollForm);
            formData.append('ajax', '1');

            try {
                const response = await fetch(enrollForm.action, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    if (data.action === 'enroll') {
                        submitBtn.textContent = 'Desinscrever-se';
                    } else {
                        submitBtn.textContent = 'Inscrever-se na Aula';
                    }
                    
                    // Update capacity counter if it exists on page
                    const capacityElem = document.getElementById('enrollment-count');
                    if (capacityElem && data.new_count !== undefined) {
                        capacityElem.textContent = data.new_count;
                    }
                } else {
                    alert(data.message || 'Erro ao processar inscrição.');
                    submitBtn.textContent = originalText;
                }
            } catch (err) {
                console.error(err);
                alert('Ocorreu um erro de rede.');
                submitBtn.textContent = originalText;
            } finally {
                submitBtn.disabled = false;
            }
        });
    }
});
