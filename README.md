# Tot & Wolkig


## Kurzbeschreibung des Projekts:

“Tot & Wolkig” ist eine interaktive Karte, die die letzten bekannten Aufenthaltsorte ausgestorbener Tierarten darstellt. Mithilfe von Leaflet.js zeigt die Anwendung eine Karte mit Markern für diese Orte. Durch das Hovern und Klicken auf einen Marker werden zusätzliche Informationen zu den Tieren sowie Wetterdaten der letzten sieben Tage für die jeweilige Region angezeigt. Dabei wird ein Diagramm zur Visualisierung der Temperaturverläufe verwendet, die dynamisch aus der Datenbank und einer Wetter-API abgerufen werden.

## Learnings:

    •	Leaflet.js: Einbindung und Konfiguration einer interaktiven Karte, inklusive Marker und dynamischer Anzeige von Daten.
    •	Chart.js: Erstellen eines Diagramms zur Visualisierung von Daten (Temperaturverläufe).
    •	AJAX und Fetch API: Effiziente Datenabfrage von APIs und Datenbanken zur Anzeige auf der Webseite.
    •	Datenbanken und PHP: Dynamische Generierung und Aktualisierung von Wetterdaten aus einer Datenbank mithilfe von PHP.
    •	Benutzerfreundlichkeit: Integration von Tooltips und animierten Übergängen für eine flüssige Benutzererfahrung.

## Schwierigkeiten:

	•	Kartenperformance: Bei vielen Markern kann es zu Performance-Problemen kommen, insbesondere bei der Anzeige vieler dynamischer Informationen.
	•	Responsive Diagramm: Die richtige Darstellung von Charts auf verschiedenen Bildschirmgrößen erforderte zusätzliche Anpassungen und Tests.
	•	Datenbankabfragen: Die korrekte Implementierung und Abfrage von Wetterdaten für verschiedene Standorte war eine Herausforderung.

## Benutzte Ressourcen:

	•	Leaflet.js für die interaktive Kartendarstellung (leafletjs.com).
	•	Chart.js für die Erstellung des Diagramms (chartjs.org).
	•	OpenWeather API und wttr.in zur Abfrage von Wetterdaten.
	•	PHP und MySQL zur Datenverarbeitung und Speicherung der Tier- und Wetterinformationen.
	•	CSS für das Design und Layout der Seite, um eine responsive Benutzererfahrung zu gewährleisten.
    •	ChatGPT zur Unterstützung bei der Entwicklung, Dokumentation und Fehlerbehebung.
	•	GitHub Copilot zur Beschleunigung des Programmierprozesses und zur Unterstützung bei Code-Vervollständigungen.