<?php namespace App\Models\Supplier;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Company
 * @property int $id
 * @property string $name
 *
 * @method static Builder|static query()
 */
class Company extends Model
{
    const YARSHINTORG = 'yarshintorg';
    const MOSCOW_PIDORS = 'moscowPidors';
    const DISCOPTIM = 'discoptim';
    const KOOPER = 'kooper';
    const SEVER_AUTO  = 'severAuto';
    const SHIN_SERRVICE = 'shinSerrvice';
    const CONTINENTAL = 'continental';
    const FORTOCHKI = 'fortochki';
    const CAR_WHEELS = 'carWheels';
    const RED_WHEEL = 'redWheel';
    const ST_TUNING = 'stTuning';
    const YULTEK = 'yultek';

    /**
     * @var string
     */
    protected $table = 'company';

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
    ];

    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    /**
     * @return HasMany
     */
    public function custom(): HasMany
    {
        return $this->hasMany(Custom::class, 'id_company', 'id');
    }

    /**
     * @return array
     */
    public static function getForParse(): array
    {
        return self::query()
            ->whereHas('custom', function($query) {
                $query->where([
                    'parse_on' => 1,
                    'paused' => 0
                ]);
            })
            ->with(['custom' => function($query) {
                $query->where([
                    'parse_on' => 1,
                    'paused' => 0
                ]);
            }])
            ->get()
            ->toArray();
    }
}
