<?php

namespace Application\Services;

class MathEvaluator
{
    /**
     * محاسبه یک عبارت ریاضی با متغیرها
     * 
     * مثال: "{base} * {card_mult} + {bonus}" با متغیرهای ['base' => 3, 'card_mult' => 2, 'bonus' => 5]
     * نتیجه: 11
     */
    public static function evaluate(string $expression, array $variables = []): float
    {
        // جایگزینی متغیرها
        foreach ($variables as $key => $value) {
            $expression = str_replace('{' . $key . '}', (string) (float) $value, $expression);
        }

        // اعتبارسنجی: فقط اعداد، عملگرها و پرانتز مجاز هستند
        if (!preg_match('/^[\d\s\+\-\*\/\.\(\)]+$/', $expression)) {
            throw new \InvalidArgumentException("عبارت ریاضی نامعتبر: {$expression}");
        }

        // محاسبه با استفاده از eval (امن به دلیل اعتبارسنجی بالا)
        try {
            $result = 0;
            eval('$result = ' . $expression . ';');
            return (float) $result;
        } catch (\Throwable $e) {
            throw new \RuntimeException("خطا در محاسبه فرمول: " . $e->getMessage());
        }
    }

    /**
     * تست یک فرمول با متغیرهای نمونه
     */
    public static function testFormula(string $expression, array $sampleVariables = []): array
    {
        try {
            $result = self::evaluate($expression, $sampleVariables);
            return [
                'success' => true,
                'result' => $result,
                'expression' => $expression,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'expression' => $expression,
            ];
        }
    }
}