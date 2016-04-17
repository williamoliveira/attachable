<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateAttachableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('attachable_files', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string("file_name");
            $table->string('file_extension');
            $table->integer("file_size")->nullable();
            $table->string("mime_type")->nullable();
            $table->boolean("use_intervention_image");
            $table->string('model')->index();

            $table->unsignedInteger('attachable_id')->index();
            $table->string('attachable_type')->index();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attachable_files');
    }
}
