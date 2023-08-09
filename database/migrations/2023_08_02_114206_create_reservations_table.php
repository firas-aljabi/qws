<?php

use App\Statuses\ReservationEvent;
use App\Statuses\ReservationStatus;
use App\Statuses\ReservationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->unsignedBigInteger('expert_id');
            $table->foreign('expert_id')->references('id')->on('experts')->onDelete('cascade');
            $table->dateTime('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('reservation_number')->unique();
            $table->tinyInteger('event')->default(ReservationEvent::OTHERS);
            $table->tinyInteger('type')->default(ReservationType::UN_APPROVED);
            $table->tinyInteger('status')->default(ReservationStatus::PENDING);
            $table->float('reservation_amount', 8, 2)->nullable();
            $table->tinyInteger('payment_status')->nullable();
            $table->tinyInteger('payment_way')->nullable();
            $table->tinyInteger('amount_type')->nullable();
            $table->dateTime('delay_date')->nullable();
            $table->string('notes')->nullable();
            $table->longText('attachment')->nullable();
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
        Schema::dropIfExists('reservations');
    }
};
