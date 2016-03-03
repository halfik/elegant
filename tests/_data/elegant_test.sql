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

Date: 2016-03-03 12:40:59
*/


-- ----------------------------
-- Sequence structure for med_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."med_id_seq";
CREATE SEQUENCE "public"."med_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for med_personnel__med_sience_degree_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."med_personnel__med_sience_degree_id_seq";
CREATE SEQUENCE "public"."med_personnel__med_sience_degree_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 6288
 CACHE 1;
SELECT setval('"public"."med_personnel__med_sience_degree_id_seq"', 6288, true);

-- ----------------------------
-- Sequence structure for med_personnel_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."med_personnel_id_seq";
CREATE SEQUENCE "public"."med_personnel_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 6290
 CACHE 1;
SELECT setval('"public"."med_personnel_id_seq"', 6290, true);

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
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for patient_data_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."patient_data_id_seq";
CREATE SEQUENCE "public"."patient_data_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for patient_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."patient_id_seq";
CREATE SEQUENCE "public"."patient_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for tu_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."tu_id_seq";
CREATE SEQUENCE "public"."tu_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for user_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."user_id_seq";
CREATE SEQUENCE "public"."user_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 20
 CACHE 1;
SELECT setval('"public"."user_id_seq"', 20, true);

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
-- Table structure for med_personnel
-- ----------------------------
DROP TABLE IF EXISTS "public"."med_personnel";
CREATE TABLE "public"."med_personnel" (
"id" int4 DEFAULT nextval('med_personnel_id_seq'::regclass) NOT NULL,
"user__id" int4,
"med__id" int4 NOT NULL,
"first_name" varchar(50) COLLATE "default" NOT NULL,
"last_name" varchar(100) COLLATE "default" NOT NULL
)
WITH (OIDS=FALSE)

;

-- ----------------------------
-- Table structure for med_personnel__med_sience_degree
-- ----------------------------
DROP TABLE IF EXISTS "public"."med_personnel__med_sience_degree";
CREATE TABLE "public"."med_personnel__med_sience_degree" (
"id" int4 DEFAULT nextval('med_personnel__med_sience_degree_id_seq'::regclass) NOT NULL,
"med_personnel__id" int4 NOT NULL,
"med_sience_degree__id" int2 NOT NULL
)
WITH (OIDS=FALSE)

;

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
