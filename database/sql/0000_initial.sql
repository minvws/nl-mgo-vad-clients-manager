CREATE ROLE cbp_mgo;
ALTER ROLE cbp_mgo WITH NOSUPERUSER INHERIT NOCREATEROLE NOCREATEDB LOGIN NOREPLICATION NOBYPASSRLS;

CREATE ROLE cbp_dba;
ALTER ROLE cbp_dba WITH NOSUPERUSER INHERIT NOCREATEROLE NOCREATEDB LOGIN NOREPLICATION NOBYPASSRLS;

CREATE TABLE deploy_releases
(
        version varchar(255),
        deployed_at timestamp default now()
);

ALTER TABLE deploy_releases OWNER TO cbp_dba;

GRANT SELECT ON deploy_releases TO cbp_mgo;

INSERT INTO deploy_releases VALUES( 'v0000_initial.sql', now() );
