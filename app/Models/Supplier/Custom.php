<?php namespace App\Models\Supplier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $name
 * @property string|null $name_ru
 * @property int $id_company
 * @property bool $visible_for_manager
 * @property bool $parse_on
 * @property bool $paused
 *
 * @method static Builder|static query()
 */
class Custom extends Model
{
    /**
     * @var string
     */
    protected $table = 'custom';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'name_ru',
        'id_company',
        'visible_for_manager',
        'parse_on',
        'paused'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'name_ru' => 'string',
        'id_company' => 'integer',
        'visible_for_manager' => 'boolean',
        'parse_on' => 'boolean',
        'paused' => 'boolean'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'id_company' => 'required|integer|exists:company,id',
            'visible_for_manager' => 'boolean',
            'parse_on' => 'boolean',
            'paused' => 'boolean'
        ];
    }
}
