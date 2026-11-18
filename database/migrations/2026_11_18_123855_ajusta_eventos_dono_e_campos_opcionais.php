<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // Criador do evento: só ele pode editar ou excluir.
            // Nullable para não quebrar eventos já existentes no banco.
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();

            // Pelo enunciado, apenas o nome é obrigatório.
            $table->text('descricao')->nullable()->change();
            $table->string('local')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');

            $table->text('descricao')->nullable(false)->change();
            $table->string('local')->nullable(false)->change();
        });
    }
};
