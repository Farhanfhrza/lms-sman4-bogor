<?php

namespace App\Traits;

trait CalculatesProgress
{
    /**
     * Update the progress completion percentage.
     *
     * @param int $percentage
     * @return void
     */
    public function updateCompletion(int $percentage)
    {
        $this->completion_percentage = min(100, max(0, $percentage));
        $this->is_completed = $this->completion_percentage >= 100;
        
        if ($this->is_completed && $this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'completed_at')) {
            $this->completed_at = now();
        }
        
        $this->save();
    }
}
