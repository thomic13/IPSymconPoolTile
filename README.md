# IP-Symcon Pool Tile

`IPSymconPoolTile` ist ein IP-Symcon-Modul fuer die Kachelvisualisierung. Es fasst wichtige Poolwerte in einer kompakten, responsiven Statuskachel zusammen und ergaenzt die maximierte Ansicht um verlinkte Detailvariablen.

Das Modul ist fuer IP-Symcon 9.0 und die neue Kachelvisualisierung entwickelt.

## Funktionen

- Kompakte Pool-Kachel mit Wasser-/Pooltemperatur, Gesamtstatus und zentralen Messwerten.
- Anzeige von Filtersteuerung, Filterdruck, Elektrolyse-Strom, Redox, pH, pH-Tank, Wasser-/Einlauf-Temperatur und Rueckspuelstatus.
- Automatische Bewertung von Messwerten als `OK`, `Achtung` oder `Kritisch`.
- Farbige Hervorhebung betroffener Messwertkarten bei Warnung oder kritischem Zustand.
- Konfigurierbare Grenzwerte fuer pH, Redox, Filterdruck, Elektrolyse-Strom, Tankinhalt und Rueckspuelintervall.
- Elektrolyse-Strom mit ignorierbarem Aus-Bereich, damit `0 mA` bzw. abgeschaltete Elektrolyse keinen Alarm erzeugt.
- Nicht konfigurierte Variablen werden automatisch ausgeblendet.
- Maximierte Ansicht mit automatisch angelegten Links auf Detailvariablen.
- Optionale Anzeige zusaetzlicher Detailbereiche, Lichtwerte und Poolroboterwerte beim Groesserziehen der HTML-Kachel.
- Optionale Schaltaktionen in der Detailansicht.

## Anforderungen

- IP-Symcon 9.0 oder neuer.
- Kachelvisualisierung.
- Poolwerte als IP-Symcon-Variablen.
- Sinnvolle Variablenprofile fuer lesbare Einheiten, z. B. `mV`, `pH`, `%`, `mbar`, `mA`, `°C`.

## Installation

1. In IP-Symcon die Verwaltungskonsole oeffnen.
2. Zu `Kern Instanzen` -> `Modules` bzw. `Module Control` wechseln.
3. Dieses Repository hinzufuegen:

   ```text
   https://github.com/thomic13/IPSymconPoolTile
   ```

4. Eine neue Instanz `PoolTile` bzw. `Pool Kachel` anlegen.
5. In der Instanzkonfiguration die gewuenschten Variablen zuordnen.
6. Die Instanz in der Kachelvisualisierung platzieren.

Nach Modul-Updates sollte die Instanz einmal geoeffnet und mit `Uebernehmen` gespeichert werden, damit neue Eigenschaften und Detail-Links angelegt bzw. aktualisiert werden.

## Konfiguration

### Kompakte Kachel

Diese Variablen werden fuer die eigentliche Pool-Statuskachel verwendet:

| Einstellung | Beschreibung |
| --- | --- |
| Filterpumpensteuerung | Status der Filtersteuerung, z. B. Auto, Rueckspuelen, Stop |
| Filterdruck | Druck im Filterkreislauf |
| Elektrolyse Strom | Strom der Elektrolysezelle in mA |
| Redox Sonde | Redoxwert in mV |
| pH Sonde | pH-Wert |
| pH Tankinhalt | Fuellstand des pH-Tanks in Prozent |
| Skimmer / Bodenablauf Temperatur | Haupt-/Wassertemperatur |
| Einlaufduesen Temperatur | Temperatur am Einlauf |
| Tage seit Rueckspuelung | Anzahl Tage seit der letzten Rueckspuelung |
| Letztes Rueckspuelen am | Datum/Uhrzeit fuer die Detailansicht |

Nicht gesetzte Variablen werden nicht angezeigt.

### Detailansicht

Weitere Poolwerte koennen optional zugeordnet werden:

- Solarventil Steuerung
- Solarventil Position
- Filterpumpe
- Elektrolyse Status
- Algizid Tankinhalt
- Solarruecklauf Temperatur
- CPU Temperatur
- Durchfluss Messstrecke

### Licht und Poolroboter

Fuer die maximierte Ansicht und optionale Zusatzbereiche koennen Licht- und Roboterwerte verknuepft werden:

- Scheinwerfer links/rechts
- Weissanteil links/rechts
- RGB-Wert links/rechts
- Poolroboter
- Roboter Laufzeit Vorgabe
- Roboter Laufzeit verbleibend

## Grenzwerte und Statuslogik

Der Gesamtstatus oben rechts wird aus den relevanten Einzelbewertungen abgeleitet:

- `Kritisch`, sobald mindestens ein relevanter Wert kritisch ist.
- `Achtung`, sobald mindestens ein relevanter Wert warnend ist.
- `OK`, wenn alle relevanten Werte im guten oder neutralen Bereich liegen.

Aktuell fliessen pH, Redox, Filterdruck, Elektrolyse-Strom und Rueckspuelintervall in den Gesamtstatus ein.

### pH

- Gut, wenn der Wert zwischen `pH gut ab` und `pH gut bis` liegt.
- Warnend, wenn der Wert ausserhalb des guten Bereichs, aber noch innerhalb `pH warnend ab` bis `pH warnend bis` liegt.
- Kritisch ausserhalb des Warnbereichs.

### Redox

- Gut ab `Redox gut ab`.
- Warnend ab `Redox warnend ab`.
- Kritisch darunter.

### Filterdruck

- Gut unterhalb `Filterdruck warnend ab`.
- Warnend ab `Filterdruck warnend ab`.
- Kritisch ab `Filterdruck kritisch ab`.

### Elektrolyse-Strom

Der Elektrolyse-Strom kann als Indikator fuer den Salzgehalt genutzt werden.

- Neutral bis `Elektrolyse aus/ignorieren bis`, z. B. `100 mA`.
- Kritisch unter `Elektrolyse kritisch unter`, sofern oberhalb des ignorierten Aus-Bereichs.
- Warnend zwischen `Elektrolyse kritisch unter` und `Elektrolyse gut ab`.
- Gut zwischen `Elektrolyse gut ab` und `Elektrolyse gut bis`.
- Warnend bis `Elektrolyse warnend bis`.
- Kritisch oberhalb `Elektrolyse warnend bis`.

Beispiel fuer eine Zelle:

| Bereich | Beispiel |
| --- | --- |
| Aus/ignorieren | `<= 100 mA` |
| Kritisch niedrig | `< 6000 mA` |
| Warnend niedrig | `6000-6499 mA` |
| Gut | `6500-9500 mA` |
| Warnend hoch | `9501-10000 mA` |
| Kritisch hoch | `> 10000 mA` |

### Tankinhalt

- Gut oberhalb `Tank warnend unter`.
- Warnend bei oder unter `Tank warnend unter`.
- Kritisch bei oder unter `Tank kritisch unter`.

### Rueckspuelung

- Gut unterhalb `Rueckspuelung warnend ab`.
- Warnend ab `Rueckspuelung warnend ab`.
- Kritisch ab `Rueckspuelung kritisch ab`.

## Grosse Kachel und maximierte Ansicht

Das Modul unterscheidet zwei Situationen:

1. **Groesserziehen der HTML-Kachel**  
   Ueber die Optionen im Abschnitt `Grosse Kachel` kann gesteuert werden, ob zusaetzliche Pool-Details, Lichtwerte oder Poolroboterwerte direkt in der groesser gezogenen Kachel erscheinen.

2. **Maximierte Ansicht ueber den Kachel-Button**  
   Das Modul legt automatisch Links auf konfigurierte Detailvariablen unterhalb der Instanz an. Dadurch zeigt IP-Symcon in der maximierten Standardansicht die verlinkten Variablen an und ermoeglicht je nach Variablenprofil auch Bedienung.

Die automatisch angelegten Links verwenden interne Idents mit dem Praefix `PoolTileDetail`.

## Schaltaktionen

Die HTML-Kachel ist primaer als Anzeige gedacht. Schaltaktionen koennen in der Konfiguration aktiviert werden. Sie werden nur fuer als schaltbar markierte Werte in der Detailansicht vorbereitet.

Die maximierte IP-Symcon-Standardansicht nutzt dagegen die normalen Variablenlinks. Ob dort geschaltet werden kann, haengt vom jeweiligen Variablenprofil und der IP-Symcon-Konfiguration ab.

## Hinweise

- Fuer eine saubere Darstellung sollten die Variablen passende Profile und Einheiten haben.
- Bei geaenderten Variablenzuordnungen die Instanz mit `Uebernehmen` speichern, damit Detail-Links synchronisiert werden.
- Nicht benoetigte Variablen einfach leer lassen; die Kachel passt ihr Layout automatisch an.
- Das Modul speichert keine Zugangsdaten und benoetigt keine externen Dienste.

## Entwicklung

Repository-Struktur:

```text
library.json
README.md
PoolTile/
  form.json
  module.html
  module.json
  module.php
```

Das Modul nutzt das IP-Symcon HTML-SDK ueber `SetVisualizationType(1)` und `GetVisualizationTile()`.
