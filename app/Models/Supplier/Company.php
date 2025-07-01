<?php namespace App\Models\Supplier;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Company
 * @property int $id
 * @property string $name
 *
 * @method static Builder|static query()
 */
class Company extends Model
{
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
}
