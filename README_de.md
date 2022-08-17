# Keymaster_XH

Keymaster_XH ermöglicht es den Administrationsbereich einer
CMSimple_XH Website zu sperren, wenn bereits ein Anwender
angemeldet ist. Das ist nötig, wenn mehr als ein Anwender Zugriff
auf den Administrationsbereich hat, da CMSimple_XH keine
Vorkehrung bzgl. gleichzeitiger Bearbeitung trifft. Nach einem
einstellbaren Zeitraum von Inaktivität wird der Administrator
automatisch abgemeldet, um zu verhindern, dass die Website für
„immer“ gesperrt ist, wenn die ordnungsgemäße Abmeldung vergessen
wurde. Bevor dies geschieht, wird der Administrator darauf
hingewiesen und hat die Möglichkeit die Sitzung zu verlängern, um
den Verlust von ungespeicherten Änderungen zu vermeiden.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Einschränkungen](#einschränkungen)
- [Fehlerbehebung](#fehlerbehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Keymaster_XH ist ein Plugin für CMSimple_XH. Es benötigt eine
UTF-8 kodierte CMSimple_XH Version.

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

### Wie funktioniert es?

Wenn kein Anwender im Administrationsmodus angemeldet ist, hält
der Schlüsselmeister einen einzigen Schlüssel. Sobald sich ein
Anwender anmeldet, wird diesem der Schlüssel übergeben und der
Zugriff auf das CMSimple_XH Back-End gewährt. Da es nur einen
Schlüssel gibt, können sich andere Anwender nicht anmelden; sie
werden einfach mit einer entsprechenden Meldung abgewiesen (vgl.
aber [Einschränkungen](#einschränkungen)).

Nach einer einstellbaren Inaktivitätsdauer, wird der Anwender
abgemeldet, was den Schlüssel wieder an den Schlüsselmeister
zurück gibt. Unter Inaktivitätsdauer versteht man einen Zeitraum,
in der keine Anfrage an den Server gestellt wurde, die vom
Schlüsselmeister erkannt werden konnte. Eine einstellbare Weile
bevor der Anwender abgemeldet wird, wird er aufgefordert die
Sitzung zu verlängern, was dann eine erkennbare Anfrage auslöst.

Es ist möglich mehrere Browserfenster (bzw. -tabs; im folgenden
bezieht sich der Begriff Browserfenster ebenso auf Browsertabs)
für die selbe CMSimple_XH Installation offen zu haben; Aktivität
in einem Fenster wird durch den Schlüsselmeister von den anderen
ebenfalls erkannt, was allerdings einen Moment dauern kann
(abhängig vom Poll-Intervall).

⚠ Achtung! Das Bearbeiten einer CMSimple_XH Website in mehr als
einem Browserfenster funktioniert im Allgemeinen nicht. Außer wenn
Sie genau wissen, was Sie tun, führen Sie Änderungen nur in einem
einzigen Fenster durch, und behandeln Sie die anderen als seien
sie schreibgeschützt.

Falls Sie verpasst haben die Sitzung rechtzeitig zu verlängern,
und vom Schlüsselmeister abgemeldet wurden, obwohl Sie Änderungen
noch nicht gespeichert hatten, besteht die *Chance* diese wieder
zu bekommen: melden Sie sich in einem anderen Browserfenster bei
der Website an, und betätigen Sie den Zurück-Schalter im
ursprünglichen Fenster. Mit etwas Glück sind Ihre Änderungen noch
da.

## Einschränkungen

Für `security_type=javascript` bzw. `wwwaut` wird der
Anmeldeversuch eines zweiten Anwenders keine Meldung anzeigen; der
Anwender kann sich einfach nicht anmelden. Lösung: wechseln Sie zu
`security_type=page`.

Manche Erweiterungen (z.B.
[Chat_XH](https://github.com/cmb69/chat_xh)) senden periodisch
Hintergrundanfragen an den Server, die vom Schlüsselmeister als
Aktivität gewertet werden könnten. Wenn eine solche Erweiterung
aktiv ist, funktioniert die automatische Abmeldung nicht, d.h.
andere Anwender werden nicht in der Lage sein sich anzumelden, bis
das Browserfenster geschlossen wird. Lösung: melden Sie sich immer
ordnungsgemäß ab, wenn Sie mit dem Bearbeiten der Website fertig
sind.

Wenn ein angemeldeter Anwender das Browserfenster schließt (aber
nicht den gesamten Browser), und ein anderer Anwender sich später
anmeldet, kann der erste Anwender die Prüfung durch den
Schlüsselmeister durch erneute Navigation zur CMSimple_XH
Installation umgehen. Lösung: melden Sie sich ordnungsgemäß ab,
wenn Sie mit der Bearbeitung fertig sind (zumindest sollten Sie
den Browser schließen).

Nach dem Ändern des Administator Passworts ist Website für die
Dauer des automatischen Abmelde-Zeitraums gesperrt. Lösung: ändern
Sie das Administator Passwort nachdem Sie alle anderen
Bearbeitungsaktivitäten abgeschlossen haben.

Wenn Sie in einer anderen CMSimple_XH Installation in einem
übergeordneten Ordner angemeldet sind, erlaubt Keymaster das Login
nicht. Lösung: melden Sie sich zunächst bei der anderen
Installation ab.

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

Copyright 2013-2019 Christoph M. Becker

Französische Übersetzung © 2014 Patrick Varlet

## Danksagung

Keymaster_XH wurde von Martin Damkens und Gert Ebersbachs
[LoginLocker](https://ge-webdesign.de/cmsimpleplugins/?Eigene_Plugins___LoginLocker)
und einem brillianten Film (raten Sie mal von welchem ;) angeregt.

Das Plugin Icon wurde von [Alessandro Rei](http://www.mentalrey.it/) gestaltet.
Vielen Dank für die Veröffentlichung dieses Icons unter GPL.

Diese Plugin verwendet „free applications icons“ von
[Aha-Soft](http://www.aha-soft.com/). Vielen Dank für die freie
Nutzbarkeit dieser Icons.

Vielen Dank an die Community im
[CMSimple_XH-Forum](https://www.cmsimpleforum.com/) für Hinweise,
Anregungen und das Testen.

Und zu guter letzt vielen Dank an [Peter Harteg](https://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von [CMSimple_XH](https://www.cmsimple-xh.org/de/)
ohne die es dieses phantastische CMS nicht gäbe.
