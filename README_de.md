# Keymaster_XH

Keymaster_XH ermöglicht es den Administrationsbereich einer
CMSimple_XH Website zu sperren, wenn bereits ein Adminstrator
angemeldet ist. Das ist nötig, wenn mehr als ein Adminstrator Zugriff
auf den Administrationsbereich hat, da CMSimple_XH keine
Vorkehrung bzgl. gleichzeitiger Bearbeitung trifft.

Ist ein Administrator bereits in der Website angemeldet,
werden andere Administratoren nach der Anmeldung informiert,
dass sie die Website nicht bearbeiten können, und ihnen wird empfohlen
sich wieder abzumelden.
Nach einem einstellbaren Zeitraum von Inaktivität kann ein anderer
Administrator die Bearbeitungssitzung übernehmen, woraufhin dem vorherigen
Administrator die weitere Bearbeitung der Website vorerst untersagt wird.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Fehlerbehebung](#fehlerbehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Keymaster_XH ist ein Plugin für [CMSimple_XH](https://cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0,
und PHP ≥ 7.1.0 mit der Json Extension, und einen zeitgemäßen Browser.
Keymaster_XH benötigt weiterhin [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.8;
ist dieses noch nicht installiert (see *Einstellungen*→*Info*),
laden Sie das [aktuelle Release](https://github.com/cmb69/plib_xh/releases/latest)
herunter, und installieren Sie es.


## Download

Das [aktuelle Release](https://github.com/cmb69/keymaster_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen
CMSimple_XH-Plugins auch. Im
[CMSimple_XH-Wiki](https://wiki.cmsimple-xh.org/doku.php/de:installation#plugins)
finden Sie weitere Details.

1. **Sichern Sie die Daten auf Ihrem Server.**
1. Entpacken Sie die ZIP-Datei auf Ihrem Rechner.
1. Laden Sie das ganze Verzeichnis `keymaster/` auf Ihren Server in das
   `plugins/` Verzeichnis von CMSimple_XH  hoch.
1. Machen Sie die Unterverzeichnisse `config/`, `css/`, `languages/`
   und die Datei `key` beschreibbar.
1. Gehen Sie zu `Plugins` → `Keymaster` im Administrationsbereich,
   um zu prüfen, ob alle Voraussetzungen erfüllt sind.

## Einstellungen

Die Plugin-Konfiguration erfolgt wie bei vielen anderen
CMSimple_XH-Plugins auch im Administrationsbereich der Website.
Wählen Sie `Plugins` → `Keymaster`.

Sie können die Voreinstellungen von Keymaster_XH unter
`Konfiguration` ändern. Hinweise zu den Optionen werden beim
Überfahren der Hilfe-Icons mit der Maus angezeigt.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können die
Sprachtexte in Ihre eigene Sprache übersetzen, falls keine
entsprechende Sprachdatei zur Verfügung steht, oder diese Ihren
Wünschen gemäß anpassen.

Das Aussehen von Keymaster_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Nach der Installation ist das Plugin bereits voll funktionsfähig.
Es ist nur wichtig sich ornungsgemäß abzumelden, wenn die Bearbeitung
der Website abgeschlossen ist, so dass andere Adminstratoren nicht
warten müssen, bis die Sitzung als inaktiv angesehen wird.

## Fehlerbehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/keymaster_xh/issues) oder im
[CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Keymaster_XH ist freie Software. Sie können es unter den Bedingungen der
GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Keymaster_XH erfolgt in der Hoffnung, dass es
Ihnen von Nutzen sein wird, aber ohne irgendeine Garantie, sogar ohne
die implizite Garantie der Marktreife oder der Verwendbarkeit für einen
bestimmten Zweck. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Keymaster_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright 2013-2023 Christoph M. Becker

Französische Übersetzung © 2014 Patrick Varlet

## Danksagung

Keymaster_XH wurde von Martin Damkens und Gert Ebersbachs
[LoginLocker](https://ge-webdesign.de/cmsimpleplugins/?Eigene_Plugins___LoginLocker)
und einem brillianten Film (raten Sie mal von welchem ;) angeregt.

Das Plugin Icon wurde von [Alessandro Rei](http://www.mentalrey.it/) gestaltet.
Vielen Dank für die Veröffentlichung dieses Icons unter GPL.

Vielen Dank an die Community im
[CMSimple_XH-Forum](https://www.cmsimpleforum.com/) für Hinweise,
Anregungen und das Testen.

Und zu guter letzt vielen Dank an [Peter Harteg](https://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von [CMSimple_XH](https://www.cmsimple-xh.org/de/)
ohne die es dieses phantastische CMS nicht gäbe.
