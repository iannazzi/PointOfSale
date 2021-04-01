/*!40101 SET NAMES utf8 */;

INSERT INTO categories_langs (category_id, language_id, category_name) VALUES  (1, 'de', 'Hauptkategorie');

INSERT INTO config_langs VALUES (41,'de',1,'Benutze Kurzansicht in der Wochenansicht',NULL);
INSERT INTO config_langs VALUES (42,'de',2,'Benutze Kurzansicht in der Tagesansicht',NULL);
INSERT INTO config_langs VALUES (43,'de',3,'Kalender Snapshot Modus','None;Nicht anzeigen;Current;Zeige aktuellen Monat;Selected;Ausgewahler Monat');
INSERT INTO config_langs VALUES (44,'de',4,'Benutzer konnen Stil auswahlen',NULL);
INSERT INTO config_langs VALUES (45,'de',5,'Benutzer konnen Sprache auswahlen',NULL);
INSERT INTO config_langs VALUES (46,'de',6,'Vorgabestil','Basic;Basic;Blueprint;Blueprint;CoffeeBreak;CoffeeBreak;Compact;Compact;GreenApple;GreenApple;Innovation;Innovation;None;None;Pine;Pine;SandBeach;SandBeach;School;School');
INSERT INTO config_langs VALUES (47,'de',7,'Vorgabesprache','en;English;de;Deutsch;ru;Russian');
INSERT INTO config_langs VALUES (48,'de',8,'Menu-Typ','None;None;Vertical;Vertical;Horizontal;Horizontal');
INSERT INTO config_langs VALUES (49,'de',9,'Seiten-Header',NULL);
INSERT INTO config_langs VALUES (50,'de',10,'Seiten-Footer',NULL);
INSERT INTO config_langs VALUES (51,'de',11,'Registrierungs-Typ','0;Keine Registrierung;1;Registrierung ohne Bestatigung;4;Registrierung mit Bestatigung per E-Mail;8;Registrierung mit Bestatigung durch einen Administrator');
INSERT INTO config_langs VALUES (52,'de',12,'E-Mail fur das Von-Feld',NULL);
INSERT INTO config_langs VALUES (53,'de',13,'Name des SMTP-Servers',NULL);
INSERT INTO config_langs VALUES (54,'de',14,'Portnummer des SMTP-Servers',NULL);
INSERT INTO config_langs VALUES (55,'de',15,'Zeige Wochen-Icon in der Jahresubersicht',NULL);
INSERT INTO config_langs VALUES (56,'de',16,'Zeige Kalendar-Snapshot in den Ansichten','2;Monat, Woche, Tag;4;Woche, Tag');
INSERT INTO config_langs VALUES (57,'de',17,'Zeige Wochen-Icon im Kalender-Snapshot',NULL);
INSERT INTO config_langs VALUES (58,'de',18,'Offne Popup-Fenster fur die Events',NULL);
INSERT INTO config_langs VALUES (59,'de',19,'Zeige Navigation im Kalender-Snapshot',NULL);
INSERT INTO config_langs VALUES (60,'de',20,'Zeit-Format','1;Vordefiniert (abhangig von der System-Locale);2;Militarisch (14:20);3;US-Standard (2:20 PM)');

INSERT INTO contents_langs VALUES (15,1,'de','Wird dem Benutzer nach der Registrierung angezeigt, wenn diese noch per E-Mail bestatigt werden muss','<h3>{user_login}</h3>\r\n<h4>Vielen Dank fur Ihre Registrierung.</h4>\r\n<p>Sie erhalten in Kurze eine Bestatigungs-E-Mail mit weiteren Anleitungen zur Aktivierung.</p>\r\n<p>Die E-Mail wurde an {user_email} gesendet.</p>');
INSERT INTO contents_langs VALUES (16,2,'de','Wird dem Benutzer nach der Registrierung angezeigt, wenn diese vom Administrator noch bestatigt werden muss','<h3>{user_login}</h3>\r\n<h4>Vielen Dank fur Ihre Registrierung.</h4>\r\n<h5>Ihr Account muss von einem Adminstrator noch bestatigt werden, bevor er gultig wird.</h5>');
INSERT INTO contents_langs VALUES (17,3,'de','Wird dem Benutzer nach der Registrierung angezeigt, wenn keine weitere Bestatigung erfolgen muss','<h3>{user_login}</h3>\r\n<h4>Vielen Dank fur Ihre Registrierung.</h4>');
INSERT INTO contents_langs VALUES (18,4,'de','Wird nach dem Andern des Passworts angezeigt','<h3>{user_login}</h3>\r\n<p>Ihr Passwort wurde ergolgreich geandert.</p>\r\n<p><a href=\"profile.php\">Zuruck zum Profil</a></p>');
INSERT INTO contents_langs VALUES (19,5,'de','Wird nach der Bestatigung des Accounts angezeigt','<h3>{user_login}</h3>\r\n<h2>Ihr Account in nun aktiv.</h2>\r\n');
INSERT INTO contents_langs VALUES (20,6,'de','Wird angezeigt, nachdem das Passwort versendet wurde','<h2>E-Mail gesendet</h2>\r\n<p>Wenn Sie den korrekten Benutzernamen oder die korrekte E-Mail-Adresse fur den Account angegeben haben, wird eine Bestatigung an die angegebene E-Mail-Adresse versendet.</p><p>Bitte prufen Sie Ihren Posteingang.</p><a href=\".\">Zuruck zur Hauptseite</a>.</p>');
INSERT INTO contents_langs VALUES (21,7,'de','Wird in der \"Passwort vergessen\"-Seite angezeigt','<h2>Willkommen, {user_login}</h2>\r\n<p>Sie konnen Ihr Passwort jetzt andern.</p>\r\n');

INSERT INTO custom_fields_langs VALUES (25,'de',1,'Ort');
INSERT INTO custom_fields_langs VALUES (26,'de',2,'Preis');
INSERT INTO custom_fields_langs VALUES (27,'de',3,'URL');
INSERT INTO custom_fields_langs VALUES (28,'de',4,'TextBox 1');
INSERT INTO custom_fields_langs VALUES (29,'de',5,'TextBox 2');
INSERT INTO custom_fields_langs VALUES (30,'de',6,'TextBox 3');
INSERT INTO custom_fields_langs VALUES (31,'de',7,'TextArea 1');
INSERT INTO custom_fields_langs VALUES (32,'de',8,'TextArea 2');
INSERT INTO custom_fields_langs VALUES (33,'de',9,'TextArea 3');
INSERT INTO custom_fields_langs VALUES (34,'de',10,'CheckBox 1');
INSERT INTO custom_fields_langs VALUES (35,'de',11,'CheckBox 2');
INSERT INTO custom_fields_langs VALUES (36,'de',12,'CheckBox 3');

INSERT INTO permissions_langs VALUES (13,1,'de','Wer kann neue Events einfugen');
INSERT INTO permissions_langs VALUES (14,2,'de','Wer kann offentliche Events aktualisieren');
INSERT INTO permissions_langs VALUES (15,3,'de','Wer kann offentliche Events loschen');
INSERT INTO permissions_langs VALUES (16,4,'de','Wer kann offentliche Events loschen');
INSERT INTO permissions_langs VALUES (17,5,'de','Wer kann private Events lesen');
INSERT INTO permissions_langs VALUES (18,6,'de','Wer kann private Events lesen loschen');

INSERT INTO email_templates_lang VALUES (7,'de',1,'Bestatigungs-E-Mail nach der Registrierung','Bestatigung','Willkommen {user_login},\r\n\r\nam {date_time} wurde in unserem Online-Veranstaltungkalender ein Account fur die E-Mail-Adresse {user_email} beantragt. Wenn Sie diesen Account bestatigen mochten, dann besuchen Sie bitte die Aktivierungsseite {activate_url}.\r\n\r\nWenn Sie diese E-Mail nicht angefordert haben, so loschen Sie sie bitte.\r\n\r\nDie Bestatigung der Registrierung ist nur innerhalb von 24 Stunden moglich.\r\n');
INSERT INTO email_templates_lang VALUES (8,'de',2,'Nachricht nach Bestatigung durch den Administrator','Ihr Account wurde bestatigt','Willkommen {user_login},\r\n\r\nIhr Account wurde durch den Administrator bestatigt. und ist ab sofort gultig.\r\n\r\nLink:  {site_url}.\r\n');
INSERT INTO email_templates_lang VALUES (9,'de',3,'E-Mail fur Benutzer, die ihr Passwort vergessen haben','Passwort vergessen','Jemand (vermutlich Sie) hat eine Passwort-Anderung veranlasst. Falls Sie dies nicht waren, so loschen Sie bitte diese E-Mail; Ihre Daten bleiben weiterhin gultig.\r\n\r\nAnsonsten folgen Sie bitte dem untenstehenden Link und andern Sie Ihr Passwort:  \r\n\r\n{activate_url}\r\n');


UPDATE config SET config_listbox = 'en;English;de;German;ru;Russian' WHERE config_id= 7;
UPDATE config_langs SET config_listbox = 'en;English;de;German;ru;Russian' WHERE config_id= 7 AND language_id='en';
UPDATE config_langs SET config_listbox = 'en;Английский;de;Немецкий;ru;Русский' WHERE config_id= 7 AND language_id='ru';


INSERT INTO config VALUES (21,'event_day_style','Style for days containing events','font-weight: bold;color:#FF0000', 2, 2, '');

INSERT INTO config_langs VALUES (61,'en',21,'Style for days containing events','');
INSERT INTO config_langs VALUES (62,'de',21,'Stil fur die Tage Falle enthalten','');
INSERT INTO config_langs VALUES (63,'ru',21,'Стиль для дней, содержащих события','');
