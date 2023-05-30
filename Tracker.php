namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    protected $table = 'tracker';

    protected $fillable = [
        'ip_address',
        'country',
        'browser_name',
        'language',
        'os',
        'is_guest',
        'user_id',
        'log_token',
        'current_route',
        'previous_route',
        'login_time',
        'logout_time',
    ];
}
