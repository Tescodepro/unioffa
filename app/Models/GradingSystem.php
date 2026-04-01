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
        // We order by min_score DESC and take the first one where min_score <= score
        // This handles cases where score might be between ranges (e.g. 69.5)
        // by falling through to the next lowest valid range.
        $grading = self::where('min_score', '<=', $score)
            ->orderBy('min_score', 'desc')
            ->first();

        return $grading ? $grading->point : 0;
    }
}
