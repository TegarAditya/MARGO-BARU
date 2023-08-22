<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait CreatedUpdatedBy
{
    public static function bootCreatedUpdatedBy()
    {
        // updating created_by and updated_by when model is created
        static::creating(function (Model $model) {
            if (!$model->isDirty('created_by_id')) {
                $model->created_by_id = auth()->user()->id;
            }
            if (!$model->isDirty('updated_by_id')) {
                $model->updated_by_id = auth()->user()->id;
            }
        });

        // updating updated_by when model is updated
        static::updating(function (Model $model) {
            if (!$model->isDirty('updated_by_id')) {
                $model->updated_by_id = auth()->user()->id;
            }
        });
    }
}
