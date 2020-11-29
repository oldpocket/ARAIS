BEGIN TRANSACTION;
DROP TABLE IF EXISTS "roles_routes";
CREATE TABLE IF NOT EXISTS "roles_routes" (
	"id"	INTEGER PRIMARY KEY AUTOINCREMENT,
	"routes_id"	INTEGER NOT NULL,
	"roles_id"	INTEGER NOT NULL,
	FOREIGN KEY("roles_id") REFERENCES "roles"("id"),
	FOREIGN KEY("routes_id") REFERENCES "routes"("id")
);
DROP TABLE IF EXISTS "users";
CREATE TABLE IF NOT EXISTS "users" (
	"id"	INTEGER PRIMARY KEY AUTOINCREMENT,
	"name"	STRING NOT NULL,
	"email"	STRING NOT NULL,
	"tokens_id"	TEXT,
	FOREIGN KEY("tokens_id") REFERENCES "tokens"("id")
);
DROP TABLE IF EXISTS "roles";
CREATE TABLE IF NOT EXISTS "roles" (
	"id"	INTEGER,
	"uid"	TEXT NOT NULL,
	"description"	TEXT,
	PRIMARY KEY("id")
);
DROP TABLE IF EXISTS "routes";
CREATE TABLE IF NOT EXISTS "routes" (
	"id"	INTEGER PRIMARY KEY AUTOINCREMENT,
	"uid"	TEXT NOT NULL UNIQUE,
	"description"	TEXT,
	"route"	TEXT NOT NULL,
	"verb"	TEXT NOT NULL
);
DROP TABLE IF EXISTS "data";
CREATE TABLE IF NOT EXISTS "data" (
	"id"	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	"value"	DOUBLE NOT NULL,
	"timestamp"	DATETIME NOT NULL,
	"sensors_id"	INTEGER NOT NULL,
	FOREIGN KEY("sensors_id") REFERENCES "sensors"("id")
);
DROP TABLE IF EXISTS "devices";
CREATE TABLE IF NOT EXISTS "devices" (
	"id"	INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE,
	"created"	DATETIME,
	"label"	STRING,
	"place"	STRING,
	"modified"	DATETIME,
	"tokens_id"	INTEGER,
	"uid"	STRING UNIQUE,
	"last_ip"	STRING(15),
	FOREIGN KEY("tokens_id") REFERENCES "tokens"("id")
);
DROP TABLE IF EXISTS "sensors";
CREATE TABLE IF NOT EXISTS "sensors" (
	"id"	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	"created"	DATETIME,
	"uid"	STRING NOT NULL,
	"label"	STRING,
	"devices_id"	INTEGER NOT NULL,
	"modified"	DATETIME,
	FOREIGN KEY("devices_id") REFERENCES "devices"("id")
);
DROP TABLE IF EXISTS "tokens";
CREATE TABLE IF NOT EXISTS "tokens" (
	"id"	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	"username"	STRING NOT NULL UNIQUE,
	"secret"	STRING NOT NULL,
	"password"	STRING NOT NULL,
	"roles_id"	INTEGER NOT NULL,
	FOREIGN KEY("roles_id") REFERENCES "roles"("id")
);
DROP TABLE IF EXISTS "log";
CREATE TABLE IF NOT EXISTS "log" (
	"id"	TEXT,
	"timestamp"	TEXT,
	"calling"	TEXT,
	"message"	TEXT
);
INSERT INTO "roles_routes" VALUES (2,4,2);
INSERT INTO "roles_routes" VALUES (3,5,2);
INSERT INTO "roles_routes" VALUES (5,6,1);
INSERT INTO "roles_routes" VALUES (6,7,1);
INSERT INTO "roles_routes" VALUES (7,8,2);
INSERT INTO "roles_routes" VALUES (8,9,1);
INSERT INTO "roles_routes" VALUES (9,9,2);
INSERT INTO "roles_routes" VALUES (10,10,1);
INSERT INTO "roles_routes" VALUES (11,11,1);
INSERT INTO "roles_routes" VALUES (12,12,1);
INSERT INTO "roles_routes" VALUES (13,12,2);
INSERT INTO "roles_routes" VALUES (14,13,1);
INSERT INTO "roles_routes" VALUES (15,14,1);
INSERT INTO "roles_routes" VALUES (16,15,1);
INSERT INTO "roles_routes" VALUES (17,15,2);
INSERT INTO "roles_routes" VALUES (18,16,1);
INSERT INTO "roles_routes" VALUES (19,16,2);
INSERT INTO "roles_routes" VALUES (20,17,2);
INSERT INTO "roles_routes" VALUES (21,18,2);
INSERT INTO "roles_routes" VALUES (22,19,2);
INSERT INTO "roles_routes" VALUES (23,20,2);
INSERT INTO "roles_routes" VALUES (24,21,2);
INSERT INTO "roles_routes" VALUES (26,22,2);
INSERT INTO "roles_routes" VALUES (27,23,2);
INSERT INTO "roles_routes" VALUES (28,24,2);
INSERT INTO "users" VALUES (4,'Admin User','admin@email.com',5);
INSERT INTO "users" VALUES (5,'Fabio Godoy','aethiopicus@gmail.com',29);
INSERT INTO "roles" VALUES (1,'device','Role for the devices');
INSERT INTO "roles" VALUES (2,'backoffice','Backoffice user - can add other users and devices in the system');
INSERT INTO "routes" VALUES (4,'deviceGet','Return device details','/^\/devices\/$/','GET');
INSERT INTO "routes" VALUES (5,'deviceDeviceUIDPost','Register a new device','/^\/devices\/(\w+)\/$/','POST');
INSERT INTO "routes" VALUES (6,'deviceDeviceUIDPut','Update an existing device','/^\/devices\/(\w+)\/$/','PUT');
INSERT INTO "routes" VALUES (7,'deviceDeviceUIDGet','Return device details','/^\/devices\/(\w+)\/$/','GET');
INSERT INTO "routes" VALUES (8,'deviceDeviceUIDDelete','Remove a device','/^\/devices\/(\w+)\/$/','DELETE');
INSERT INTO "routes" VALUES (9,'deviceDeviceUIDSensorsGet','Get data from a sensor','/^\/devices\/(\w+)\/sensors\/$/','GET');
INSERT INTO "routes" VALUES (10,'deviceDeviceUIDSensorsSensorUIDPost','Register a new sensor in an existing device','/^\/devices\/(\w+)\/sensors\/(\w+)\/$/','POST');
INSERT INTO "routes" VALUES (11,'deviceDeviceUIDSensorsSensorUIDPut','Update an existing sensor','/^\/devices\/(\w+)\/sensors\/(\w+)\/$/','PUT');
INSERT INTO "routes" VALUES (12,'deviceDeviceUIDSensorsSensorUIDGet','Return sensor details','/^\/devices\/(\w+)\/sensors\/(\w+)\/$/','GET');
INSERT INTO "routes" VALUES (13,'deviceDeviceUIDSensorsSensorUIDDelete','Remove a sensor','/^\/devices\/(\w+)\/sensors\/(\w+)\/$/','DELETE');
INSERT INTO "routes" VALUES (14,'deviceDeviceUIDSensorsSensorUIDDataPost','Add data to a sensor','/^\/devices\/(\w+)\/sensors\/(\w+)\/data$/','POST');
INSERT INTO "routes" VALUES (15,'deviceDeviceUIDSensorsSensorUIDDataGet','Get data from a sensor','/^\/devices\/(\w+)\/sensors\/(\w+)\/data$/','GET');
INSERT INTO "routes" VALUES (16,'deviceDeviceUIDSensorsSensorUIDDataDelete','Delete data within the timestamp','/^\/devices\/(\w+)\/sensors\/(\w+)\/data$/','DELETE');
INSERT INTO "routes" VALUES (17,'usersUsernameGet','Get a user from the system','/^\/users\/(\w+)$/','GET');
INSERT INTO "routes" VALUES (18,'authorizationRolesRoleUIDPost','Create a new role in the system','/^\/authorization\/roles\/(\w+)$/','POST');
INSERT INTO "routes" VALUES (19,'authorizationRoutesRouteUIDPost','Register a new route in the system','/^\/authorization\/routes\/(\w+)$/','POST');
INSERT INTO "routes" VALUES (20,'authorizationPermissionRoleUIDRouteUIDPost','Associate a route with a role','/^\/authorization\/permission\/(\w+)\/(\w+)$/','POST');
INSERT INTO "routes" VALUES (21,'usersGet','Get a list of users in the system','/^\/users$/','GET');
INSERT INTO "routes" VALUES (22,'usersUsernamePost','Create a new user in the system','/^\/users\/(\w+)$/','POST');
INSERT INTO "routes" VALUES (23,'usersUsernamePut','Update a user from the system','/^\/users\/(\w+)$/','PUT');
INSERT INTO "routes" VALUES (24,'usersUsernamePasswordPut','Update users password','/^\/users\/(\w+)\/password$/','PUT');
INSERT INTO "devices" VALUES (31,'2020-10-30 22:09:25','local arduino device','home office','2020-10-30 22:09:25',27,4030,'192.168.1.2');
INSERT INTO "tokens" VALUES (5,'admin','92V+Xw9o6tlr7A==','$2y$10$QHkVrff9iVds5ijT1lk1AeLAt5Kz/7j9rut.leskfeJHWnaVmvhxK',2);
INSERT INTO "tokens" VALUES (27,'DEVICE_4030','prnr4u4OBJCsQA==','$2y$10$19mL0LK5yRwQfq41X5lhIehAu8N0jYzln5ukZjrktWgPGIX5QJETe',1);
INSERT INTO "tokens" VALUES (29,'aethiopicus','Ps0kyYRsnZRPGQ==','$2y$10$ouzO1dAdln2gxo8EAipD2eowX68/qMEeWz8YnMBTBcodFNqOj0zk2',2);
COMMIT;
