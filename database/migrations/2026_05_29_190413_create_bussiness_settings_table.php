<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, file, json, boolean
            $table->string('group')->default('general'); // general, email, contact, location
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default settings
        DB::table('business_settings')->insert([
            // General Settings
            ['key' => 'company_name', 'value' => 'My Business', 'type' => 'text', 'group' => 'general', 'sort_order' => 1, 'created_at' => now()],
            ['key' => 'company_logo', 'value' => null, 'type' => 'file', 'group' => 'general', 'sort_order' => 2, 'created_at' => now()],
            ['key' => 'company_logo_dark', 'value' => null, 'type' => 'file', 'group' => 'general', 'sort_order' => 3, 'created_at' => now()],
            ['key' => 'favicon', 'value' => null, 'type' => 'file', 'group' => 'general', 'sort_order' => 4, 'created_at' => now()],
            ['key' => 'company_stamp', 'value' => null, 'type' => 'file', 'group' => 'general', 'sort_order' => 5, 'created_at' => now()],

            // Contact Settings
            ['key' => 'phone', 'value' => null, 'type' => 'text', 'group' => 'contact', 'sort_order' => 6, 'created_at' => now()],
            ['key' => 'phone_alternative', 'value' => null, 'type' => 'text', 'group' => 'contact', 'sort_order' => 7, 'created_at' => now()],
            ['key' => 'email', 'value' => null, 'type' => 'text', 'group' => 'contact', 'sort_order' => 8, 'created_at' => now()],
            ['key' => 'email_alternative', 'value' => null, 'type' => 'text', 'group' => 'contact', 'sort_order' => 9, 'created_at' => now()],
            ['key' => 'address', 'value' => null, 'type' => 'text', 'group' => 'contact', 'sort_order' => 10, 'created_at' => now()],
            ['key' => 'city', 'value' => null, 'type' => 'text', 'group' => 'contact', 'sort_order' => 11, 'created_at' => now()],
            ['key' => 'country', 'value' => 'Uganda', 'type' => 'text', 'group' => 'contact', 'sort_order' => 12, 'created_at' => now()],
            ['key' => 'postal_code', 'value' => null, 'type' => 'text', 'group' => 'contact', 'sort_order' => 13, 'created_at' => now()],

            // Social Media
            ['key' => 'facebook', 'value' => null, 'type' => 'text', 'group' => 'contact', 'sort_order' => 14, 'created_at' => now()],
            ['key' => 'twitter', 'value' => null, 'type' => 'text', 'group' => 'contact', 'sort_order' => 15, 'created_at' => now()],
            ['key' => 'instagram', 'value' => null, 'type' => 'text', 'group' => 'contact', 'sort_order' => 16, 'created_at' => now()],
            ['key' => 'linkedin', 'value' => null, 'type' => 'text', 'group' => 'contact', 'sort_order' => 17, 'created_at' => now()],

            // Email Settings (SMTP)
            ['key' => 'mail_mailer', 'value' => 'smtp', 'type' => 'text', 'group' => 'email', 'sort_order' => 18, 'created_at' => now()],
            ['key' => 'mail_host', 'value' => 'smtp.gmail.com', 'type' => 'text', 'group' => 'email', 'sort_order' => 19, 'created_at' => now()],
            ['key' => 'mail_port', 'value' => '587', 'type' => 'text', 'group' => 'email', 'sort_order' => 20, 'created_at' => now()],
            ['key' => 'mail_username', 'value' => null, 'type' => 'text', 'group' => 'email', 'sort_order' => 21, 'created_at' => now()],
            ['key' => 'mail_password', 'value' => null, 'type' => 'text', 'group' => 'email', 'sort_order' => 22, 'created_at' => now()],
            ['key' => 'mail_encryption', 'value' => 'tls', 'type' => 'text', 'group' => 'email', 'sort_order' => 23, 'created_at' => now()],
            ['key' => 'mail_from_address', 'value' => null, 'type' => 'text', 'group' => 'email', 'sort_order' => 24, 'created_at' => now()],
            ['key' => 'mail_from_name', 'value' => null, 'type' => 'text', 'group' => 'email', 'sort_order' => 25, 'created_at' => now()],

            // Locations/Branches (stored as JSON)
            ['key' => 'locations', 'value' => null, 'type' => 'json', 'group' => 'location', 'sort_order' => 26, 'created_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('business_settings');
    }
};
