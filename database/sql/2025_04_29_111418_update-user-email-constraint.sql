alter table "users"
    drop constraint "users_email_unique";

alter table "users"
    add constraint "users_email_deleted_at_unique" unique ("email", "deleted_at");