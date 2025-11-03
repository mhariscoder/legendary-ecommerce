<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Full name of the customer
            $table->string('email')->unique(); // Email address
            $table->string('phone'); // Customer's phone number
            $table->text('shipping_address'); // Shipping address of the customer
            $table->text('billing_address')->nullable(); // Billing address (optional)
            $table->string('status')->default('active'); // Customer status (active, inactive, etc.)
            $table->timestamps(); // Created at & updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
