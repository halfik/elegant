/*
Navicat PGSQL Data Transfer

Source Server         : localhost
Source Server Version : 90405
Source Host           : localhost:5432
Source Database       : core_test
Source Schema         : public

Target Server Type    : PGSQL
Target Server Version : 90200
File Encoding         : 65001

Date: 2016-12-19 16:47:14
*/


-- ----------------------------
-- Sequence structure for med_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "med_id_seq";
CREATE SEQUENCE "med_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for patient_data_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "patient_data_id_seq";
CREATE SEQUENCE "patient_data_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for patient_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "patient_id_seq";
CREATE SEQUENCE "patient_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for tu_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "tu_id_seq";
CREATE SEQUENCE "tu_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for user_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "user_id_seq";
CREATE SEQUENCE "user_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 5
 CACHE 1;
SELECT setval('"public"."user_id_seq"', 5, true);

-- ----------------------------
-- Table structure for med
-- ----------------------------
DROP TABLE IF EXISTS "med";
CREATE TABLE "med" (
"id" int4 DEFAULT nextval('med_id_seq'::regclass) NOT NULL,
"name" varchar(100) COLLATE "default" NOT NULL,
"city" varchar(100) COLLATE "default" NOT NULL,
"street" varchar(100) COLLATE "default" NOT NULL,
"zip_code" char(6) COLLATE "default" NOT NULL,
"nip" char(10) COLLATE "default" NOT NULL,
"regon" varchar(14) COLLATE "default" NOT NULL,
"krs" varchar(20) COLLATE "default" NOT NULL,
"spokesman" varchar(100) COLLATE "default" NOT NULL,
"phone" varchar(20) COLLATE "default" NOT NULL,
"cell_phone" varchar(20) COLLATE "default",
"email" varchar(150) COLLATE "default" NOT NULL,
"created_at" timestamp NOT NULL,
"updated_at" timestamp NOT NULL,
"deleted_at" timestamp
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Records of med
-- ----------------------------
BEGIN;
INSERT INTO "med" VALUES ('1', 'Med 1', 'Warsaw', 'Unknown 1', '00-111', '2743424750', '63985222839628', 'krs1', 'med 1 spokesman', '+48 600 10 10 10', null, 'med@med1.com', '2015-11-22 14:25:26', '2015-11-22 14:25:26', null);
INSERT INTO "med" VALUES ('2', 'Med 2', 'Warsaw', 'Unknown 2', '00-222', '1283954829', '01594320168108', 'krs2', 'med 2 spokesman', '+48 600 20 20 20', null, 'med@med2.com', '2015-11-22 14:25:26', '2015-11-22 14:25:26', null);
COMMIT;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS "migrations";
CREATE TABLE "migrations" (
"migration" varchar(255) COLLATE "default" NOT NULL,
"batch" int4 NOT NULL
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Records of migrations
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for patient
-- ----------------------------
DROP TABLE IF EXISTS "patient";
CREATE TABLE "patient" (
"id" int4 DEFAULT nextval('patient_id_seq'::regclass) NOT NULL,
"user__id" int4,
"pesel" char(11) COLLATE "default" NOT NULL,
"created_at" timestamp NOT NULL,
"updated_at" timestamp NOT NULL,
"deleted_at" timestamp
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Records of patient
-- ----------------------------
BEGIN;
INSERT INTO "patient" VALUES ('1', '1', '92091811263', '2015-11-22 14:25:26', '2015-11-22 14:25:26', null);
INSERT INTO "patient" VALUES ('2', '2', '30090416782', '2015-11-22 14:25:26', '2015-11-22 14:25:26', null);
COMMIT;

-- ----------------------------
-- Table structure for patient_data
-- ----------------------------
DROP TABLE IF EXISTS "patient_data";
CREATE TABLE "patient_data" (
"id" int4 DEFAULT nextval('patient_data_id_seq'::regclass) NOT NULL,
"patient__id" int4 NOT NULL,
"med__id" int4,
"tu__id" int4,
"first_name" varchar(100) COLLATE "default" NOT NULL,
"last_name" varchar(100) COLLATE "default" NOT NULL,
"birth_date" date NOT NULL,
"zip_code" char(6) COLLATE "default" NOT NULL,
"city" varchar(100) COLLATE "default" NOT NULL,
"street" varchar(100) COLLATE "default" NOT NULL,
"email" varchar(150) COLLATE "default",
"phone" varchar(20) COLLATE "default" NOT NULL,
"notes" text COLLATE "default",
"created_at" timestamp NOT NULL,
"updated_at" timestamp NOT NULL,
"deleted_at" timestamp
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Records of patient_data
-- ----------------------------
BEGIN;
INSERT INTO "patient_data" VALUES ('1', '1', '1', '1', 'John', 'First', '1970-02-12', '00-001', 'New York', 'First Street', 'one@patient.com', '501 00 00 00', null, '2015-11-22 14:25:26', '2015-11-22 14:25:26', null);
INSERT INTO "patient_data" VALUES ('2', '2', '2', '2', 'Adam', 'Second', '1975-05-22', '00-002', 'Moscow', 'Second Street', 'second@patient.com', '502 00 00 00', null, '2015-11-22 14:25:26', '2015-11-22 14:25:26', null);
INSERT INTO "patient_data" VALUES ('3', '2', '1', '2', 'Adam', 'Second', '1975-05-22', '00-002', 'Moscow', 'Second Street', 'second@patient.com', '502 20 00 00', null, '2015-11-22 14:25:26', '2015-11-22 14:25:26', null);
COMMIT;

-- ----------------------------
-- Table structure for tu
-- ----------------------------
DROP TABLE IF EXISTS "tu";
CREATE TABLE "tu" (
"id" int4 DEFAULT nextval('tu_id_seq'::regclass) NOT NULL,
"name" varchar(100) COLLATE "default" NOT NULL,
"city" varchar(100) COLLATE "default",
"zip_code" char(6) COLLATE "default",
"street" varchar(100) COLLATE "default",
"nip" char(10) COLLATE "default",
"regon" varchar(14) COLLATE "default",
"krs" varchar(20) COLLATE "default",
"phone" varchar(20) COLLATE "default",
"mobile" varchar(20) COLLATE "default",
"email" varchar(150) COLLATE "default",
"main_representative" varchar(150) COLLATE "default",
"created_at" timestamp NOT NULL,
"updated_at" timestamp NOT NULL,
"deleted_at" timestamp
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Records of tu
-- ----------------------------
BEGIN;
INSERT INTO "tu" VALUES ('1', 'Tu 1', 'Warsaw', '11-111', 'Tu Street 1', '8944895519', '29834542376272', 'tu krs 1', '+48 500 10 10 10', null, 'tu@tu.com', null, '2015-11-22 14:25:26', '2015-11-22 14:25:26', null);
INSERT INTO "tu" VALUES ('2', 'Tu 2', 'Berlin', '22-222', 'Unknown 2', '1484171040', '17263775889002', 'tu krs 2', '+48 500 20 20 20', null, 'tu@tu.com', null, '2015-11-22 14:25:26', '2015-11-22 14:25:26', null);
COMMIT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS "user";
CREATE TABLE "user" (
"id" int4 DEFAULT nextval('user_id_seq'::regclass) NOT NULL,
"login" varchar(255) COLLATE "default" NOT NULL,
"email" varchar(255) COLLATE "default" NOT NULL,
"password" varchar(255) COLLATE "default" NOT NULL,
"first_name" varchar(255) COLLATE "default",
"last_name" varchar(255) COLLATE "default",
"med__id" int4,
"tu__id" int4
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO "user" VALUES ('1', 'User 1', 'user1@hot.com', 'user1@hot.com', 'User', 'One', null, null);
INSERT INTO "user" VALUES ('2', 'User 2', 'user2@hot.com', 'user2@hot.com', 'User', 'Two', null, null);
INSERT INTO "user" VALUES ('3', 'User 3', 'user3@hot.com', 'user3@hot.com', 'User', 'Tree', null, null);
INSERT INTO "user" VALUES ('4', 'User 4', 'user4@hot.com', 'user4@hot.com', 'User', 'Four', null, '1');
INSERT INTO "user" VALUES ('5', 'User 5', 'user5@hot.com', 'user5@hot.com', 'User', 'Five', '1', null);
COMMIT;

-- ----------------------------
-- Alter Sequences Owned By 
-- ----------------------------
ALTER SEQUENCE "med_id_seq" OWNED BY "med"."id";
ALTER SEQUENCE "patient_data_id_seq" OWNED BY "patient_data"."id";
ALTER SEQUENCE "patient_id_seq" OWNED BY "patient"."id";
ALTER SEQUENCE "tu_id_seq" OWNED BY "tu"."id";
ALTER SEQUENCE "user_id_seq" OWNED BY "user"."id";

-- ----------------------------
-- Primary Key structure for table med
-- ----------------------------
ALTER TABLE "med" ADD PRIMARY KEY ("id");

-- ----------------------------
-- Uniques structure for table patient
-- ----------------------------
ALTER TABLE "patient" ADD UNIQUE ("pesel");

-- ----------------------------
-- Primary Key structure for table patient
-- ----------------------------
ALTER TABLE "patient" ADD PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table patient_data
-- ----------------------------
ALTER TABLE "patient_data" ADD PRIMARY KEY ("id", "patient__id");

-- ----------------------------
-- Primary Key structure for table tu
-- ----------------------------
ALTER TABLE "tu" ADD PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table user
-- ----------------------------
ALTER TABLE "user" ADD PRIMARY KEY ("id");
