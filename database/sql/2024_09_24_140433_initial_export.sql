create table "cache"
(
    "key"        varchar(255) not null,
    "value"      text         not null,
    "expiration" integer      not null
);

alter table "cache"
    add primary key ("key");

create table "cache_locks"
(
    "key"        varchar(255) not null,
    "owner"      varchar(255) not null,
    "expiration" integer      not null
);

alter table "cache_locks"
    add primary key ("key");

create table "jobs"
(
    "id"           bigserial    not null primary key,
    "queue"        varchar(255) not null,
    "payload"      text         not null,
    "attempts"     smallint     not null,
    "reserved_at"  integer null,
    "available_at" integer      not null,
    "created_at"   integer      not null
);

create index "jobs_queue_index" on "jobs" ("queue");

create table "job_batches"
(
    "id"             varchar(255) not null,
    "name"           varchar(255) not null,
    "total_jobs"     integer      not null,
    "pending_jobs"   integer      not null,
    "failed_jobs"    integer      not null,
    "failed_job_ids" text         not null,
    "options"        text null,
    "cancelled_at"   integer null,
    "created_at"     integer      not null,
    "finished_at"    integer null
);

alter table "job_batches"
    add primary key ("id");

create table "failed_jobs"
(
    "id"         bigserial    not null primary key,
    "uuid"       varchar(255) not null,
    "connection" text         not null,
    "queue"      text         not null,
    "payload"    text         not null,
    "exception"  text         not null,
    "failed_at"  timestamp(0) without time zone not null default CURRENT_TIMESTAMP
);

alter table "failed_jobs"
    add constraint "failed_jobs_uuid_unique" unique ("uuid");

create table "users"
(
    "id"                      uuid         not null,
    "name"                    varchar(255) not null,
    "email"                   varchar(255) not null,
    "registered_at"           timestamp(0) without time zone null,
    "password"                varchar(255) not null,
    "registration_token"      varchar(255) null,
    "two_factor_secret"       text null,
    "two_factor_confirmed_at" timestamp(0) without time zone null,
    "remember_token"          varchar(100) null,
    "created_at"              timestamp(0) without time zone null,
    "updated_at"              timestamp(0) without time zone null,
    "deleted_at"              timestamp(0) without time zone null
);

alter table "users"
    add primary key ("id");

alter table "users"
    add constraint "users_email_unique" unique ("email");

create table "password_reset_tokens"
(
    "email"      varchar(255) not null,
    "token"      varchar(255) not null,
    "created_at" timestamp(0) without time zone null
);

alter table "password_reset_tokens"
    add primary key ("email");

create table "sessions"
(
    "id"            varchar(255) not null,
    "user_id"       uuid null,
    "ip_address"    varchar(45) null,
    "user_agent"    text null,
    "payload"       text         not null,
    "last_activity" integer      not null
);

alter table "sessions"
    add primary key ("id");

create index "sessions_user_id_index" on "sessions" ("user_id");

create index "sessions_last_activity_index" on "sessions" ("last_activity");

create table "roles"
(
    "name" varchar(255) not null
);

alter table "roles"
    add primary key ("name");

create table "role_user"
(
    "user_id"   uuid         not null,
    "role_name" varchar(255) not null
);

alter table "role_user"
    add constraint "role_user_user_id_foreign" foreign key ("user_id") references "users" ("id");

alter table "role_user"
    add constraint "role_user_role_name_foreign" foreign key ("role_name") references "roles" ("name");

insert into "roles" ("name")
values ('userAdmin');

insert into "roles" ("name")
values ('user');

