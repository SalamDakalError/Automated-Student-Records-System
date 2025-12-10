/*
SQLyog Ultimate - MySQL GUI v8.2 
MySQL - 5.5.5-10.4.32-MariaDB : Database - school
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`school` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;

USE `school`;

/*Table structure for table `5_magsaysay` */

DROP TABLE IF EXISTS `5_magsaysay`;

CREATE TABLE `5_magsaysay` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `student_name` varchar(255) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `adviser_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `5_magsaysay` */

insert  into `5_magsaysay`(`id`,`student_name`,`gender`,`grade_level`,`section`,`adviser_name`) values (1,'Baluyot Frias Jan Xevier','Male','5','Magsaysay','Khian Kervy Mamaril'),(2,'Batislaon,Aeron Carreon','Male','5','Magsaysay','Khian Kervy Mamaril'),(3,'Bernardo Kurt Michael Riosa','Male','5','Magsaysay','Khian Kervy Mamaril'),(4,'Buan, Cyrus Arvee Giongco','Male','5','Magsaysay','Khian Kervy Mamaril'),(5,'Cunanan, Prince Carl','Male','5','Magsaysay','Khian Kervy Mamaril'),(6,'Goingco, Drake Calvin Bautista','Male','5','Magsaysay','Khian Kervy Mamaril'),(7,'Infante, James Mendez','Male','5','Magsaysay','Khian Kervy Mamaril'),(8,'Lacson John Red Hernandez','Male','5','Magsaysay','Khian Kervy Mamaril'),(9,'Limin,Jaslan Mangalindan','Male','5','Magsaysay','Khian Kervy Mamaril'),(10,'Lusung, Yuan Lein Rodriguez','Male','5','Magsaysay','Khian Kervy Mamaril'),(11,'Macaspac, Matteo Dho Arcilla','Male','5','Magsaysay','Khian Kervy Mamaril'),(12,'Miranda, Franz','Male','5','Magsaysay','Khian Kervy Mamaril'),(13,'Nayanggazohann,Cedy','Male','5','Magsaysay','Khian Kervy Mamaril'),(14,'Quiambao, Harvin  Miranda','Male','5','Magsaysay','Khian Kervy Mamaril'),(15,'Reyes Mhon Vincent Corpuz','Male','5','Magsaysay','Khian Kervy Mamaril'),(16,'Meneje, Gelo S.','Male','5','Magsaysay','Khian Kervy Mamaril'),(17,'Serrano, Aaron Kiel Dabu','Male','5','Magsaysay','Khian Kervy Mamaril'),(18,'Villamayon, Jasper Supang','Male','5','Magsaysay','Khian Kervy Mamaril'),(19,'Vistar, C-Jay Gomaga','Male','5','Magsaysay','Khian Kervy Mamaril'),(20,'Zapata, Xed Yuan Arcilla','Male','5','Magsaysay','Khian Kervy Mamaril'),(21,'Cardenio, John Henry','Male','5','Magsaysay','Khian Kervy Mamaril'),(22,'Azuelo,Jenn Mae Abenierra','Female','5','Magsaysay','Khian Kervy Mamaril'),(23,'Buan, Maria Dyniss','Female','5','Magsaysay','Khian Kervy Mamaril'),(24,'Bujala, Jaylovelyn Mabini','Female','5','Magsaysay','Khian Kervy Mamaril'),(25,'Canete  Nieran Alinsuas','Female','5','Magsaysay','Khian Kervy Mamaril'),(26,'Canlapan Mayline','Female','5','Magsaysay','Khian Kervy Mamaril'),(27,'Caoleng Simoune Gabrielle','Female','5','Magsaysay','Khian Kervy Mamaril'),(28,'De Leon Jannadhine Breboneria','Female','5','Magsaysay','Khian Kervy Mamaril'),(29,'Enriquez, Kyrie Haboc','Female','5','Magsaysay','Khian Kervy Mamaril'),(30,'Estimada, Keiko','Female','5','Magsaysay','Khian Kervy Mamaril'),(31,'Flores, Nicole Faith Icban','Female','5','Magsaysay','Khian Kervy Mamaril'),(32,'Gabiano, Zyree','Female','5','Magsaysay','Khian Kervy Mamaril'),(33,'Galula, Allyza Sophia','Female','5','Magsaysay','Khian Kervy Mamaril'),(34,'Garcia, Quennie Malungcut','Female','5','Magsaysay','Khian Kervy Mamaril'),(35,'Manalastas, Starr','Female','5','Magsaysay','Khian Kervy Mamaril'),(36,'Marangit, Sanifa Camaya','Female','5','Magsaysay','Khian Kervy Mamaril'),(37,'Mongcal, Mangcal Arcataka','Female','5','Magsaysay','Khian Kervy Mamaril'),(38,'Panganiban, Micah Jewel','Female','5','Magsaysay','Khian Kervy Mamaril'),(39,'Paras, Mician Rae Buntog','Female','5','Magsaysay','Khian Kervy Mamaril'),(40,'Petell, Brielle Ashley Maglonzo','Female','5','Magsaysay','Khian Kervy Mamaril'),(41,'Quito Ayesha','Female','5','Magsaysay','Khian Kervy Mamaril'),(42,'Raandaan Jonabeth Alicante','Female','5','Magsaysay','Khian Kervy Mamaril'),(43,'Serrano Misha Nunag','Female','5','Magsaysay','Khian Kervy Mamaril'),(44,'Sunga Quiyane Binayog','Female','5','Magsaysay','Khian Kervy Mamaril'),(45,'Villavicencio Francine Mac Castro','Female','5','Magsaysay','Khian Kervy Mamaril'),(46,'Zamora Angel','Female','5','Magsaysay','Khian Kervy Mamaril'),(47,'Lazaro Karen','Female','5','Magsaysay','Khian Kervy Mamaril');

/*Table structure for table `esp_5_magsaysay` */

DROP TABLE IF EXISTS `esp_5_magsaysay`;

CREATE TABLE `esp_5_magsaysay` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `student_name` varchar(255) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `q1_grade` decimal(5,2) DEFAULT NULL,
  `q2_grade` decimal(5,2) DEFAULT NULL,
  `q3_grade` decimal(5,2) DEFAULT NULL,
  `q4_grade` decimal(5,2) DEFAULT NULL,
  `final_grade` decimal(5,2) DEFAULT NULL,
  `teacher` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `esp_5_magsaysay` */

insert  into `esp_5_magsaysay`(`id`,`student_name`,`gender`,`q1_grade`,`q2_grade`,`q3_grade`,`q4_grade`,`final_grade`,`teacher`) values (1,'Baluyot Frias Jan Xevier','Male','0.00','0.00','0.00','10.88','90.00','Aron Diolata'),(2,'Batislaon,Aeron Carreon','Male','0.00','0.00','0.00','8.25','98.00','Aron Diolata'),(3,'Bernardo Kurt Michael Riosa','Male','0.00','0.00','0.00','8.25','0.00','Aron Diolata'),(4,'Buan, Cyrus Arvee Giongco','Male','0.00','0.00','0.00','6.38','8.00','Aron Diolata'),(5,'Cunanan, Prince Carl','Male','0.00','0.00','0.00','10.13','0.00','Aron Diolata'),(6,'Goingco, Drake Calvin Bautista','Male','0.00','0.00','0.00','12.75','6.00','Aron Diolata'),(7,'Infante, James Mendez','Male','0.00','0.00','0.00','6.00','0.00','Aron Diolata'),(8,'Lacson John Red Hernandez','Male','0.00','0.00','0.00','9.75','8.00','Aron Diolata'),(9,'Limin,Jaslan Mangalindan','Male','0.00','0.00','0.00','6.38','0.00','Aron Diolata'),(10,'Lusung, Yuan Lein Rodriguez','Male','0.00','0.00','0.00','12.75','9.00','Aron Diolata'),(11,'Macaspac, Matteo Dho Arcilla','Male','0.00','0.00','0.00','11.63','8.00','Aron Diolata'),(12,'Miranda, Franz','Male','0.00','0.00','0.00','9.38','8.00','Aron Diolata'),(13,'Nayanggazohann,Cedy','Male','0.00','0.00','0.00','11.25','8.00','Aron Diolata'),(14,'Quiambao, Harvin  Miranda','Male','0.00','0.00','0.00','13.13','10.00','Aron Diolata'),(15,'Reyes Mhon Vincent Corpuz','Male','0.00','0.00','0.00','11.63','10.00','Aron Diolata'),(16,'Meneje, Gelo S.','Male','0.00','0.00','0.00','8.63','9.00','Aron Diolata'),(17,'Serrano, Aaron Kiel Dabu','Male','0.00','0.00','0.00','12.75','10.00','Aron Diolata'),(18,'Villamayon, Jasper Supang','Male','0.00','0.00','0.00','3.38','0.00','Aron Diolata'),(19,'Vistar, C-Jay Gomaga','Male','0.00','0.00','0.00','6.38','10.00','Aron Diolata'),(20,'Zapata, Xed Yuan Arcilla','Male','0.00','0.00','0.00','12.75','9.00','Aron Diolata'),(21,'Cardenio, John Henry','Male','0.00','0.00','0.00','5.63','8.00','Aron Diolata'),(22,'Azuelo,Jenn Mae Abenierra','Female','0.00','0.00','0.00','2.25','0.00','Aron Diolata'),(23,'Buan, Maria Dyniss','Female','0.00','0.00','0.00','10.13','8.00','Aron Diolata'),(24,'Bujala, Jaylovelyn Mabini','Female','0.00','0.00','0.00','10.13','9.00','Aron Diolata'),(25,'Canete  Nieran Alinsuas','Female','0.00','0.00','0.00','12.38','10.00','Aron Diolata'),(26,'Canlapan Mayline','Female','0.00','0.00','0.00','6.00','0.00','Aron Diolata'),(27,'Caoleng Simoune Gabrielle','Female','0.00','0.00','0.00','11.25','9.00','Aron Diolata'),(28,'De Leon Jannadhine Breboneria','Female','0.00','0.00','0.00','7.50','8.00','Aron Diolata'),(29,'Enriquez, Kyrie Haboc','Female','0.00','0.00','0.00','10.13','0.00','Aron Diolata'),(30,'Estimada, Keiko','Female','0.00','0.00','0.00','12.75','10.00','Aron Diolata'),(31,'Flores, Nicole Faith Icban','Female','0.00','0.00','0.00','12.38','10.00','Aron Diolata'),(32,'Gabiano, Zyree','Female','0.00','0.00','0.00','10.88','8.00','Aron Diolata'),(33,'Galula, Allyza Sophia','Female','0.00','0.00','0.00','6.00','9.00','Aron Diolata'),(34,'Garcia, Quennie Malungcut','Female','0.00','0.00','0.00','10.50','9.00','Aron Diolata'),(35,'Manalastas, Starr','Female','0.00','0.00','0.00','11.63','0.00','Aron Diolata'),(36,'Marangit, Sanifa Camaya','Female','0.00','0.00','0.00','9.75','10.00','Aron Diolata'),(37,'Mongcal, Mangcal Arcataka','Female','0.00','0.00','0.00','10.88','10.00','Aron Diolata'),(38,'Panganiban, Micah Jewel','Female','0.00','0.00','0.00','13.13','10.00','Aron Diolata'),(39,'Paras, Mician Rae Buntog','Female','0.00','0.00','0.00','11.63','10.00','Aron Diolata'),(40,'Petell, Brielle Ashley Maglonzo','Female','0.00','0.00','0.00','13.13','10.00','Aron Diolata'),(41,'Quito Ayesha','Female','0.00','0.00','0.00','10.88','10.00','Aron Diolata'),(42,'Raandaan Jonabeth Alicante','Female','0.00','0.00','0.00','13.13','10.00','Aron Diolata'),(43,'Serrano Misha Nunag','Female','0.00','0.00','0.00','13.13','8.00','Aron Diolata'),(44,'Sunga Quiyane Binayog','Female','0.00','0.00','0.00','13.13','8.00','Aron Diolata'),(45,'Villavicencio Francine Mac Castro','Female','0.00','0.00','0.00','0.00','0.00','Aron Diolata'),(46,'Zamora Angel','Female','0.00','0.00','0.00','12.00','8.00','Aron Diolata'),(47,'Lazaro Karen','Female','0.00','0.00','0.00','13.13','10.00','Aron Diolata');

/*Table structure for table `esp_vi_venus` */

DROP TABLE IF EXISTS `esp_vi_venus`;

CREATE TABLE `esp_vi_venus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `student_name` varchar(255) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `q1_grade` decimal(5,2) DEFAULT NULL,
  `q2_grade` decimal(5,2) DEFAULT NULL,
  `q3_grade` decimal(5,2) DEFAULT NULL,
  `q4_grade` decimal(5,2) DEFAULT NULL,
  `final_grade` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `esp_vi_venus` */

insert  into `esp_vi_venus`(`id`,`student_name`,`gender`,`q1_grade`,`q2_grade`,`q3_grade`,`q4_grade`,`final_grade`) values (1,' ARCEO, MARCUS ANDREW B.','Male','84.00','84.00','0.00','0.00','0.00'),(2,' CARIŇO, TOEBY YHAEL ANGELES','Male','91.00','76.00','0.00','0.00','0.00'),(3,' DE GUZMAN , YUAN KYLER D.','Male','91.00','87.00','0.00','0.00','0.00'),(4,' DE LEON ERVIC MARQUITO','Male','87.00','87.00','0.00','0.00','0.00'),(5,' DELA CRUZ, GIANVER JEAN DOMINGGO','Male','77.00','87.00','0.00','0.00','0.00'),(6,' LOMANGGYA, JON DAVID P.','Male','93.00','87.00','0.00','0.00','0.00'),(7,' LULU , RHENZ RUZZEL G.','Male','89.00','65.00','0.00','0.00','0.00'),(8,' MACAPAGAL, LACSON JR. P.','Male','75.00','88.00','0.00','0.00','0.00'),(9,' ORLAIN, TERENCE JERIEL D.','Male','82.00','64.00','0.00','0.00','0.00'),(10,' QUIAMBAO, JOHN LOR DELA FUENTE','Male','86.00','43.00','0.00','0.00','0.00'),(11,' RASCO, SEAN ROWEL TAMAYO','Male','82.00','33.00','0.00','0.00','0.00'),(12,' RELABO , PRINCE RUTHZNEIL SHELLORD C.','Male','85.00','43.00','0.00','0.00','0.00'),(13,' SABONDO, KEYLETTE - TRANS IN','Male','82.00','43.00','0.00','0.00','0.00'),(14,' SAN JUAN, THOMAS GREGORY GONZALES','Male','80.00','656.00','0.00','0.00','0.00'),(15,' TAYAG , DENVER KIEL D.','Male','80.00','767.00','0.00','0.00','0.00'),(16,' TUNGOL JOHANNES LISCOG','Male','89.00','7.00','0.00','0.00','0.00'),(17,' TUNGOL, DANIEL MADDAWIN','Male','83.00','66.00','0.00','0.00','0.00'),(18,' YUTUC , MARC ANDREI A.','Male','77.00','34.00','0.00','0.00','0.00'),(19,'BALUYUT, JOHN ALBERT','Male','86.00','324.00','0.00','0.00','0.00'),(20,'CALBARRIO, PRINCE LIAM','Male','0.00','0.00','0.00','0.00','0.00'),(21,'AMANSEC , DARLENE P.','Female','92.00','234.00','0.00','0.00','0.00'),(22,'ANTONIO, JEWELKRIS G.','Female','82.00','999.99','0.00','0.00','0.00'),(23,'AQUILER, MARY JOY TINTE','Female','87.00','999.99','0.00','0.00','0.00'),(24,'CABRERA, ZYREN  MENDIOLA','Female','91.00','42.00','0.00','0.00','0.00'),(25,'CANQUE , PRINCESS MAE G.','Female','85.00','342.00','0.00','0.00','0.00'),(26,'CASTILLO, RYAZZEN GAJELAN','Female','85.00','43.00','0.00','0.00','0.00'),(27,'GARCIA, ALEXA MIONETTE M.','Female','87.00','24.00','0.00','0.00','0.00'),(28,'GONZALES, SKYLEE REGALA','Female','87.00','23.00','0.00','0.00','0.00'),(29,'HERMANO, RHEA ALEXA SUMICAO','Female','87.00','4.00','0.00','0.00','0.00'),(30,'MANGUERRA, COLYNE HUMPHREY','Female','91.00','234.00','0.00','0.00','0.00'),(31,'NICDAO, HENESSY CASTILLO','Female','83.00','23.00','0.00','0.00','0.00'),(32,'PATIAG, ASHLEY JOY B.','Female','84.00','42.00','0.00','0.00','0.00'),(33,'PUEBLAS, GERCELLA GINGOYON','Female','93.00','4.00','0.00','0.00','0.00'),(34,'ROQUE , JORLAN S.','Female','90.00','242.00','0.00','0.00','0.00'),(35,'SALUNGA , ANICA KLEIN CARREON','Female','93.00','4.00','0.00','0.00','0.00'),(36,'SARMIENTO , SHIAN LEE M.','Female','89.00','23.00','0.00','0.00','0.00'),(37,'SOTOR, AMBER ROSE','Female','93.00','423.00','0.00','0.00','0.00');

/*Table structure for table `gmrc_iv_rizal` */

DROP TABLE IF EXISTS `gmrc_iv_rizal`;

CREATE TABLE `gmrc_iv_rizal` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `student_name` varchar(255) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `q1_grade` decimal(5,2) DEFAULT NULL,
  `q2_grade` decimal(5,2) DEFAULT NULL,
  `q3_grade` decimal(5,2) DEFAULT NULL,
  `q4_grade` decimal(5,2) DEFAULT NULL,
  `final_grade` decimal(5,2) DEFAULT NULL,
  `teacher` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `gmrc_iv_rizal` */

insert  into `gmrc_iv_rizal`(`id`,`student_name`,`gender`,`q1_grade`,`q2_grade`,`q3_grade`,`q4_grade`,`final_grade`,`teacher`) values (1,'Bautisa, France Kiel Lacon','Male','0.00','0.00','0.00','6.32','13.00','Khian Kervy Mamaril'),(2,'Candelario, Nylan Esto','Male','0.00','0.00','0.00','0.00','15.00','Khian Kervy Mamaril'),(3,'Dalangin, Lorenz Salvador','Male','0.00','0.00','0.00','6.32','11.00','Khian Kervy Mamaril'),(4,'De Jesus, Benjamin Tyga Liscano','Male','0.00','0.00','0.00','0.00','13.00','Khian Kervy Mamaril'),(5,'Cortez, Xyron Credo','Male','0.00','0.00','0.00','3.16','15.00','Khian Kervy Mamaril'),(6,'Garong Shemuelle Jones L.','Male','0.00','0.00','0.00','3.16','15.00','Khian Kervy Mamaril'),(7,'Gonzales, Hadrian Consulta','Male','0.00','0.00','0.00','2.84','15.00','Khian Kervy Mamaril'),(8,'Mabana, Terrence Navarro','Male','0.00','0.00','0.00','0.00','0.00','Khian Kervy Mamaril'),(9,'Mangao, John Carlo Tarroza','Male','0.00','0.00','0.00','3.16','9.00','Khian Kervy Mamaril'),(10,'Manuel, Mathias Manzanes','Male','0.00','0.00','0.00','2.53','0.00','Khian Kervy Mamaril'),(11,'Pleno, James Andrei Dizon','Male','0.00','0.00','0.00','4.42','9.00','Khian Kervy Mamaril'),(12,'Reyes, Brionne Ysmael','Male','0.00','0.00','0.00','2.84','11.00','Khian Kervy Mamaril'),(13,'Rosales, John Mc Brandon Patubo','Male','0.00','0.00','0.00','5.69','15.00','Khian Kervy Mamaril'),(14,'Rosales, Jhon Vinvie Felicidad','Male','0.00','0.00','0.00','6.32','15.00','Khian Kervy Mamaril'),(15,'Tolentino, John Noel Cabrera','Male','0.00','0.00','0.00','5.37','15.00','Khian Kervy Mamaril'),(16,'Traya, John Romeo Villanueva','Male','0.00','0.00','0.00','6.32','9.00','Khian Kervy Mamaril'),(17,'Villafuerte, Ken Buan','Male','0.00','0.00','0.00','6.32','15.00','Khian Kervy Mamaril'),(18,'Pangilinan, David Dweyn','Male','0.00','0.00','0.00','1.58','0.00','Khian Kervy Mamaril'),(19,'Boiser, Janiella Dampil','Female','0.00','0.00','0.00','3.16','12.00','Khian Kervy Mamaril'),(20,'Buan, Zia Tolentino','Female','0.00','0.00','0.00','6.32','15.00','Khian Kervy Mamaril'),(21,'Cordon, Haven Joy Sevilla','Female','0.00','0.00','0.00','6.32','15.00','Khian Kervy Mamaril'),(22,'David, Hera Juralbal','Female','0.00','0.00','0.00','6.32','15.00','Khian Kervy Mamaril'),(23,'David, Zabrina Gabrielle Del Mundo','Female','0.00','0.00','0.00','3.16','15.00','Khian Kervy Mamaril'),(24,'De Guzman, Rhengel Arceo','Female','0.00','0.00','0.00','6.32','15.00','Khian Kervy Mamaril'),(25,'De Guzman, Zia Nicole Marcos','Female','0.00','0.00','0.00','6.32','15.00','Khian Kervy Mamaril'),(26,'Esguerra, Nathazia De Jesus','Female','0.00','0.00','0.00','6.32','15.00','Khian Kervy Mamaril'),(27,'Hernandez, Mckeena Taylor Martinez','Female','0.00','0.00','0.00','6.00','15.00','Khian Kervy Mamaril'),(28,'Ibasco, Clea Hailee Azarcon','Female','0.00','0.00','0.00','0.00','15.00','Khian Kervy Mamaril'),(29,'Ibay, Elise Margarette Nabong','Female','0.00','0.00','0.00','5.69','15.00','Khian Kervy Mamaril'),(30,'Jimenez, Nathalie','Female','0.00','0.00','0.00','6.32','15.00','Khian Kervy Mamaril'),(31,'Leyva, Princess Khalisse C.','Female','0.00','0.00','0.00','0.00','15.00','Khian Kervy Mamaril'),(32,'Macasulot, Maria Jandee Galopa','Female','0.00','0.00','0.00','6.00','15.00','Khian Kervy Mamaril'),(33,'Quito, Rhaze Brianna Pasion','Female','0.00','0.00','0.00','3.16','15.00','Khian Kervy Mamaril'),(34,'Quito, Samantha Klea Pearl Cayanan','Female','0.00','0.00','0.00','6.32','15.00','Khian Kervy Mamaril'),(35,'Rodrigo, Princess Yhna Cupay','Female','0.00','0.00','0.00','3.16','9.00','Khian Kervy Mamaril'),(36,'Turla, Keeona Sofia Bless','Female','0.00','0.00','0.00','0.00','15.00','Khian Kervy Mamaril'),(37,'Vidal, Princess Regine Merciales','Female','0.00','0.00','0.00','5.37','15.00','Khian Kervy Mamaril'),(38,'Vivas, Princess Apple Dungo','Female','0.00','0.00','0.00','6.32','14.00','Khian Kervy Mamaril');

/*Table structure for table `password_resets` */

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` double DEFAULT NULL,
  `code_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

/*Data for the table `password_resets` */

/*Table structure for table `sci_5_magsaysay` */

DROP TABLE IF EXISTS `sci_5_magsaysay`;

CREATE TABLE `sci_5_magsaysay` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `student_name` varchar(255) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `q1_grade` decimal(5,2) DEFAULT NULL,
  `q2_grade` decimal(5,2) DEFAULT NULL,
  `q3_grade` decimal(5,2) DEFAULT NULL,
  `q4_grade` decimal(5,2) DEFAULT NULL,
  `final_grade` decimal(5,2) DEFAULT NULL,
  `uploaded_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `sci_5_magsaysay` */

insert  into `sci_5_magsaysay`(`id`,`student_name`,`gender`,`q1_grade`,`q2_grade`,`q3_grade`,`q4_grade`,`final_grade`,`uploaded_by`) values (1,'Baluyot Frias Jan Xevier','Male','0.00','0.00','0.00','10.88','95.00','Khian Kervy Mamaril'),(2,'Batislaon,Aeron Carreon','Male','0.00','0.00','0.00','8.25','98.00','Khian Kervy Mamaril'),(3,'Bernardo Kurt Michael Riosa','Male','0.00','0.00','0.00','8.25','0.00','Khian Kervy Mamaril'),(4,'Buan, Cyrus Arvee Giongco','Male','0.00','0.00','0.00','6.38','8.00','Khian Kervy Mamaril'),(5,'Cunanan, Prince Carl','Male','0.00','0.00','0.00','10.13','0.00','Khian Kervy Mamaril'),(6,'Goingco, Drake Calvin Bautista','Male','0.00','0.00','0.00','12.75','6.00','Khian Kervy Mamaril'),(7,'Infante, James Mendez','Male','0.00','0.00','0.00','6.00','0.00','Khian Kervy Mamaril'),(8,'Lacson John Red Hernandez','Male','0.00','0.00','0.00','9.75','8.00','Khian Kervy Mamaril'),(9,'Limin,Jaslan Mangalindan','Male','0.00','0.00','0.00','6.38','0.00','Khian Kervy Mamaril'),(10,'Lusung, Yuan Lein Rodriguez','Male','0.00','0.00','0.00','12.75','9.00','Khian Kervy Mamaril'),(11,'Macaspac, Matteo Dho Arcilla','Male','0.00','0.00','0.00','11.63','8.00','Khian Kervy Mamaril'),(12,'Miranda, Franz','Male','0.00','0.00','0.00','9.38','8.00','Khian Kervy Mamaril'),(13,'Nayanggazohann,Cedy','Male','0.00','0.00','0.00','11.25','8.00','Khian Kervy Mamaril'),(14,'Quiambao, Harvin  Miranda','Male','0.00','0.00','0.00','13.13','10.00','Khian Kervy Mamaril'),(15,'Reyes Mhon Vincent Corpuz','Male','0.00','0.00','0.00','11.63','10.00','Khian Kervy Mamaril'),(16,'Meneje, Gelo S.','Male','0.00','0.00','0.00','8.63','9.00','Khian Kervy Mamaril'),(17,'Serrano, Aaron Kiel Dabu','Male','0.00','0.00','0.00','12.75','10.00','Khian Kervy Mamaril'),(18,'Villamayon, Jasper Supang','Male','0.00','0.00','0.00','3.38','0.00','Khian Kervy Mamaril'),(19,'Vistar, C-Jay Gomaga','Male','0.00','0.00','0.00','6.38','10.00','Khian Kervy Mamaril'),(20,'Zapata, Xed Yuan Arcilla','Male','0.00','0.00','0.00','12.75','9.00','Khian Kervy Mamaril'),(21,'Cardenio, John Henry','Male','0.00','0.00','0.00','5.63','8.00','Khian Kervy Mamaril'),(22,'Azuelo,Jenn Mae Abenierra','Female','0.00','0.00','0.00','2.25','0.00','Khian Kervy Mamaril'),(23,'Buan, Maria Dyniss','Female','0.00','0.00','0.00','10.13','8.00','Khian Kervy Mamaril'),(24,'Bujala, Jaylovelyn Mabini','Female','0.00','0.00','0.00','10.13','9.00','Khian Kervy Mamaril'),(25,'Canete  Nieran Alinsuas','Female','0.00','0.00','0.00','12.38','10.00','Khian Kervy Mamaril'),(26,'Canlapan Mayline','Female','0.00','0.00','0.00','6.00','0.00','Khian Kervy Mamaril'),(27,'Caoleng Simoune Gabrielle','Female','0.00','0.00','0.00','11.25','9.00','Khian Kervy Mamaril'),(28,'De Leon Jannadhine Breboneria','Female','0.00','0.00','0.00','7.50','8.00','Khian Kervy Mamaril'),(29,'Enriquez, Kyrie Haboc','Female','0.00','0.00','0.00','10.13','0.00','Khian Kervy Mamaril'),(30,'Estimada, Keiko','Female','0.00','0.00','0.00','12.75','10.00','Khian Kervy Mamaril'),(31,'Flores, Nicole Faith Icban','Female','0.00','0.00','0.00','12.38','10.00','Khian Kervy Mamaril'),(32,'Gabiano, Zyree','Female','0.00','0.00','0.00','10.88','8.00','Khian Kervy Mamaril'),(33,'Galula, Allyza Sophia','Female','0.00','0.00','0.00','6.00','9.00','Khian Kervy Mamaril'),(34,'Garcia, Quennie Malungcut','Female','0.00','0.00','0.00','10.50','9.00','Khian Kervy Mamaril'),(35,'Manalastas, Starr','Female','0.00','0.00','0.00','11.63','0.00','Khian Kervy Mamaril'),(36,'Marangit, Sanifa Camaya','Female','0.00','0.00','0.00','9.75','10.00','Khian Kervy Mamaril'),(37,'Mongcal, Mangcal Arcataka','Female','0.00','0.00','0.00','10.88','10.00','Khian Kervy Mamaril'),(38,'Panganiban, Micah Jewel','Female','0.00','0.00','0.00','13.13','10.00','Khian Kervy Mamaril'),(39,'Paras, Mician Rae Buntog','Female','0.00','0.00','0.00','11.63','10.00','Khian Kervy Mamaril'),(40,'Petell, Brielle Ashley Maglonzo','Female','0.00','0.00','0.00','13.13','10.00','Khian Kervy Mamaril'),(41,'Quito Ayesha','Female','0.00','0.00','0.00','10.88','10.00','Khian Kervy Mamaril'),(42,'Raandaan Jonabeth Alicante','Female','0.00','0.00','0.00','13.13','10.00','Khian Kervy Mamaril'),(43,'Serrano Misha Nunag','Female','0.00','0.00','0.00','13.13','8.00','Khian Kervy Mamaril'),(44,'Sunga Quiyane Binayog','Female','0.00','0.00','0.00','13.13','8.00','Khian Kervy Mamaril'),(45,'Villavicencio Francine Mac Castro','Female','0.00','0.00','0.00','0.00','0.00','Khian Kervy Mamaril'),(46,'Zamora Angel','Female','0.00','0.00','0.00','12.00','8.00','Khian Kervy Mamaril'),(47,'Lazaro Karen','Female','0.00','0.00','0.00','13.13','10.00','Khian Kervy Mamaril');

/*Table structure for table `teacher_files` */

DROP TABLE IF EXISTS `teacher_files`;

CREATE TABLE `teacher_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_name` varchar(765) DEFAULT NULL,
  `subject` varchar(765) DEFAULT NULL,
  `grade_section` varchar(150) DEFAULT NULL,
  `file_name` varchar(765) DEFAULT NULL,
  `file_path` varchar(765) DEFAULT NULL,
  `status` varchar(24) DEFAULT NULL,
  `submitted_date` datetime DEFAULT NULL,
  `approve_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `teacher_files` */

insert  into `teacher_files`(`id`,`teacher_name`,`subject`,`grade_section`,`file_name`,`file_path`,`status`,`submitted_date`,`approve_date`,`created_at`) values (21,'Khian Kervy Mamaril','ESP','5 - Magsaysay','ESP-5-Magsaysay.xlsx','uploads/teacher_files/ESP-5-Magsaysay.xlsx','approved','2025-12-07 14:14:05','2025-12-07 14:14:57','2025-12-07 14:14:05'),(22,'Khian Kervy Mamaril','SCI','5 - Magsaysay','SCI-5-Magsaysay.xlsx','uploads/teacher_files/SCI-5-Magsaysay.xlsx','approved','2025-12-07 14:14:14','2025-12-07 14:14:41','2025-12-07 14:14:14'),(23,'Khian Kervy Mamaril','ESP','5 - Magsaysay','ESP-5-Magsaysay.xlsx','uploads/teacher_files/ESP-5-Magsaysay.xlsx','approved','2025-12-07 14:17:42','2025-12-07 14:17:53','2025-12-07 14:17:42'),(24,'Aron Diolata','ESP','5 - Magsaysay','ESP-5-Magsaysay.xlsx','uploads/teacher_files/ESP-5-Magsaysay.xlsx','approved','2025-12-07 15:09:49','2025-12-08 15:47:35','2025-12-07 15:09:49'),(25,'Aron Diolata','ESP','5 - Magsaysay','ESP-5-Magsaysay.xlsx','uploads/teacher_files/ESP-5-Magsaysay.xlsx','approved','2025-12-07 15:27:53','2025-12-08 15:39:51','2025-12-07 15:27:53'),(26,'Khian Kervy Mamaril','ESP','5 - Magsaysay','ESP-5-Magsaysay.xlsx','uploads/teacher_files/ESP-5-Magsaysay.xlsx','approved','2025-12-08 15:39:05','2025-12-08 15:39:40','2025-12-08 15:39:05'),(27,'Khian Kervy Mamaril','GMRC','IV - Rizal','GMRC-IV-Rizal.xlsx','uploads/teacher_files/GMRC-IV-Rizal.xlsx','approved','2025-12-08 16:08:39','2025-12-10 12:00:33','2025-12-08 16:08:39');

/*Table structure for table `user_tokens` */

DROP TABLE IF EXISTS `user_tokens`;

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` double DEFAULT NULL,
  `selector` varchar(32) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `type` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

/*Data for the table `user_tokens` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` double DEFAULT NULL,
  `name` varchar(765) DEFAULT NULL,
  `email` varchar(2295) DEFAULT NULL,
  `password_hash` varchar(2295) DEFAULT NULL,
  `role` varchar(450) DEFAULT NULL,
  `advisory` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`password_hash`,`role`,`advisory`) values (1,'Khian Kervy Mamaril','asinwalapa@gmail.com','$2y$10$ks046Q3Rr3wXUqYL.W56eurAk6WJ5JJlP9dj5fh.GtRyHlOz50IH6','adviser','5-Magsaysay'),(2,'Aron Diolata','jeremiediolata01@gmail.com','$2y$10$lWGIRSxmBmPnyoWBI6Dxi.jrApElTnohCP640qbNIMon3PoXGcQM.','teacher',NULL),(3,'Bea Jane Baroa','mamadeerpoki@gmail.com','$2y$10$ks046Q3Rr3wXUqYL.W56eurAk6WJ5JJlP9dj5fh.GtRyHlOz50IH6','principal',NULL),(4,'Manarang John Paul','johnpaulmanarang07@gmail.com','$2y$10$Y/SWnXWKvNevi2bJUJRn9.LChLyWd9mThZFfzDdwZwqbWvJ3c3rhq','admin',NULL),(5,'Ralph Antonio Cruz','adviser2@gmail.com','$2y$10$ks046Q3Rr3wXUqYL.W56eurAk6WJ5JJlP9dj5fh.GtRyHlOz50IH6','adviser',NULL),(6,'Jessica Mae Lopez','teacher2@gmail.com','$2y$10$ks046Q3Rr3wXUqYL.W56eurAk6WJ5JJlP9dj5fh.GtRyHlOz50IH6','teacher',NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
