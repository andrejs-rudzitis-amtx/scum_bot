<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscordAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discordAnnouncements', function (Blueprint $table) {
            $table->id();
            $table->dateTime('sentAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('content')->nullable();
            $table->dateTime('publishedAt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discordAnnouncements');
    }
}
