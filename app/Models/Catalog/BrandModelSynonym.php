<?php namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BrandModelSynonym
 *
 * @property int $id
 * @property int $id_model
 * @property string|null $brand_synonym
 * @property string|null $model_synonym
 *
 * @method static Builder|static query()
 */
class BrandModelSynonym extends Model
{
    /**
     * @var string
     */
    protected $table = 'brand_model_synonyms';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'id_model',
        'brand_synonym',
        'model_synonym'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_model' => 'integer',
        'brand_synonym' => 'string',
        'model_synonym' => 'string'
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
            'id_model' => 'required|integer|exists:model,id',
            'brand_synonym' => 'nullable|string|max:255',
            'model_synonym' => 'nullable|string|max:255'
        ];
    }

    /**
     * Relationship to the Model
     *
     * @return BelongsTo
     */
    public function model(): BelongsTo
    {
        return $this->belongsTo(Model::class, 'id_model');
    }
}
