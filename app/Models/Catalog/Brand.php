<?php namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Brand
 * @property int $id
 * @property string $name
 * @property int $id_good_type
 *
 * @method static Builder|static query()
 */
class Brand extends Model
{
    /**
     * @var string
     */
    protected $table = 'brand';

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'id_good_type'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'id_good_type' => 'integer'
    ];

    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'id_good_type' => 'required|integer|exists:good_type,id'
        ];
    }
}
