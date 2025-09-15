CREATE UNIQUE INDEX unique_active_email ON users(email) WHERE deleted_at IS NULL;

ALTER TABLE users DROP CONSTRAINT IF EXISTS users_email_deleted_at_unique;

