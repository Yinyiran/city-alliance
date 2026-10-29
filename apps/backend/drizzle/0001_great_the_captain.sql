CREATE TABLE "coupons" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"shop_id" uuid,
	"title" varchar(100) NOT NULL,
	"coupon_type" varchar(30) DEFAULT 'CASH' NOT NULL,
	"face_value" numeric(10, 2) NOT NULL,
	"min_order_amount" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"total_quota" integer DEFAULT 0 NOT NULL,
	"issued_count" integer DEFAULT 0 NOT NULL,
	"used_count" integer DEFAULT 0 NOT NULL,
	"valid_days" integer,
	"valid_start_time" timestamp with time zone,
	"valid_end_time" timestamp with time zone,
	"status" varchar(20) DEFAULT 'ACTIVE' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "member_coupons" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"member_id" uuid NOT NULL,
	"coupon_id" uuid NOT NULL,
	"coupon_code" varchar(32) NOT NULL,
	"status" varchar(20) DEFAULT 'UNUSED' NOT NULL,
	"used_order_id" uuid,
	"used_at" timestamp with time zone,
	"expire_at" timestamp with time zone NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	CONSTRAINT "member_coupons_coupon_code_unique" UNIQUE("coupon_code")
);
--> statement-breakpoint
CREATE TABLE "balance_records" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"member_id" uuid NOT NULL,
	"change_type" varchar(30) NOT NULL,
	"amount" numeric(12, 2) NOT NULL,
	"balance_before" numeric(12, 2) NOT NULL,
	"balance_after" numeric(12, 2) NOT NULL,
	"reference_no" varchar(64),
	"remark" varchar(255),
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "points_records" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"member_id" uuid NOT NULL,
	"change_type" varchar(30) NOT NULL,
	"points" integer NOT NULL,
	"points_before" integer NOT NULL,
	"points_after" integer NOT NULL,
	"reference_no" varchar(64),
	"remark" varchar(255),
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "member_addresses" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"member_id" uuid NOT NULL,
	"receiver_name" varchar(50) NOT NULL,
	"phone" varchar(20) NOT NULL,
	"province" varchar(50) NOT NULL,
	"city" varchar(50) NOT NULL,
	"district" varchar(50) NOT NULL,
	"detail_address" varchar(255) NOT NULL,
	"postal_code" varchar(10),
	"latitude" numeric(10, 6),
	"longitude" numeric(10, 6),
	"is_default" boolean DEFAULT false NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "cashier_shifts" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"store_id" uuid NOT NULL,
	"clerk_id" uuid NOT NULL,
	"shift_start" timestamp with time zone NOT NULL,
	"shift_end" timestamp with time zone,
	"cash_total" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"scan_total" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"order_count" integer DEFAULT 0 NOT NULL,
	"diff_amount" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"status" varchar(20) DEFAULT 'OPEN' NOT NULL,
	"remark" varchar(255),
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "store_clerks" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"store_id" uuid NOT NULL,
	"user_id" uuid NOT NULL,
	"clerk_no" varchar(30) NOT NULL,
	"real_name" varchar(50) NOT NULL,
	"phone" varchar(20) NOT NULL,
	"role" varchar(30) DEFAULT 'CASHIER' NOT NULL,
	"status" varchar(20) DEFAULT 'ACTIVE' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "express_companies" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"code" varchar(30) NOT NULL,
	"name" varchar(50) NOT NULL,
	"phone" varchar(30),
	"website" varchar(200),
	"is_enabled" boolean DEFAULT true NOT NULL,
	"sort_order" integer DEFAULT 0 NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	CONSTRAINT "express_companies_code_unique" UNIQUE("code")
);
--> statement-breakpoint
CREATE TABLE "order_delivery_traces" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"order_id" uuid NOT NULL,
	"express_company_id" uuid,
	"tracking_no" varchar(64),
	"delivery_type" varchar(30) DEFAULT 'EXPRESS' NOT NULL,
	"rider_name" varchar(50),
	"rider_phone" varchar(20),
	"status" varchar(30) DEFAULT 'SHIPPED' NOT NULL,
	"trace_logs" jsonb DEFAULT '[]'::jsonb,
	"shipped_at" timestamp with time zone DEFAULT now() NOT NULL,
	"delivered_at" timestamp with time zone,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "goods_categories" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"parent_id" uuid,
	"name" varchar(100) NOT NULL,
	"icon" varchar(255),
	"sort_order" integer DEFAULT 0 NOT NULL,
	"is_visible" boolean DEFAULT true NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "media_attachments" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"tenant_id" uuid,
	"file_name" varchar(255) NOT NULL,
	"file_ext" varchar(20) NOT NULL,
	"file_size" integer NOT NULL,
	"mime_type" varchar(100) NOT NULL,
	"storage_driver" varchar(20) DEFAULT 'LOCAL' NOT NULL,
	"file_url" varchar(500) NOT NULL,
	"file_hash" varchar(64),
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "system_audit_logs" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"user_id" uuid,
	"tenant_id" uuid,
	"action" varchar(100) NOT NULL,
	"target_entity" varchar(50) NOT NULL,
	"target_id" varchar(64),
	"ip_address" varchar(50),
	"user_agent" varchar(255),
	"changes" jsonb,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "system_configs" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"config_key" varchar(100) NOT NULL,
	"config_value" jsonb NOT NULL,
	"group" varchar(50) DEFAULT 'SYSTEM' NOT NULL,
	"description" varchar(255),
	"is_encrypted" boolean DEFAULT false NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL,
	CONSTRAINT "system_configs_config_key_unique" UNIQUE("config_key")
);
--> statement-breakpoint
ALTER TABLE "coupons" ADD CONSTRAINT "coupons_shop_id_shops_id_fk" FOREIGN KEY ("shop_id") REFERENCES "public"."shops"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "member_coupons" ADD CONSTRAINT "member_coupons_member_id_members_id_fk" FOREIGN KEY ("member_id") REFERENCES "public"."members"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "member_coupons" ADD CONSTRAINT "member_coupons_coupon_id_coupons_id_fk" FOREIGN KEY ("coupon_id") REFERENCES "public"."coupons"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "member_coupons" ADD CONSTRAINT "member_coupons_used_order_id_orders_id_fk" FOREIGN KEY ("used_order_id") REFERENCES "public"."orders"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "balance_records" ADD CONSTRAINT "balance_records_member_id_members_id_fk" FOREIGN KEY ("member_id") REFERENCES "public"."members"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "points_records" ADD CONSTRAINT "points_records_member_id_members_id_fk" FOREIGN KEY ("member_id") REFERENCES "public"."members"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "member_addresses" ADD CONSTRAINT "member_addresses_member_id_members_id_fk" FOREIGN KEY ("member_id") REFERENCES "public"."members"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "cashier_shifts" ADD CONSTRAINT "cashier_shifts_store_id_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."stores"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "cashier_shifts" ADD CONSTRAINT "cashier_shifts_clerk_id_store_clerks_id_fk" FOREIGN KEY ("clerk_id") REFERENCES "public"."store_clerks"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "store_clerks" ADD CONSTRAINT "store_clerks_store_id_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."stores"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "store_clerks" ADD CONSTRAINT "store_clerks_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "order_delivery_traces" ADD CONSTRAINT "order_delivery_traces_order_id_orders_id_fk" FOREIGN KEY ("order_id") REFERENCES "public"."orders"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "order_delivery_traces" ADD CONSTRAINT "order_delivery_traces_express_company_id_express_companies_id_fk" FOREIGN KEY ("express_company_id") REFERENCES "public"."express_companies"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "media_attachments" ADD CONSTRAINT "media_attachments_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "system_audit_logs" ADD CONSTRAINT "system_audit_logs_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "system_audit_logs" ADD CONSTRAINT "system_audit_logs_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;