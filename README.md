# IP-Symcon Pool Tile

Erster Prototyp fuer eine eigene Pool-Kachel in der IP-Symcon Kachelvisualisierung.

## Ziel

- Kleine Kachel: reine Anzeige der wichtigsten Poolwerte.
- Maximierte Ansicht: alle konfigurierten Poolwerte, Lichtwerte und Poolroboterwerte.
- Nicht gesetzte Variablen verschwinden automatisch aus der Darstellung.
- Grenzwerte fuer pH, Redox, Filterdruck, Tankinhalt und Rueckspuelung sind in der Instanz einstellbar.
- Schaltaktionen sind vorbereitet und koennen in der Konfiguration aktiviert werden.

## Aus deinem Screenshot bereits abgeleitete Variablen

| Wert | ID |
| --- | ---: |
| Filterpumpe Steuerung | 29044 |
| Solarventil Steuerung | 45158 |
| Solarventil Position | 17902 |
| Filterpumpe | 37240 |
| Filterdruck | 28993 |
| Elektrolyse | 29726 |
| Elektrolyse Strom | 10104 |
| Redox Sonde | 37235 |
| pH Sonde | 28995 |
| pH Tankinhalt | 26171 |
| Algizid Tankinhalt | 42368 |
| Skimmer / Bodenablauf | 16570 |
| Einlaufduesen | 28998 |
| Solarruecklauf | 16571 |
| CPU Temp | 16569 |
| Durchfluss Messstrecke | 37244 |
| Letztes Rueckspuelen am | 10110 |
| Tage seit Rueckspuelung | 10111 |

## Installation als lokales Entwicklungsmodul

1. Den Ordner `IPSymconPoolTile` auf dein Symcon-System kopieren.
2. In IP-Symcon das Modul ueber die Modulverwaltung als lokales Repository bzw. Entwicklungsmodul einbinden.
3. Den Symcon-Dienst neu starten, falls IP-Symcon das lokale Modul nicht sofort neu einliest.
4. Eine neue Instanz `PoolTile` anlegen.
5. Die Variablen in der Instanzkonfiguration zuordnen.
6. Die Instanz in der Kachelvisualisierung platzieren.

## Offene Punkte fuer die naechste Iteration

- Verhalten der maximierten Ansicht in IP-Symcon 9.0 live pruefen.
- Farbprofil der RGB-Variablen pruefen, weil Symcon-Farbwerte je nach Profil unterschiedlich kodiert sein koennen.
- Schaltaktionen fuer Integer-Profile sauber auf die vorhandenen Profile mappen, z. B. Auto, Rueckspuelen, Stop.
- Optional: eigene Detail-Controls fuer Filterpumpensteuerung und Poolroboter-Laufzeit bauen.
