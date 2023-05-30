use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackerTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracker', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->ipAddress('ip_address')->nullable();
            $table->string('country')->nullable();
            $table->string('browser_name')->nullable();
            $table->string('language')->nullable();
            $table->string('os')->nullable();
            $table->boolean('is_guest')->default(false);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('log_token')->nullable();
            $table->string('current_route')->nullable();
            $table->string('previous_route')->nullable();
            $table->timestamp('login_time')->nullable();
            $table->timestamp('logout_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracker');
    }
}
