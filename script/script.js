// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {

    getAnimalInfos();

});


async function getAnimalInfos() {
    // Erstelle eine Karte und setze die Standardansicht (Mittelpunkt auf Europa)
    var map = L.map('map').setView([20, 0], 2);

    // OpenStreetMap-Kacheln laden
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // AJAX-Anfrage an das PHP-Skript, um die Koordinaten zu laden
    await fetch('script/getAnimalsInfo.php')
        .then(response => response.json())
        .then(data => {
            console.log(data);

            // HTML-Container für die Tierinformationen
            let infoContainer = document.getElementById('animal-info');
            let locationContainer = document.getElementById('location');

            data.forEach(animal => {
                // Füge für jede Location einen Marker hinzu
                var marker = L.marker([animal.latitude, animal.longitude]).addTo(map);

                // Binde ein Popup an den Marker mit der Beschreibung
                // marker.bindPopup(`<b>${location.location}</b><br>${location.description}`);

                // Füge ein 'click'-Event hinzu, um die Informationen unter der Karte anzuzeigen
                marker.on('click', function() {
                    // Leere das infoContainer-Element
                    infoContainer.innerHTML = '';

                    // Informationen der Location anzeigen
                    let animalInfo = document.createElement('div');
                    
                    // Titel der Location
                    locationContainer.innerHTML = '';
                    let locationTitle = document.createElement('h2');
                    locationTitle.innerHTML = animal.location;

                    locationContainer.appendChild(locationTitle);


                    // Tiername - lastSeen
                    let nameLastseenDiv = document.createElement('div');
                    nameLastseenDiv.classList.add('nameLastseenDiv');
                    
                    let animalName = document.createElement('p');
                    animalName.classList.add('animalName');
                    animalName.innerText = `${animal.name}`;
                    
                    let lastseen = document.createElement('p');
                    lastseen.classList.add('lastseen');
                    lastseen.innerText = `Last seen: ${animal.last_record}`;
                    
                    nameLastseenDiv.appendChild(animalName);
                    nameLastseenDiv.appendChild(lastseen);
                    
                    infoContainer.appendChild(nameLastseenDiv);



                    // image - description
                    let imgDescDiv = document.createElement('div');
                    imgDescDiv.classList.add('imgDescDiv');
                    
                    let animalImage = document.createElement('img');
                    animalImage.classList.add('animalImage');
                    animalImage.src = `${animal.image}`;
                    animalImage.alt = 'animal image';
                    animalImage.width = '200';
                    
                    let animalDesc = document.createElement('p');
                    animalDesc.classList.add('animalDesc');
                    animalDesc.innerText = `${animal.description}`;
                    
                    imgDescDiv.appendChild(animalImage);
                    imgDescDiv.appendChild(animalDesc);
                    
                    infoContainer.appendChild(imgDescDiv);
                    

                    
                    
                    infoContainer.appendChild(animalInfo);
                });

            });
        })
        .catch(error => {
            console.error('Fehler beim Abrufen der Daten:', error);
        });
}