<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->id('id_conge');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('type',['maladie' , 'annuel','maternite','autre']);
            $table->enum('decision_comite',['non_traite' , 'approuve ','rejete'])
                ->default('non_traite')->comment('decision du comite');
            $table->enum('statut',['en_attente' , 'approuve ','rejete'])
                ->default('en_attente')->comment('Status du conge');
            $table->unsignedBigInteger('id_employer');
            $table->timestamps();
            $table->foreign('id_employer')->references('id_employer')->on('employers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
};

