<?php namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Class Model
 * @property int $id
 * @property string $name
 * @property int $id_brand
 *
 * @method static Builder|static query()
 */
class Model extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'model';

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'id_brand'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'id_brand' => 'integer'
    ];

    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'id_brand' => 'required|integer|exists:brand,id'
        ];
    }
}
