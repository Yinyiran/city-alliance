ALTER TABLE "store_clerks" ADD COLUMN "permissions" text[] DEFAULT '{}' NOT NULL;--> statement-breakpoint
ALTER TABLE "store_clerks" ADD COLUMN "verify_count" integer DEFAULT 0 NOT NULL;