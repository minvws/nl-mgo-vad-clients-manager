select exists (select 1 from pg_class c, pg_namespace n where n.nspname = 'public' and c.relname = 'migrations' and c.relkind in ('r', 'p') and n.oid = c.relnamespace);

alter table "clients" drop column "fqdn";

alter table "registration_requests" drop column "client_fqdn";

