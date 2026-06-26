<?php

namespace App\Services\EmpReporting;

use App\Models\Utilities\EmpReporting\EmployeeTopicReporter;
use App\Models\Admin\Employee;

class TopicReportingResolverService
{
    /**
     * Resolve the correct reporting person for a topic
     */
    public function resolve(string $employeeCode, string $topicCode, array $currentScopes = []): ?array
    {
        $rules = EmployeeTopicReporter::where('employee_code', $employeeCode)
            ->where('topic_code', $topicCode)
            ->where('is_active', true)
            ->orderByDesc('priority')
            ->get();

        if ($rules->isEmpty()) {
            return $this->getDefaultReportingManager($employeeCode);
        }

        foreach ($rules as $rule) {
            if ($this->scopesMatch($rule->scopes ?? [], $currentScopes)) {
                $manager = Employee::where('code', $rule->reporting_to_code)->first();

                return [
                    'reporting_to_code' => $rule->reporting_to_code,
                    'display_name'      => $manager?->display_name ?? $rule->reporting_to_code,
                    'source'            => 'topic_rule',
                    'topic_code'        => $topicCode,
                    'matched_scopes'    => $rule->scopes,
                    'attributes'        => $rule->attributes,
                ];
            }
        }

        return $this->getDefaultReportingManager($employeeCode);
    }

    private function getDefaultReportingManager(string $employeeCode): ?array
    {
        $employee = Employee::where('code', $employeeCode)->first();

        if (!$employee || !$employee->reporting_manager_code) {
            return null;
        }

        $manager = Employee::where('code', $employee->reporting_manager_code)->first();

        return [
            'reporting_to_code' => $employee->reporting_manager_code,
            'display_name'      => $manager?->display_name ?? $employee->reporting_manager_code,
            'source'            => 'default',
            'topic_code'        => null,
            'matched_scopes'    => null,
            'attributes'        => null,
        ];
    }

    private function scopesMatch(array $ruleScopes, array $currentScopes): bool
    {
        foreach ($ruleScopes as $key => $ruleValue) {
            if ($ruleValue === 'ALL') continue;

            $currentValue = $currentScopes[$key] ?? null;
            if ($currentValue === null || $currentValue !== $ruleValue) {
                return false;
            }
        }
        return true;
    }
}