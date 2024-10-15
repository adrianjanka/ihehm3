// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
    buildPage();
});


async function buildPage() {
    // Erstelle eine Karte und setze die Standardansicht (Mittelpunkt auf Europa)
    var map = L.map('map', {
        zoomControl: false
    }).setView([20, 0], 2);

    // OpenStreetMap-Kacheln laden
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // AJAX-Anfrage an das PHP-Skript, um die Koordinaten zu laden
    await fetch('script/getAnimalsInfo.php')
        .then(response => response.json())
        .then(data => {
            console.log("Tiere: ",data);

            // HTML-Container für die Tierinformationen
            // let infoContainer = document.getElementById('info');
            let locationContainer = document.getElementById('location');

            data.forEach(animal => {
                // Füge für jede Location einen Marker hinzu
                var marker = L.marker([animal.latitude, animal.longitude]).addTo(map);

                
                marker.on('mouseover', function() {
                    marker.bindTooltip(`<strong>${animal.name}</strong><br>last seen: ${animal.last_record}`).openTooltip();
                });

                // Füge ein 'click'-Event hinzu, um die Informationen unter der Karte anzuzeigen
                marker.on('click', function() {
                    // Leere das infoContainer-Element
                    info.innerHTML = '';

                    let animalInfoDiv = document.createElement('div');
                    animalInfoDiv.classList.add('animal-info');

                    info.appendChild(animalInfoDiv);


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
                    
                    let animalName = document.createElement('h3');
                    animalName.classList.add('animalName');
                    animalName.innerText = `Animal: ${animal.name}`;
                    
                    let lastseen = document.createElement('h3');
                    lastseen.classList.add('lastseen');
                    lastseen.innerText = `Last seen: ${animal.last_record}`;
                    
                    nameLastseenDiv.appendChild(animalName);
                    nameLastseenDiv.appendChild(lastseen);
                    
                    animalInfoDiv.appendChild(nameLastseenDiv);



                    // image - description
                    let imgDescDiv = document.createElement('div');
                    imgDescDiv.classList.add('imgDescDiv');
                    
                    let animalImage = document.createElement('img');
                    animalImage.classList.add('animalImage');
                    animalImage.src = `${animal.image}`;
                    animalImage.alt = 'animal image';
                    
                    let animalDesc = document.createElement('p');
                    animalDesc.classList.add('animalDesc');
                    animalDesc.innerText = `${animal.description}`;

                    let vacationButton = document.createElement('button');
                    vacationButton.classList.add('vacationButton');
                    vacationButton.innerText = `Willst du in ${animal.location} Ferien machen? Dann schau dir hier das Wetter an.`;
                    vacationButton.addEventListener('click', function() {
                        weatherTitle.scrollIntoView({behavior: "smooth"});
                    });
                    animalDesc.appendChild(vacationButton);
                    
                    imgDescDiv.appendChild(animalImage);
                    imgDescDiv.appendChild(animalDesc);
                    
                    animalInfoDiv.appendChild(imgDescDiv);
                    
                    animalInfoDiv.appendChild(animalInfo);


                    // Wetterinformationen basierend auf der Location und Temperaturverlauf abrufen
                    fetch(`script/getWeatherInfo.php?location=${animal.location}`)
                    .then(response => response.json())
                    .then(weatherData => {
                        console.log("Wetter: ",weatherData);

                        // weather desc
                        let weatherCondition = weatherData[0].description;
                        let imageSrc;
                        console.log(weatherCondition);

                        if (["Patchy rain nearby", "Light rain shower", "Moderate rain", "Shower in vicinity", "Rain shower", "Light rain", "Moderate rain at times", "Rain", "Light drizzle", "Heavy rain", "Patchy light drizzle", "Patchy light rain", "Rain, mist"].includes(weatherCondition)) {
                            imageSrc = "img/rain.gif";
                        } else if (["Sunny", "Clear"].includes(weatherCondition)) {
                            imageSrc = "img/sun.gif";
                        } else if (["Cloudy", "Overcast"].includes(weatherCondition)) {
                            imageSrc = "img/clouds.gif";
                        } else if (["Mist", "Fog", "Haze", "Freezing fog"].includes(weatherCondition)) {
                            imageSrc = "img/fog.gif";
                        } else if (["Thundery outbreaks in nearby", "Thunderstorms", "Heavy thunderstorm", "Heavy thunderstorms", "Thunderstorm with rain", "Thunderstorms with rain", "Heavy thunderstorm with rain", "Heavy thunderstorms with rain", "Thunder", "Thunders"].includes(weatherCondition)) {
                            imageSrc = "img/thunder.gif";
                        } else if (["Snow", "Blizzard", "Heavy snow", "Heavy snow showers", "Heavy snowfall", "Heavy snowfall showers", "Light snow", "Light snow showers", "Light snowfall", "Light snowfall showers", "Moderate snow", "Moderate or heavy snow showers", "Moderate snowfall", "Moderate snowfall showers", "Snow showers", "Snowfall", "Snowfall showers", "Moderate snow showers", "Heavy snow showers"].includes(weatherCondition)) {
                            imageSrc = "img/snow.gif";
                        } else if (["Partly cloudy", "Partly cloudy skies"].includes(weatherCondition)) {
                            imageSrc = "img/sunandcloud.gif";
                        } else {
                            imageSrc = "img/clouds.gif";
                        }
                        console.log(imageSrc);

                        // Wetterinformationen anzeigen
                        let weatherInfo = document.createElement('div');
                        weatherInfo.innerHTML = `
                            <h2 id="weatherTitle">Weather in: ${animal.location} in the last 7 Days</h2>

                            <div class="weatherInfoDiv">
                                <div class="weatherGifDiv">
                                    <img class="weatherGifImg" src="${imageSrc}" alt="${weatherCondition}">
                                </div>
                                <div class="weatherChart">
                                    <canvas id="temperatureChart"></canvas>
                                </div>
                            </div>
                            
                        `;
                        animalInfoDiv.appendChild(weatherInfo);
                        
                        // Rufe die Funktion auf, um den Temperaturverlauf in einem Chart darzustellen
                        createTemperatureChart(weatherData);  // Hier wird die Funktion für das Diagramm aufgerufen
                        
                        // scorll to weather info
                        locationContainer.scrollIntoView({behavior: "smooth"});
                    })
                    .catch(error => {
                        console.error('Fehler beim Abrufen der Wetterinformationen:', error);
                    });




                });

            });
        })
        .catch(error => {
            console.error('Fehler beim Abrufen der Daten:', error);
        });
}



// Funktion zum Erstellen des Liniendiagramms mit Chart.js
async function createTemperatureChart(weatherData) {

    // Daten verarbeiten
    const dates = weatherData.map(entry => entry.date);  // X-Achse: Datum
    const temperatures = weatherData.map(entry => entry.temperature);  // Y-Achse: Temperatur

    // Liniendiagramm mit Chart.js erstellen
    const ctx = document.getElementById('temperatureChart').getContext('2d');
    
    const temperatureChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,  // X-Achse: Datum
            datasets: [{
                label: 'Temperatur (°C)',
                data: temperatures,  // Y-Achse: Temperatur
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        font: {
                            weight: 'bold',  // Fettgedruckte Labels
                            size: 15         // Optionale Schriftgröße
                        }
                    }
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day'  // Zeitachse auf Tage setzen
                    }
                },
                y: {
                    beginAtZero: false,
                    min: -5,  // Mindestwert für die Y-Achse (z.B. -5 °C)
                    max: 40,  // Höchstwert für die Y-Achse (z.B. 40 °C)
                    ticks: {
                        callback: function(value) {
                            return value + '°C';  // Temperaturen in °C anzeigen
                        }
                    }
                }
            }
        }
    });
}