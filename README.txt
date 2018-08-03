In development, Apache 2.4.9, MySQL server 14.14 and PHP 7.2.2-1 were used. Apache must have the PHP extension installed and enabled.

The .php files contained within this folder should all be in the web server's document root.

The web server should be set up to only use HTTPS, though for testing purposes ONLY it is not required

The admin user for the website has username 'admin' and default password 'test'.

The MySQL server should have a user called ReservationUser and default password testPass (for testing purposes)
which should be able to create, update, select, insert and delete.

The following SQL commands were used to set up the database:

CREATE DATABASE ReservationSystem;

USE ReservationSystem;

CREATE TABLE Reservations (
	ReservationID int NOT NULL AUTO_INCREMENT,
	Date DATE NOT NULL,
	Start_time TIME NOT NULL,
	End_time TIME NOT NULL,
	Surname VARCHAR(255),
	Forename VARCHAR(255),
	Email VARCHAR(255),
	Tel_No VARCHAR(11),
	Reservation_Type ENUM('internal','external') NOT NULL,
	Notes TEXT, UNIQUE (ReservationID),
	PRIMARY KEY (ReservationID)
);

CREATE TABLE Users (
	id int NOT NULL AUTO_INCREMENT,
	username VARCHAR(20) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	PRIMARY KEY (id)
); 
