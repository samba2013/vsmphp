--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.18
-- Dumped by pg_dump version 9.6.18

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE admin.users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE admin.cloud_config ALTER COLUMN id DROP DEFAULT;
ALTER TABLE admin.cloud ALTER COLUMN id DROP DEFAULT;
ALTER TABLE admin.accounts ALTER COLUMN id DROP DEFAULT;
ALTER TABLE admin.account_type ALTER COLUMN id DROP DEFAULT;
DROP SEQUENCE admin.users_id_seq;
DROP TABLE admin.users;
DROP SEQUENCE admin.cloud_id_seq;
DROP SEQUENCE admin.cloud_config_id_seq;
DROP TABLE admin.cloud_config;
DROP TABLE admin.cloud;
DROP SEQUENCE admin.accounts_id_seq;
DROP TABLE admin.accounts;
DROP SEQUENCE admin.account_type_id_seq;
DROP TABLE admin.account_type;
DROP EXTENSION plpgsql;
DROP SCHEMA public;
DROP SCHEMA admin;
--
-- Name: admin; Type: SCHEMA; Schema: -; Owner: admin
--

CREATE SCHEMA admin;


ALTER SCHEMA admin OWNER TO admin;

--
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: account_type; Type: TABLE; Schema: admin; Owner: admin
--

CREATE TABLE admin.account_type (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    maxinstances integer NOT NULL,
    is_active integer DEFAULT 1,
    created_by integer NOT NULL,
    modified_by integer,
    created_at timestamp with time zone NOT NULL,
    modified_at timestamp with time zone
);


ALTER TABLE admin.account_type OWNER TO admin;

--
-- Name: account_type_id_seq; Type: SEQUENCE; Schema: admin; Owner: admin
--

CREATE SEQUENCE admin.account_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE admin.account_type_id_seq OWNER TO admin;

--
-- Name: account_type_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: admin
--

ALTER SEQUENCE admin.account_type_id_seq OWNED BY admin.account_type.id;


--
-- Name: accounts; Type: TABLE; Schema: admin; Owner: admin
--

CREATE TABLE admin.accounts (
    id integer NOT NULL,
    account_type_id integer NOT NULL,
    name character varying(100) NOT NULL,
    token character varying(100) NOT NULL,
    is_active integer DEFAULT 1,
    created_by integer NOT NULL,
    modified_by integer,
    created_at timestamp with time zone NOT NULL,
    modified_at timestamp with time zone
);


ALTER TABLE admin.accounts OWNER TO admin;

--
-- Name: accounts_id_seq; Type: SEQUENCE; Schema: admin; Owner: admin
--

CREATE SEQUENCE admin.accounts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE admin.accounts_id_seq OWNER TO admin;

--
-- Name: accounts_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: admin
--

ALTER SEQUENCE admin.accounts_id_seq OWNED BY admin.accounts.id;


--
-- Name: cloud; Type: TABLE; Schema: admin; Owner: admin
--

CREATE TABLE admin.cloud (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    username character varying(100) NOT NULL,
    password character varying(100) NOT NULL,
    status character varying(100) NOT NULL,
    account_id integer NOT NULL,
    cloud_config_id integer NOT NULL,
    sshport integer DEFAULT 22,
    is_active integer DEFAULT 1,
    created_by integer NOT NULL,
    modified_by integer,
    created_at timestamp with time zone NOT NULL,
    modified_at timestamp with time zone
);


ALTER TABLE admin.cloud OWNER TO admin;

--
-- Name: cloud_config; Type: TABLE; Schema: admin; Owner: admin
--

CREATE TABLE admin.cloud_config (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    memory_size integer NOT NULL,
    storage_size integer NOT NULL,
    location character varying(100) NOT NULL,
    domain character varying(100) NOT NULL,
    tag character varying(100) NOT NULL,
    bandwith character varying(100) NOT NULL,
    is_active integer DEFAULT 1,
    created_by integer NOT NULL,
    modified_by integer,
    created_at timestamp with time zone NOT NULL,
    modified_at timestamp with time zone
);


ALTER TABLE admin.cloud_config OWNER TO admin;

--
-- Name: cloud_config_id_seq; Type: SEQUENCE; Schema: admin; Owner: admin
--

CREATE SEQUENCE admin.cloud_config_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE admin.cloud_config_id_seq OWNER TO admin;

--
-- Name: cloud_config_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: admin
--

ALTER SEQUENCE admin.cloud_config_id_seq OWNED BY admin.cloud_config.id;


--
-- Name: cloud_id_seq; Type: SEQUENCE; Schema: admin; Owner: admin
--

CREATE SEQUENCE admin.cloud_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE admin.cloud_id_seq OWNER TO admin;

--
-- Name: cloud_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: admin
--

ALTER SEQUENCE admin.cloud_id_seq OWNED BY admin.cloud.id;


--
-- Name: users; Type: TABLE; Schema: admin; Owner: admin
--

CREATE TABLE admin.users (
    id integer NOT NULL,
    username character varying(50) NOT NULL,
    firstname character varying(100) NOT NULL,
    lastname character varying(100) NOT NULL,
    password character varying(100) NOT NULL,
    created_by integer NOT NULL,
    modified_by integer,
    created_at timestamp with time zone NOT NULL,
    modified_at timestamp with time zone,
    is_active integer DEFAULT 1,
    role_id integer NOT NULL
);


ALTER TABLE admin.users OWNER TO admin;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: admin; Owner: admin
--

CREATE SEQUENCE admin.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE admin.users_id_seq OWNER TO admin;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: admin
--

ALTER SEQUENCE admin.users_id_seq OWNED BY admin.users.id;


--
-- Name: account_type id; Type: DEFAULT; Schema: admin; Owner: admin
--

ALTER TABLE ONLY admin.account_type ALTER COLUMN id SET DEFAULT nextval('admin.account_type_id_seq'::regclass);


--
-- Name: accounts id; Type: DEFAULT; Schema: admin; Owner: admin
--

ALTER TABLE ONLY admin.accounts ALTER COLUMN id SET DEFAULT nextval('admin.accounts_id_seq'::regclass);


--
-- Name: cloud id; Type: DEFAULT; Schema: admin; Owner: admin
--

ALTER TABLE ONLY admin.cloud ALTER COLUMN id SET DEFAULT nextval('admin.cloud_id_seq'::regclass);


--
-- Name: cloud_config id; Type: DEFAULT; Schema: admin; Owner: admin
--

ALTER TABLE ONLY admin.cloud_config ALTER COLUMN id SET DEFAULT nextval('admin.cloud_config_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: admin; Owner: admin
--

ALTER TABLE ONLY admin.users ALTER COLUMN id SET DEFAULT nextval('admin.users_id_seq'::regclass);


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

