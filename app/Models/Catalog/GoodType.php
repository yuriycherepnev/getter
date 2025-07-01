<?php namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Model
 * @property int $id
 * @property string $name
 * @property string $name_ru
 */
class GoodType extends Model
{
    /**
     * @var string
     */
    protected $table = 'good_type';

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
        'name_ru',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'name_ru' => 'string',
    ];

    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'name_ru' => 'required|string|max:255',
        ];
    }
}
