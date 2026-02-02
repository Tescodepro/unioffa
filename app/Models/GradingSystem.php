<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingSystem extends Model
{
    use HasFactory;

    protected $fillable = ['grade', 'min_score', 'max_score', 'point', 'description'];

    /**
     * Get the grade point for a given score.
     */
    public static function getPoint(float $score)
    {
        $grading = self::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();

        return $grading ? $grading->point : 0;
    }
}
