<?php namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model as BaseModel;

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
     * @var string
     */
    protected $primaryKey = 'id';

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

    /**
     * @return array
     */
    public static function messages(): array
    {
        return [
            'name.required' => 'Поле "Название" обязательно для заполнения',
            'name.string' => 'Название должно быть строкой',
            'name.max' => 'Название не должно превышать 255 символов',
            'id_brand.required' => 'Необходимо указать бренд',
            'id_brand.integer' => 'ID бренда должен быть целым числом',
            'id_brand.exists' => 'Указанный бренд не существует'
        ];
    }
}
