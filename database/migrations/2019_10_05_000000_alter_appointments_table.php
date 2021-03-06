<?php

use Wappointment\ClassConnect\Capsule;
use Wappointment\Config\Database;
use Wappointment\Models\Appointment;

class AlterAppointmentsTable extends Wappointment\Installation\Migrate
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $foreignName = $this->getForeignName(Database::$prefix_self . '_appointments' . '_' . 'client_id_foreign');

        Capsule::schema()->table(Database::$prefix_self . '_appointments', function ($table) use ($foreignName) {
            $table->unsignedInteger('staff_id')->nullable()->default(null)->change();
            $table->unsignedInteger('service_id')->nullable()->default(null)->change();
            $table->unsignedInteger('client_id')->nullable()->default(null)->change();
            if ($foreignName === false) {
                $table->foreign('client_id')->references('id')->on(Database::$prefix_self . '_clients');
            } else {
                $table->foreign('client_id', $foreignName)->references('id')->on(Database::$prefix_self . '_clients');
            }
        });

        Appointment::where('staff_id', 0)->where('service_id', 0)->update([
            'staff_id' => null,
            'service_id' => null
        ]);
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $foreignName = $this->getForeignName(Database::$prefix_self . '_appointments' . '_' . 'client_id_foreign');
        Capsule::schema()->table(Database::$prefix_self . '_appointments', function ($table) use ($foreignName) {
            if ($foreignName === false) {
                $table->dropForeign(['client_id']);
            } else {
                $table->dropForeign($foreignName);
            }
        });
    }
}
