CREATE TABLE "roles" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"code" varchar(50) NOT NULL,
	"name" varchar(50) NOT NULL,
	"tenant_type" varchar(30) NOT NULL,
	"permissions" text[] DEFAULT '{}' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	CONSTRAINT "roles_code_unique" UNIQUE("code")
);
--> statement-breakpoint
CREATE TABLE "tenants" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"name" varchar(100) NOT NULL,
	"code" varchar(50) NOT NULL,
	"type" varchar(30) NOT NULL,
	"parent_id" uuid,
	"status" varchar(20) DEFAULT 'ACTIVE' NOT NULL,
	"config" jsonb DEFAULT '{}'::jsonb,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL,
	CONSTRAINT "tenants_code_unique" UNIQUE("code")
);
--> statement-breakpoint
CREATE TABLE "user_roles" (
	"user_id" uuid NOT NULL,
	"role_id" uuid NOT NULL
);
--> statement-breakpoint
CREATE TABLE "users" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"username" varchar(50) NOT NULL,
	"password_hash" varchar(255) NOT NULL,
	"nickname" varchar(50) NOT NULL,
	"avatar_url" varchar(255),
	"mobile" varchar(20),
	"tenant_id" uuid,
	"status" varchar(20) DEFAULT 'ACTIVE' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL,
	CONSTRAINT "users_username_unique" UNIQUE("username")
);
--> statement-breakpoint
CREATE TABLE "shops" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"tenant_id" uuid NOT NULL,
	"name" varchar(100) NOT NULL,
	"logo_url" varchar(255) NOT NULL,
	"city_id" uuid,
	"owner_user_id" uuid NOT NULL,
	"sales_multiplier" numeric(5, 2) DEFAULT '1.00' NOT NULL,
	"wechat_corp_id" varchar(100),
	"status" varchar(20) DEFAULT 'ACTIVE' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "stores" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"shop_id" uuid NOT NULL,
	"name" varchar(100) NOT NULL,
	"phone" varchar(30) NOT NULL,
	"address" varchar(255) NOT NULL,
	"latitude" numeric(10, 7) NOT NULL,
	"longitude" numeric(10, 7) NOT NULL,
	"status" varchar(20) DEFAULT 'OPEN' NOT NULL,
	"cloud_printer_sn" varchar(50),
	"cloud_printer_type" varchar(30),
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "wecom_qr_codes" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"shop_id" uuid NOT NULL,
	"store_id" uuid,
	"qr_code_url" varchar(255) NOT NULL,
	"welcome_text" text NOT NULL,
	"max_limit" integer DEFAULT 200 NOT NULL,
	"current_count" integer DEFAULT 0 NOT NULL,
	"sort_order" integer DEFAULT 0 NOT NULL,
	"is_active" boolean DEFAULT true NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "member_upgrade_records" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"member_id" uuid NOT NULL,
	"from_level" varchar(20) NOT NULL,
	"to_level" varchar(20) NOT NULL,
	"pay_amount" numeric(10, 2) NOT NULL,
	"payment_channel" varchar(30) NOT NULL,
	"inviter_id" uuid,
	"inviter_reward" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"shop_manager_reward" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"branch_reward" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"headquarter_reward" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"is_partner_custom_grant" boolean DEFAULT false NOT NULL,
	"status" varchar(20) DEFAULT 'PAID' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "members" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"member_code" varchar(6) NOT NULL,
	"user_id" uuid NOT NULL,
	"level" varchar(20) DEFAULT 'NORMAL' NOT NULL,
	"inviter_id" uuid,
	"bind_shop_id" uuid,
	"bind_store_id" uuid,
	"points" integer DEFAULT 0 NOT NULL,
	"balance" numeric(12, 2) DEFAULT '0.00' NOT NULL,
	"frozen_balance" numeric(12, 2) DEFAULT '0.00' NOT NULL,
	"total_earned" numeric(12, 2) DEFAULT '0.00' NOT NULL,
	"real_name" varchar(50),
	"id_card_no" varchar(30),
	"employment_type" varchar(50) DEFAULT '其它',
	"is_real_name_verified" boolean DEFAULT false NOT NULL,
	"status" varchar(20) DEFAULT 'ACTIVE' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL,
	CONSTRAINT "members_member_code_unique" UNIQUE("member_code")
);
--> statement-breakpoint
CREATE TABLE "freight_templates" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"shop_id" uuid NOT NULL,
	"name" varchar(100) NOT NULL,
	"charge_type" varchar(20) DEFAULT 'BY_COUNT' NOT NULL,
	"first_quantity" integer DEFAULT 1 NOT NULL,
	"first_fee" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"continue_quantity" integer DEFAULT 1 NOT NULL,
	"continue_fee" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"free_shipping_threshold" numeric(10, 2),
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "goods_sku" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"spu_id" uuid NOT NULL,
	"sku_code" varchar(100),
	"spec_values" jsonb NOT NULL,
	"price" numeric(10, 2) NOT NULL,
	"stock" integer DEFAULT 0 NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "goods_spu" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"shop_id" uuid NOT NULL,
	"name" varchar(200) NOT NULL,
	"trade_type" varchar(30) NOT NULL,
	"category_id" uuid,
	"cover_image" varchar(255) NOT NULL,
	"album_images" text[] DEFAULT '{}' NOT NULL,
	"description" text,
	"status" varchar(20) DEFAULT 'ON_SALE' NOT NULL,
	"base_price" numeric(10, 2) NOT NULL,
	"freight_template_id" uuid,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "order_items" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"order_id" uuid NOT NULL,
	"spu_id" uuid NOT NULL,
	"sku_id" uuid NOT NULL,
	"goods_name" varchar(200) NOT NULL,
	"spec_text" varchar(200),
	"price" numeric(10, 2) NOT NULL,
	"quantity" integer DEFAULT 1 NOT NULL,
	"total_amount" numeric(10, 2) NOT NULL
);
--> statement-breakpoint
CREATE TABLE "order_refunds" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"order_id" uuid NOT NULL,
	"refund_no" varchar(64) NOT NULL,
	"refund_amount" numeric(10, 2) NOT NULL,
	"reason" text NOT NULL,
	"status" varchar(20) DEFAULT 'PENDING_AUDIT' NOT NULL,
	"arbitrated_by" uuid,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL,
	CONSTRAINT "order_refunds_refund_no_unique" UNIQUE("refund_no")
);
--> statement-breakpoint
CREATE TABLE "orders" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"order_no" varchar(64) NOT NULL,
	"customer_id" uuid NOT NULL,
	"shop_id" uuid NOT NULL,
	"store_id" uuid,
	"trade_type" varchar(30) NOT NULL,
	"order_status" varchar(30) DEFAULT 'PENDING_PAY' NOT NULL,
	"pay_status" varchar(20) DEFAULT 'UNPAID' NOT NULL,
	"total_amount" numeric(10, 2) NOT NULL,
	"discount_amount" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"pay_amount" numeric(10, 2) NOT NULL,
	"payment_channel" varchar(30),
	"verify_code" varchar(20),
	"verified_at" timestamp with time zone,
	"verify_user_id" uuid,
	"appointment_time" timestamp with time zone,
	"delivery_info" text,
	"paid_at" timestamp with time zone,
	"completed_at" timestamp with time zone,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL,
	CONSTRAINT "orders_order_no_unique" UNIQUE("order_no")
);
--> statement-breakpoint
CREATE TABLE "commission_records" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"order_id" uuid,
	"commission_type" varchar(50) NOT NULL,
	"beneficiary_id" uuid NOT NULL,
	"beneficiary_type" varchar(30) NOT NULL,
	"ratio_percent" numeric(6, 3) NOT NULL,
	"amount" numeric(10, 2) NOT NULL,
	"status" varchar(20) DEFAULT 'FROZEN' NOT NULL,
	"unfreeze_due_date" timestamp with time zone,
	"unfrozen_at" timestamp with time zone,
	"revoked_at" timestamp with time zone,
	"revoke_reason" text,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "tax_withholding_records" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"withdraw_id" uuid NOT NULL,
	"member_id" uuid NOT NULL,
	"gross_amount" numeric(10, 2) NOT NULL,
	"cost_deduction" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"taxable_income" numeric(10, 2) NOT NULL,
	"tax_rate_percent" numeric(5, 2) NOT NULL,
	"quick_deduction" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"tax_withheld" numeric(10, 2) NOT NULL,
	"tax_period" varchar(7) NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "withdraw_records" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"applicant_id" uuid NOT NULL,
	"applicant_type" varchar(20) NOT NULL,
	"gross_amount" numeric(10, 2) NOT NULL,
	"platform_fee" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"tax_amount" numeric(10, 2) DEFAULT '0.00' NOT NULL,
	"net_amount" numeric(10, 2) NOT NULL,
	"channel" varchar(30) NOT NULL,
	"real_name" varchar(50) NOT NULL,
	"id_card_no" varchar(30) NOT NULL,
	"bank_card_no" varchar(50),
	"status" varchar(20) DEFAULT 'AUDITING' NOT NULL,
	"audit_note" text,
	"audited_by" uuid,
	"audited_at" timestamp with time zone,
	"paid_at" timestamp with time zone,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "store_sign_in_records" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"store_id" uuid NOT NULL,
	"member_id" uuid NOT NULL,
	"year_month" varchar(7) NOT NULL,
	"merchant_deducted_amount" numeric(10, 2) DEFAULT '2.00' NOT NULL,
	"reward_cash" numeric(10, 2) DEFAULT '1.00' NOT NULL,
	"reward_points" integer DEFAULT 50 NOT NULL,
	"gift_order_id" uuid,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "video_progress_records" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"video_id" uuid NOT NULL,
	"member_id" uuid NOT NULL,
	"watched_seconds" integer NOT NULL,
	"points_awarded" integer DEFAULT 0 NOT NULL,
	"nonce" varchar(64) NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "purchase_orders" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"order_no" varchar(64) NOT NULL,
	"supplier_id" uuid NOT NULL,
	"buyer_shop_id" uuid NOT NULL,
	"total_amount" numeric(10, 2) NOT NULL,
	"status" varchar(30) DEFAULT 'PENDING_PAY' NOT NULL,
	"shipping_tracking_no" varchar(100),
	"notes" text,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL,
	CONSTRAINT "purchase_orders_order_no_unique" UNIQUE("order_no")
);
--> statement-breakpoint
CREATE TABLE "suppliers" (
	"id" uuid PRIMARY KEY DEFAULT gen_random_uuid() NOT NULL,
	"tenant_id" uuid NOT NULL,
	"company_name" varchar(150) NOT NULL,
	"contact_name" varchar(50) NOT NULL,
	"contact_phone" varchar(20) NOT NULL,
	"license_image_url" varchar(255),
	"status" varchar(20) DEFAULT 'PENDING_AUDIT' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
ALTER TABLE "user_roles" ADD CONSTRAINT "user_roles_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "user_roles" ADD CONSTRAINT "user_roles_role_id_roles_id_fk" FOREIGN KEY ("role_id") REFERENCES "public"."roles"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "users" ADD CONSTRAINT "users_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "shops" ADD CONSTRAINT "shops_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "shops" ADD CONSTRAINT "shops_city_id_tenants_id_fk" FOREIGN KEY ("city_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "shops" ADD CONSTRAINT "shops_owner_user_id_users_id_fk" FOREIGN KEY ("owner_user_id") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "stores" ADD CONSTRAINT "stores_shop_id_shops_id_fk" FOREIGN KEY ("shop_id") REFERENCES "public"."shops"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "wecom_qr_codes" ADD CONSTRAINT "wecom_qr_codes_shop_id_shops_id_fk" FOREIGN KEY ("shop_id") REFERENCES "public"."shops"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "wecom_qr_codes" ADD CONSTRAINT "wecom_qr_codes_store_id_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."stores"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "member_upgrade_records" ADD CONSTRAINT "member_upgrade_records_member_id_members_id_fk" FOREIGN KEY ("member_id") REFERENCES "public"."members"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "member_upgrade_records" ADD CONSTRAINT "member_upgrade_records_inviter_id_members_id_fk" FOREIGN KEY ("inviter_id") REFERENCES "public"."members"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "members" ADD CONSTRAINT "members_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "members" ADD CONSTRAINT "members_bind_shop_id_shops_id_fk" FOREIGN KEY ("bind_shop_id") REFERENCES "public"."shops"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "members" ADD CONSTRAINT "members_bind_store_id_stores_id_fk" FOREIGN KEY ("bind_store_id") REFERENCES "public"."stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "freight_templates" ADD CONSTRAINT "freight_templates_shop_id_shops_id_fk" FOREIGN KEY ("shop_id") REFERENCES "public"."shops"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "goods_sku" ADD CONSTRAINT "goods_sku_spu_id_goods_spu_id_fk" FOREIGN KEY ("spu_id") REFERENCES "public"."goods_spu"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "goods_spu" ADD CONSTRAINT "goods_spu_shop_id_shops_id_fk" FOREIGN KEY ("shop_id") REFERENCES "public"."shops"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "goods_spu" ADD CONSTRAINT "goods_spu_freight_template_id_freight_templates_id_fk" FOREIGN KEY ("freight_template_id") REFERENCES "public"."freight_templates"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "order_items" ADD CONSTRAINT "order_items_order_id_orders_id_fk" FOREIGN KEY ("order_id") REFERENCES "public"."orders"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "order_items" ADD CONSTRAINT "order_items_spu_id_goods_spu_id_fk" FOREIGN KEY ("spu_id") REFERENCES "public"."goods_spu"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "order_items" ADD CONSTRAINT "order_items_sku_id_goods_sku_id_fk" FOREIGN KEY ("sku_id") REFERENCES "public"."goods_sku"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "order_refunds" ADD CONSTRAINT "order_refunds_order_id_orders_id_fk" FOREIGN KEY ("order_id") REFERENCES "public"."orders"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "order_refunds" ADD CONSTRAINT "order_refunds_arbitrated_by_users_id_fk" FOREIGN KEY ("arbitrated_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "orders" ADD CONSTRAINT "orders_customer_id_members_id_fk" FOREIGN KEY ("customer_id") REFERENCES "public"."members"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "orders" ADD CONSTRAINT "orders_shop_id_shops_id_fk" FOREIGN KEY ("shop_id") REFERENCES "public"."shops"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "orders" ADD CONSTRAINT "orders_store_id_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "orders" ADD CONSTRAINT "orders_verify_user_id_users_id_fk" FOREIGN KEY ("verify_user_id") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "commission_records" ADD CONSTRAINT "commission_records_order_id_orders_id_fk" FOREIGN KEY ("order_id") REFERENCES "public"."orders"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "tax_withholding_records" ADD CONSTRAINT "tax_withholding_records_withdraw_id_withdraw_records_id_fk" FOREIGN KEY ("withdraw_id") REFERENCES "public"."withdraw_records"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "tax_withholding_records" ADD CONSTRAINT "tax_withholding_records_member_id_members_id_fk" FOREIGN KEY ("member_id") REFERENCES "public"."members"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "withdraw_records" ADD CONSTRAINT "withdraw_records_audited_by_users_id_fk" FOREIGN KEY ("audited_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "store_sign_in_records" ADD CONSTRAINT "store_sign_in_records_store_id_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "store_sign_in_records" ADD CONSTRAINT "store_sign_in_records_member_id_members_id_fk" FOREIGN KEY ("member_id") REFERENCES "public"."members"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "store_sign_in_records" ADD CONSTRAINT "store_sign_in_records_gift_order_id_orders_id_fk" FOREIGN KEY ("gift_order_id") REFERENCES "public"."orders"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "video_progress_records" ADD CONSTRAINT "video_progress_records_member_id_members_id_fk" FOREIGN KEY ("member_id") REFERENCES "public"."members"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "purchase_orders" ADD CONSTRAINT "purchase_orders_supplier_id_suppliers_id_fk" FOREIGN KEY ("supplier_id") REFERENCES "public"."suppliers"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "purchase_orders" ADD CONSTRAINT "purchase_orders_buyer_shop_id_shops_id_fk" FOREIGN KEY ("buyer_shop_id") REFERENCES "public"."shops"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "suppliers" ADD CONSTRAINT "suppliers_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;