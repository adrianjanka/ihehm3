// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
    // Initialize the map
    var map = L.map('map').setView([20, 0], 2); // Centered at latitude 20, longitude 0

    // Load the OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Define pin locations
    var locations = [
        {
            name: "Chur, Switzerland",
            coords: [46.85, 9.53], // Coordinates for Chur
            description: "Last seen: Quagga, 1872"
        },
        {
            name: "Lake Atitlán, Guatemala",
            coords: [14.7, -91.2], // Coordinates for Lake Atitlán
            description: "Last seen: Atitlán grebe, 1983"
        },
        {
            name: "Antananarivo, Madagascar",
            coords: [-18.8792, 47.5079], // Coordinates for Antananarivo
            description: "Last seen: Afrocyclops pauliani, 1951"
        }
    ];

    // Loop through each location and add a marker
    locations.forEach(function (location) {
        var marker = L.marker(location.coords).addTo(map);
        marker.bindPopup("<b>" + location.name + "</b><br>" + location.description).openPopup();
    });
});