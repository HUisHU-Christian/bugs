ALTER TABLE tags ADD COLUMN ZH_TW VARCHAR(255) AFTER tag;
ALTER TABLE tags ADD COLUMN ZH_CN VARCHAR(255) AFTER tag;
ALTER TABLE tags ADD COLUMN RU VARCHAR(255) AFTER tag;
ALTER TABLE tags ADD COLUMN IT VARCHAR(255) AFTER tag;
ALTER TABLE tags ADD COLUMN FR VARCHAR(255) AFTER tag;
ALTER TABLE tags ADD COLUMN ES VARCHAR(255) AFTER tag;
ALTER TABLE tags ADD COLUMN EN VARCHAR(255) AFTER tag;
ALTER TABLE tags ADD COLUMN DE VARCHAR(255) AFTER tag;

UPDATE tags SET EN = tag;
UPDATE tags SET ES = 'Estado: Aberto', FR='État:ouvert' WHERE id = 1;
UPDATE tags SET ES = 'Estado: Cerado', FR='État: Fermé' WHERE id = 2;
UPDATE tags SET ES = 'Tipo: desarollo', FR='Type: développement' WHERE id = 3;
UPDATE tags SET ES = 'Tipo: debug', FR='Type:débogage' WHERE id = 4;
UPDATE tags SET ES = 'Deicsion: impossible', FR='Verdict: impossible :(' WHERE id = 6;
UPDATE tags SET ES = 'Decision: Solucionado', FR='Verdict: Résolu ! :)' WHERE id = 7;
UPDATE tags SET ES = 'Estado: haciendo tests', FR='État: nous testons' WHERE id = 8;
UPDATE tags SET ES = 'Tipo: melioracion', FR='Type:amélioration' WHERE id = 17;
UPDATE tags SET ES = 'Estado: progressamos', FR='État: Progressons' WHERE id = 9;
UPDATE tags SET ES = 'Decision: tal vez.  Hay que ver', FR='Verdict: piste à explorer' WHERE id = 18;

ALTER TABLE activity ADD COLUMN updated_at  datetime default NOW() AFTER activity;
ALTER TABLE activity ADD COLUMN created_at datetime default NOW() AFTER activity;
ALTER TABLE activity ADD COLUMN ZH_TW VARCHAR(255) AFTER description;
ALTER TABLE activity ADD COLUMN ZH_CN VARCHAR(255) AFTER description;
ALTER TABLE activity ADD COLUMN RU VARCHAR(255) AFTER description;
ALTER TABLE activity ADD COLUMN IT VARCHAR(255) AFTER description;
ALTER TABLE activity ADD COLUMN FR VARCHAR(255) AFTER description;
ALTER TABLE activity ADD COLUMN ES VARCHAR(255) AFTER description;
ALTER TABLE activity ADD COLUMN EN VARCHAR(255) AFTER description;
ALTER TABLE activity ADD COLUMN DE VARCHAR(255) AFTER description;

UPDATE activity SET EN = description;
UPDATE activity SET FR = 'Nouveau billet créé' WHERE id = 1;
UPDATE activity SET FR = 'Nouveau commentaire sur un billet' WHERE id = 2;
UPDATE activity SET FR = 'Billet fermé' WHERE id = 3;
UPDATE activity SET FR = 'Billet rouvert' WHERE id = 4;
UPDATE activity SET FR = 'Changement de responsable du billet' WHERE id = 5;
UPDATE activity SET FR = 'Mise à jour des étiquettes' WHERE id = 6;
UPDATE activity SET FR = 'Fichier joint au billet' WHERE id = 7;
UPDATE activity SET FR = 'Déplacement d`un billet du projet A vers le projet B' WHERE id = 8;
UPDATE activity SET FR = 'Un usager a commencé ou cessé de suivre le billet' WHERE id = 9;
UPDATE activity SET FR = 'Mise à jour d`un billet' WHERE id = 10;
UPDATE activity SET FR = 'Commentaire supprimé' WHERE id = 11;
UPDATE activity SET FR = 'Commentaire modifié' WHERE id = 12;
UPDATE activity SET FR = 'Temps de travail d`un ouvrier' WHERE id = 13;
UPDATE activity SET ES = 'Nuevo billette' WHERE id = 1;
UPDATE activity SET ES = 'Nuevo commentario' WHERE id = 2;
UPDATE activity SET ES = 'Billette cerado' WHERE id = 3;
UPDATE activity SET ES = 'Billette habierto de vuelta' WHERE id = 4;
UPDATE activity SET ES = 'Cambio de « in carcado »' WHERE id = 5;
UPDATE activity SET ES = 'Cambio de etiqueta' WHERE id = 6;
UPDATE activity SET ES = 'Documento añadido al billette' WHERE id = 7;
UPDATE activity SET ES = 'Billette colocado de proyecto A al proyecto B' WHERE id = 8;
UPDATE activity SET ES = 'Un empezo o parro de seguir este billette' WHERE id = 9;
UPDATE activity SET ES = 'Combio a la descripcion del billette' WHERE id = 10;
UPDATE activity SET ES = 'Commentario borrado' WHERE id = 11;
UPDATE activity SET ES = 'Commentario modificado' WHERE id = 12;
UPDATE activity SET ES = 'Tiempo de trabajo' WHERE id = 13;

ALTER TABLE `update_history`
	CHANGE `DteRelease` `DteRelease` date NULL AFTER `Description`,
	CHANGE `DteInstall` `DteInstall` date NULL AFTER `DteRelease`;

