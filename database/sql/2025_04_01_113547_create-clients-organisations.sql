select exists (select 1
               from pg_class c,
                    pg_namespace n
               where n.nspname = 'public'
                 and c.relname = 'migrations'
                 and c.relkind in ('r', 'p')
                 and n.oid = c.relnamespace);

create table "organisations"
(
    "id"                 uuid         not null,
    "main_contact_email" varchar(128) not null,
    "main_contact_name"  varchar(128) not null,
    "name"               varchar(128) not null,
    "coc_number"         varchar(8)   not null,
    "notes"              text null,
    "created_at"         timestamp(0) without time zone null,
    "updated_at"         timestamp(0) without time zone null
);

alter table "organisations"
    add primary key ("id");

create table "clients"
(
    "id"              uuid         not null,
    "organisation_id" uuid         not null,
    "redirect_uris"   jsonb        not null,
    "fqdn"            varchar(256) not null,
    "active"          boolean      not null default '0',
    "created_at"      timestamp(0) without time zone null,
    "updated_at"      timestamp(0) without time zone null
);

alter table "clients"
    add constraint "clients_organisation_id_foreign" foreign key ("organisation_id") references "organisations" ("id") on delete cascade;

alter table "clients"
    add primary key ("id");

alter table "clients"
    add constraint "clients_fqdn_unique" unique ("fqdn");

create table "registration_requests"
(
    "id"                              uuid         not null,
    "organisation_name"               varchar(128) not null,
    "organisation_main_contact_email" varchar(128) not null,
    "organisation_main_contact_name"  varchar(128) not null,
    "organisation_coc_number"         varchar(8)   not null,
    "client_redirect_uris"            jsonb        not null,
    "client_fqdn"                     varchar(256) not null,
    "notes"                           text null,
    "created_at"                      timestamp(0) without time zone null,
    "updated_at"                      timestamp(0) without time zone null
);

alter table "registration_requests"
    add primary key ("id");

