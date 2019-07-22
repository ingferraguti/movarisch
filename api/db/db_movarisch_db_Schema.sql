--
-- Database: `movarisch_db`
--

CREATE DATABASE IF NOT EXISTS `movarisch_db`;
USE `movarisch_db`;


-- ENTITIES

--
-- Struttura della tabella `frasih`
--

CREATE TABLE IF NOT EXISTS `frasih` (
	`Codice` varchar(130)  NOT NULL,
	`Descrizione` varchar(130)  NOT NULL,
	`Punteggio` decimal(6,2)  NOT NULL,
	
	-- RELAZIONI

	`_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT 

);





--
-- Struttura della tabella `miscelanonpericolosa`
--

CREATE TABLE IF NOT EXISTS `miscelanonpericolosa` (
	`Nome` varchar(130)  NOT NULL,
	`Score` decimal(6,2) ,
	
	-- RELAZIONI

	`_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT 

);




-- relation m:m Sostanza Miscelanonpericolosa - Sostanza
CREATE TABLE IF NOT EXISTS `Miscelanonpericolosa_Sostanza` (
    `_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_Miscelanonpericolosa` int(11)  NOT NULL REFERENCES Miscelanonpericolosa(_id),
    `id_Sostanza` int(11)  NOT NULL REFERENCES Sostanza(_id)
);


--
-- Struttura della tabella `processo`
--

CREATE TABLE IF NOT EXISTS `processo` (
	`AltaEmissione` bool  NOT NULL,
	`Nome` varchar(130)  NOT NULL,
	
	-- RELAZIONI

	`_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT 

);




-- relation m:m Sostanza Processo - Sostanza
CREATE TABLE IF NOT EXISTS `Processo_Sostanza` (
    `_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_Processo` int(11)  NOT NULL REFERENCES Processo(_id),
    `id_Sostanza` int(11)  NOT NULL REFERENCES Sostanza(_id)
);


--
-- Struttura della tabella `sostanza`
--

CREATE TABLE IF NOT EXISTS `sostanza` (
	`Identificativo` varchar(130) ,
	`Nome` varchar(130)  NOT NULL,
	`Score` decimal(6,2) ,
	`VLEP` bool  NOT NULL,
	
	-- RELAZIONI
	`User` int(11)  REFERENCES user(_id),

	`_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT 

);




-- relation m:m FrasiH Sostanza - FrasiH
CREATE TABLE IF NOT EXISTS `Sostanza_FrasiH` (
    `_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_Sostanza` int(11)  NOT NULL REFERENCES Sostanza(_id),
    `id_FrasiH` int(11)  NOT NULL REFERENCES FrasiH(_id)
);


--
-- Struttura della tabella `user`
--

CREATE TABLE IF NOT EXISTS `user` (
	`mail` varchar(130) ,
	`name` varchar(130) ,
	`password` varchar(130)  NOT NULL,
	`roles` varchar(130) ,
	`surname` varchar(130) ,
	`username` varchar(130)  NOT NULL,
	
	-- RELAZIONI

	`_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT 

);


-- Security

INSERT INTO `movarisch_db`.`user` (`username`, `password`, `_id`) VALUES ('admin', '62f264d7ad826f02a8af714c0a54b197935b717656b80461686d450f7b3abde4c553541515de2052b9af70f710f0cd8a1a2d3f4d60aa72608d71a63a9a93c0f5', 1);

CREATE TABLE IF NOT EXISTS `roles` (
	`role` varchar(30) ,
	
	-- RELAZIONI

	`_user` int(11)  NOT NULL REFERENCES user(_id),
	`_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT 

);
INSERT INTO `movarisch_db`.`roles` (`role`, `_user`, `_id`) VALUES ('ADMIN', '1', 1);






