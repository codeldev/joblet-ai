<?php

declare(strict_types=1);

/** @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection */
/** @noinspection PhpDuplicateMatchArmBodyInspection */

namespace App\Services\Notifications;

final class ExceptionNotificationService
{
    /**
     * @param  array<mixed,mixed>  $data
     * @return array<string,string>
     */
    public function buildReportData(array $data): array
    {
        return [
            'url'     => $this->toString(value: $data['url'] ?? 'Unknown'),
            'ip'      => $this->toString(value: $data['ip'] ?? 'Unknown'),
            'user'    => $this->toString(value: $data['user'] ?? 'Unknown'),
            'message' => $this->toString(value: $data['message'] ?? 'Unknown'),
            'file'    => $this->toString(value: $data['file'] ?? 'Unknown'),
            'line'    => $this->toString(value: $data['line'] ?? 'Unknown'),
        ];
    }

    /**
     * @param  array<mixed,mixed>  $traceData
     * @return array<int,string>
     */
    public function buildTraceData(array $traceData): array
    {
        /** @var array<int,string> $result */
        $result = array_map(
            callback: fn (mixed $traceLine): string => $this->getTraceLine(traceLine: $traceLine),
            array   : $traceData
        );

        return $result;
    }

    public function toString(mixed $value): string
    {
        return match (true)
        {
            is_string(value: $value)                                                                 => $value,
            is_scalar(value: $value)                                                                 => (string) $value,
            is_object(value: $value) && method_exists(object_or_class: $value, method: '__toString') => (string) $value,
            default                                                                                  => 'Unknown'
        };
    }

    private function getTraceLine(mixed $traceLine): string
    {
        return is_array(value: $traceLine)
            ? $this->buildTraceLine(traceLine: $traceLine)
            : '';
    }

    /** @param array<mixed,mixed> $traceLine */
    private function buildTraceLine(array $traceLine): string
    {
        /** @var array<string,string> $replaceData */
        $replaceData = [
            'class'    => $traceLine['class']    ?? '',
            'function' => $traceLine['function'] ?? '',
            'file'     => $traceLine['file']     ?? '',
            'line'     => $this->toString(value: $traceLine['line'] ?? ''),
        ];

        return trans(key: 'exception.email.trace.line', replace: $replaceData);
    }
}
