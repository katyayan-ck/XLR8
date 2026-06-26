<?php

namespace App\Imports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Utilities\EmpReporting\EmployeeTopicReporter;
use App\Models\Utilities\EmpReporting\ReportingTopic;
use Illuminate\Support\Facades\Log;

class TopicReportingSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        echo "\n🚀 Processing Topic Reporting Sheet...\n";

        foreach ($rows as $index => $row) {
            $this->processTopicRule($row->toArray(), $index + 2);
        }

        echo "✅ Topic Reporting Import Completed!\n";
    }

    private function processTopicRule(array $row, int $rowNumber): void
    {
        $employeeCode     = strtoupper(trim($row['employee_code'] ?? ''));
        $topicCode        = strtoupper(trim($row['topic_code'] ?? ''));
        $reportingToCode  = strtoupper(trim($row['reporting_to_code'] ?? ''));

        if (!$employeeCode || !$topicCode || !$reportingToCode) {
            echo "[Row {$rowNumber}] ⚠️  Skipped - Missing required fields\n";
            return;
        }

        // Validate topic exists
        $topic = ReportingTopic::where('code', $topicCode)->first();
        if (!$topic) {
            echo "[Row {$rowNumber}] ❌ Invalid Topic Code: {$topicCode}\n";
            return;
        }

        // Build scopes
        $scopes = [
            'branch'      => $this->cleanScope($row['branch'] ?? 'ALL'),
            'location'    => $this->cleanScope($row['location'] ?? 'ALL'),
            'segment'     => $this->cleanScope($row['segment'] ?? 'ALL'),
            'sub_segment' => $this->cleanScope($row['sub_segment'] ?? 'ALL'),
            'model'       => $this->cleanScope($row['model'] ?? 'ALL'),
            'department'  => $this->cleanScope($row['department'] ?? 'ALL'),
            'division'    => $this->cleanScope($row['division'] ?? 'ALL'),
            'vertical'    => $this->cleanScope($row['vertical'] ?? 'ALL'),
        ];

        // Build attributes (dynamic based on topic)
        $attributes = [];
        if (!empty($row['bargain_power']))   $attributes['bargain_power']   = (int) $row['bargain_power'];
        if (!empty($row['max_od_discount'])) $attributes['max_od_discount'] = (float) $row['max_od_discount'];
        if (!empty($row['threshold']))       $attributes['threshold']       = (float) $row['threshold'];
        if (!empty($row['upper_threshold'])) $attributes['upper_threshold'] = (float) $row['upper_threshold'];

        // Save or Update
        EmployeeTopicReporter::updateOrCreate(
            [
                'employee_code'    => $employeeCode,
                'topic_code'       => $topicCode,
                'reporting_to_code'=> $reportingToCode,
            ],
            [
                'scopes'     => $scopes,
                'attributes' => $attributes,
                'priority'   => (int)($row['priority'] ?? 0),
                'is_active'  => (bool)($row['is_active'] ?? true),
            ]
        );

        echo "[Row {$rowNumber}] ✅ Topic Rule Saved - {$employeeCode} → {$reportingToCode} ({$topicCode})\n";
    }

    private function cleanScope($value): string
    {
        $value = strtoupper(trim($value));
        return in_array($value, ['ALL', 'ANY', '']) ? 'ALL' : $value;
    }
}