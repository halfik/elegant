/*
Navicat PGSQL Data Transfer

Source Server         : localhost
Source Server Version : 90311
Source Host           : localhost:5432
Source Database       : core_test
Source Schema         : public

Target Server Type    : PGSQL
Target Server Version : 90311
File Encoding         : 65001

Date: 2016-03-15 11:06:22
*/


-- ----------------------------
-- Sequence structure for med_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."med_id_seq";
CREATE SEQUENCE "public"."med_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 2
 CACHE 1;
SELECT setval('"public"."med_id_seq"', 2, true);

-- ----------------------------
-- Sequence structure for med_personnel__med_sience_degree_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."med_personnel__med_sience_degree_id_seq";
CREATE SEQUENCE "public"."med_personnel__med_sience_degree_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for med_personnel_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."med_personnel_id_seq";
CREATE SEQUENCE "public"."med_personnel_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 2
 CACHE 1;
SELECT setval('"public"."med_personnel_id_seq"', 2, true);

-- ----------------------------
-- Sequence structure for med_personnel_lang_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."med_personnel_lang_id_seq";
CREATE SEQUENCE "public"."med_personnel_lang_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 9472
 CACHE 1;
SELECT setval('"public"."med_personnel_lang_id_seq"', 9472, true);

-- ----------------------------
-- Sequence structure for med_science_degree_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."med_science_degree_id_seq";
CREATE SEQUENCE "public"."med_science_degree_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 3
 CACHE 1;
SELECT setval('"public"."med_science_degree_id_seq"', 3, true);

-- ----------------------------
-- Sequence structure for patient_data_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."patient_data_id_seq";
CREATE SEQUENCE "public"."patient_data_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 4
 CACHE 1;
SELECT setval('"public"."patient_data_id_seq"', 4, true);

-- ----------------------------
-- Sequence structure for patient_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."patient_id_seq";
CREATE SEQUENCE "public"."patient_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 2
 CACHE 1;
SELECT setval('"public"."patient_id_seq"', 2, true);

-- ----------------------------
-- Sequence structure for tu_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tu_id_seq";
CREATE SEQUENCE "public"."tu_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 2
 CACHE 1;
SELECT setval('"public"."tu_id_seq"', 2, true);

-- ----------------------------
-- Sequence structure for user_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."user_id_seq";
CREATE SEQUENCE "public"."user_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 5
 CACHE 1;
SELECT setval('"public"."user_id_seq"', 5, true);

-- ----------------------------
-- Table structure for med
-- ----------------------------
DROP TABLE IF EXISTS "public"."med";
CREATE TABLE "public"."med" (
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
INSERT INTO "public"."med" VALUES ('1', 'Med 1', 'Warsaw', 'Unknown 1', '00-111', '2743424750', '63985222839628', 'krs1', 'med 1 spokesman', '+48 600 10 10 10', null, 'med@med1.com', '2016-03-15 10:02:55', '2016-03-15 10:02:55', null);
INSERT INTO "public"."med" VALUES ('2', 'Med 2', 'Warsaw', 'Unknown 2', '00-222', '1283954829', '01594320168108', 'krs2', 'med 2 spokesman', '+48 600 20 20 20', null, 'med@med2.com', '2016-03-15 10:02:55', '2016-03-15 10:02:55', null);

-- ----------------------------
-- Table structure for med_personnel
-- ----------------------------
DROP TABLE IF EXISTS "public"."med_personnel";
CREATE TABLE "public"."med_personnel" (
"id" int4 DEFAULT nextval('med_personnel_id_seq'::regclass) NOT NULL,
"user__id" int4 NOT NULL,
"med__id" int4 NOT NULL,
"first_name" varchar(50) COLLATE "default" NOT NULL,
"last_name" varchar(100) COLLATE "default" NOT NULL
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Records of med_personnel
-- ----------------------------
INSERT INTO "public"."med_personnel" VALUES ('1', '5', '1', 'Greg', 'Johnson');
INSERT INTO "public"."med_personnel" VALUES ('2', '3', '2', 'Adam', 'Johnson');

-- ----------------------------
-- Table structure for med_personnel__med_sience_degree
-- ----------------------------
DROP TABLE IF EXISTS "public"."med_personnel__med_sience_degree";
CREATE TABLE "public"."med_personnel__med_sience_degree" (
"id" int4 DEFAULT nextval('med_personnel__med_sience_degree_id_seq'::regclass) NOT NULL,
"med_personnel__id" int4 NOT NULL,
"med_sience_degree__id" int4 NOT NULL
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Records of med_personnel__med_sience_degree
-- ----------------------------

-- ----------------------------
-- Table structure for med_science_degree
-- ----------------------------
DROP TABLE IF EXISTS "public"."med_science_degree";
CREATE TABLE "public"."med_science_degree" (
"id" int4 DEFAULT nextval('med_science_degree_id_seq'::regclass) NOT NULL,
"name" varchar(250) COLLATE "default" NOT NULL
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Records of med_science_degree
-- ----------------------------
INSERT INTO "public"."med_science_degree" VALUES ('1', 'degree 1');
INSERT INTO "public"."med_science_degree" VALUES ('2', 'degree 2');
INSERT INTO "public"."med_science_degree" VALUES ('3', 'degree 3');

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS "public"."migrations";
CREATE TABLE "public"."migrations" (
"migration" varchar(255) COLLATE "default" NOT NULL,
"batch" int4 NOT NULL
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Records of migrations
-- ----------------------------

-- ----------------------------
-- Table structure for patient
-- ----------------------------
DROP TABLE IF EXISTS "public"."patient";
CREATE TABLE "public"."patient" (
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
INSERT INTO "public"."patient" VALUES ('1', '1', '92091811263', '2016-03-15 10:02:55', '2016-03-15 10:02:55', null);
INSERT INTO "public"."patient" VALUES ('2', '2', '30090416782', '2016-03-15 10:02:55', '2016-03-15 10:02:55', null);

-- ----------------------------
-- Table structure for patient_data
-- ----------------------------
DROP TABLE IF EXISTS "public"."patient_data";
CREATE TABLE "public"."patient_data" (
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
INSERT INTO "public"."patient_data" VALUES ('1', '1', '1', '1', 'John', 'First', '1970-02-12', '00-001', 'New York', 'First Street', 'one@patient.com', '501 00 00 00', null, '2016-03-15 10:02:55', '2016-03-15 10:02:55', null);
INSERT INTO "public"."patient_data" VALUES ('2', '2', '2', '2', 'Adam', 'Second', '1975-05-22', '00-002', 'Moscow', 'Second Street', 'second@patient.com', '502 00 00 00', null, '2016-03-15 10:02:55', '2016-03-15 10:02:55', null);
INSERT INTO "public"."patient_data" VALUES ('3', '2', '1', '2', 'Adam', 'Second', '1975-05-22', '00-002', 'Moscow', 'Second Street', 'second@patient.com', '502 20 00 00', null, '2016-03-15 10:02:55', '2016-03-15 10:02:55', null);

-- ----------------------------
-- Table structure for tu
-- ----------------------------
DROP TABLE IF EXISTS "public"."tu";
CREATE TABLE "public"."tu" (
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
INSERT INTO "public"."tu" VALUES ('1', 'Tu 1', 'Warsaw', '11-111', 'Tu Street 1', '8944895519', '29834542376272', 'tu krs 1', '+48 500 10 10 10', null, 'tu@tu.com', null, '2016-03-15 10:02:55', '2016-03-15 10:02:55', null);
INSERT INTO "public"."tu" VALUES ('2', 'Tu 2', 'Berlin', '22-222', 'Unknown 2', '1484171040', '17263775889002', 'tu krs 2', '+48 500 20 20 20', null, 'tu@tu.com', null, '2016-03-15 10:02:55', '2016-03-15 10:02:55', null);

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS "public"."user";
CREATE TABLE "public"."user" (
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
INSERT INTO "public"."user" VALUES ('1', 'User 1', 'user1@hot.com', 'user1@hot.com', 'User', 'One', null, null);
INSERT INTO "public"."user" VALUES ('2', 'User 2', 'user2@hot.com', 'user2@hot.com', 'User', 'Two', null, null);
INSERT INTO "public"."user" VALUES ('3', 'User 3', 'user3@hot.com', 'user3@hot.com', 'User', 'Tree', null, null);
INSERT INTO "public"."user" VALUES ('4', 'User 4', 'user4@hot.com', 'user4@hot.com', 'User', 'Four', null, '1');
INSERT INTO "public"."user" VALUES ('5', 'User 5', 'user5@hot.com', 'user5@hot.com', 'User', 'Five', '1', null);

-- ----------------------------
-- Alter Sequences Owned By 
-- ----------------------------
ALTER SEQUENCE "public"."med_id_seq" OWNED BY "med"."id";
ALTER SEQUENCE "public"."med_personnel__med_sience_degree_id_seq" OWNED BY "med_personnel__med_sience_degree"."id";
ALTER SEQUENCE "public"."med_personnel_id_seq" OWNED BY "med_personnel"."id";
ALTER SEQUENCE "public"."med_science_degree_id_seq" OWNED BY "med_science_degree"."id";
ALTER SEQUENCE "public"."patient_data_id_seq" OWNED BY "patient_data"."id";
ALTER SEQUENCE "public"."patient_id_seq" OWNED BY "patient"."id";
ALTER SEQUENCE "public"."tu_id_seq" OWNED BY "tu"."id";
ALTER SEQUENCE "public"."user_id_seq" OWNED BY "user"."id";

-- ----------------------------
-- Primary Key structure for table med
-- ----------------------------
ALTER TABLE "public"."med" ADD PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table med_personnel
-- ----------------------------
ALTER TABLE "public"."med_personnel" ADD PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table med_personnel__med_sience_degree
-- ----------------------------
ALTER TABLE "public"."med_personnel__med_sience_degree" ADD PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table med_science_degree
-- ----------------------------
ALTER TABLE "public"."med_science_degree" ADD PRIMARY KEY ("id");

-- ----------------------------
-- Uniques structure for table patient
-- ----------------------------
ALTER TABLE "public"."patient" ADD UNIQUE ("pesel");

-- ----------------------------
-- Primary Key structure for table patient
-- ----------------------------
ALTER TABLE "public"."patient" ADD PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table patient_data
-- ----------------------------
ALTER TABLE "public"."patient_data" ADD PRIMARY KEY ("id", "patient__id");

-- ----------------------------
-- Primary Key structure for table tu
-- ----------------------------
ALTER TABLE "public"."tu" ADD PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table user
-- ----------------------------
ALTER TABLE "public"."user" ADD PRIMARY KEY ("id");
