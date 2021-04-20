# RRZE-UnivIS

Darstellung von Lehrveranstaltungen und organisatorischen Daten aus UnivIS.

## Download 

GITHub-Repo: https://github.com/RRZE-Webteam/rrze-univis


## Autor 
RRZE-Webteam , http://www.rrze.fau.de

## Copryright

GNU General Public License (GPL) Version 3 


## Zweck 

Shortcode [univis] zum Einbindung von UnivIS-Daten in WordPress-Seiten.


## Dokumentation

Eine vollständige Dokumentation mit vielen Anwendungsbeispielen findet sich auf der Seite: 
https://www.wordpress.rrze.fau.de/plugins/fau-und-rrze-plugins/rrze-univis/


### Kurzeinführung


#### WP-Einstellungsmenü

Einstellungen › UnivIS

#### Verwendung des Shortcodes [univis]

- Anzeige eines Links zur UnivIS-Startseite. Der Linktext kann unter Einstellungen / RRZE-UnivIS modifiziert werden
```
[univis]
```
- Bindet die Mitarbeiterübersicht ein - besonders geeignet für wissenschaftliche Einrichtungen (Telefonnummern und E-Mail-Adressen können ein- und ausgeblendet werden.)
```
[univis number="1005681200"]
[univis task="mitarbeiter-alle"]
[univis task="mitarbeiter-alle" show="telefon, mail"]
[univis task="mitarbeiter-alle"]
```    
- Bindet die Mitarbeiterübersicht ein - besonders geeignet für nicht-wissenschaftliche Einrichtungen (Telefonnummern und E-Mail-Adressen können ein- und ausgeblendet werden.)
```
[univis task="mitarbeiter-orga"]
[univis task="mitarbeiter-orga" hide="telefon"]
[univis task="mitarbeiter-orga" show="mail"]
```
- Bindet die Mitarbeiterübersicht der Organisationseinheit mit der UnivISOrgNr 1005681200 im Telefonbuchformat ein (alphabetische Sortierung, optional mit Telefonnummern, E-Mail-Adressen, Sprungmarken)
```
[univis number="1005681200" task="mitarbeiter-telefonbuch"]
[univis number="1005681200" task="mitarbeiter-telefonbuch" show="telefon, mail, sprungmarken"]
```
- Bindet sämtliche Lehrveranstaltungen der Organisationseinheit mit der UnivISOrgNr 1005681200 ein
```
[univis number="1005681200" task="lehrveranstaltungen-alle"]
```
- Bindet sämtliche UnivIS-Publikationen der eingestellten Organisationseinheit ein
```
[univis task="publikationen"]
```


##### Ausblenden importierter Lehrveranstaltungen möglich

- Blendet alle importierten Lehrveranstaltungen aus, um doppelte Ausgaben zu vermeiden
```
[univis task="lehrveranstaltungen-alle" id="49680223" lv_import="0"]
```

##### Filterung nach Lehrveranstaltungstyp möglich

- Gibt alle Vorlesungen der Org-Nr. 21101522 aus. Bei type müssen die Kürzel wie im Vorlesungsverzeichnis angegeben werden (vorl, ueb, tut, ...)
```
[univis task="lehrveranstaltungen-alle" id="21101522" type="vorl"]
```
- Bindet die Daten der einen Person ein. Die Person muss dabei der Organisationseinheit angehören, die in Einstellungen - UnivIS eingegeben wurde
```
[univis task="mitarbeiter-einzeln" firstname="Max" lastname="Mustermann"]
[univis task="mitarbeiter-einzeln" name="Mustermann,Max"]
```
- Zeigt die Daten zur Lehrveranstaltung mit dieser ID. Die Lehrveranstaltung muss dabei der Organisationseinheit zugeordnet sein, die in Einstellungen - UnivIS eingegeben wurde und aus dem aktuellen Semester stammen
```
[univis task="lehrveranstaltungen-einzeln" id="21101522"]
```
- Publikationen, eingeschränkt nach Erscheinungsjahr:
```
[univis task="publikationen" since="2017"]
```

- Zeigt alle Lehrveranstaltungen der Person mit dieser ID. Der Dozent muss dabei der Organisationseinheit angehören, die in Einstellungen - UnivIS eingegeben wurde.
```
[univis task="lehrveranstaltungen-alle" dozentid="21555666"]
```
- Zeigt alle Lehrveranstaltungen der Person mit dem Namen Max Mustermann. Der Dozent muss dabei der Organisationseinheit angehören, die in Einstellungen - UnivIS eingegeben wurde. Der Name des Dozenten muss in der Form Nachname,Vorname ohne Leerzeichen angegeben werden.
```
[univis task="lehrveranstaltungen-alle" dozentname="Mustermann,Max"]
```
- Zeigt alle Lehrveranstaltungen an:

Im aktuellen Semester
```
[univis task="lehrveranstaltungen-alle"]
```

Im nächsten Semester
```
[univis task="lehrveranstaltungen-alle" sem="1"]
```

Im vergangenen Semester
```
[univis task="lehrveranstaltungen-alle" sem="-1"]
```

Im Sommersemester 2017
```
[univis task="lehrveranstaltungen-alle" sem="2017s"]
```

#### Hinweise

- Der Shortcode-Parameter number kann weggelassen werden, wenn in der Einstellungsseite des Plugins (Einstellungen - UnivIS) eine UnivISOrgNr vergeben wird. 

- Bei der Anzeige von Lehrveranstaltungen wird automatisch das Semester angezeigt, dass bei UnivIS als aktuelles Semester eingestellt ist. 

- Formatierungen von UnivIS werden in HTML übersetzt (fett, kursiv, hochgestellt, tiefgestellt, automatische Links, mehrzeilig)

- Die UnivIS-ID einer Lehrveranstaltung, Organisation oder Person finden Sie über die Suche unter "Settings" oder in der Metabox beim Erstellen eines Posts oder einer Page.