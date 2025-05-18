document.addEventListener("DOMContentLoaded", function () {
    fetch('/src/server.php')
        .then(response => response.json())
        .then(data => {
            const filmsContainer = document.getElementById('films-container');
            data.forEach(film => {
                const filmElement = document.createElement('div');
                filmElement.classList.add('film');
                filmElement.innerHTML = `
                    <h3>${film.title}</h3>
                    <p>Type: ${film.type}</p>
                    <p>Année: ${film.release_year}</p>
                    <p>Plateforme: ${film.platform}</p>
                    <button>Ajouter aux favoris</button>
                `;
                filmsContainer.appendChild(filmElement);
            });
        })
        .catch(error => console.error('Erreur:', error));
});
