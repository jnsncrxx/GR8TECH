<?php

namespace App\Services;

use App\Models\TaxBracket;
use Carbon\Carbon;

class TaxCalculationService
{
    public const PAY_FREQUENCY_MONTHLY = 'monthly';

    public const PAY_FREQUENCY_SEMI_MONTHLY = 'semi_monthly';

    /**
     * Calculate tax for a given income using dynamic tax brackets.
     *
     * Brackets in the database are stored as monthly TRAIN thresholds.
     * For semi-monthly cutoffs, thresholds and base tax are scaled ÷ 2 so
     * withholding is computed against semi-monthly taxable compensation.
     */
    public function calculateTax($income, $date = null, string $payFrequency = self::PAY_FREQUENCY_MONTHLY)
    {
        $date = $date ?: now();

        $taxBrackets = TaxBracket::active()
            ->forDate($date)
            ->ordered()
            ->get();

        if ($taxBrackets->isEmpty()) {
            return 0;
        }

        $applicableBracket = $this->findApplicableBracket($income, $taxBrackets, $payFrequency);

        if (! $applicableBracket) {
            return 0;
        }

        return $this->computeBracketTax($income, $applicableBracket, $payFrequency);
    }

    /**
     * Find the applicable tax bracket for a given income.
     */
    public function findApplicableBracket($income, $taxBrackets, string $payFrequency = self::PAY_FREQUENCY_MONTHLY)
    {
        foreach ($taxBrackets as $bracket) {
            if ($this->incomeFitsBracket($income, $bracket, $payFrequency)) {
                return $bracket;
            }
        }

        return null;
    }

    /**
     * Get tax breakdown for a given income.
     */
    public function getTaxBreakdown($income, $date = null, string $payFrequency = self::PAY_FREQUENCY_MONTHLY)
    {
        $date = $date ?: now();

        $taxBrackets = TaxBracket::active()
            ->forDate($date)
            ->ordered()
            ->get();

        $breakdown = [
            'income' => $income,
            'pay_frequency' => $payFrequency,
            'tax_amount' => 0,
            'effective_rate' => 0,
            'bracket_used' => null,
            'calculation_details' => [],
        ];

        if ($taxBrackets->isEmpty()) {
            return $breakdown;
        }

        $applicableBracket = $this->findApplicableBracket($income, $taxBrackets, $payFrequency);

        if ($applicableBracket) {
            $taxAmount = $this->computeBracketTax($income, $applicableBracket, $payFrequency);
            $effectiveRate = $income > 0 ? ($taxAmount / $income) * 100 : 0;
            $scaled = $this->scaledBracketValues($applicableBracket, $payFrequency);

            $breakdown['tax_amount'] = $taxAmount;
            $breakdown['effective_rate'] = $effectiveRate;
            $breakdown['bracket_used'] = [
                'id' => $applicableBracket->id,
                'name' => $applicableBracket->name,
                'min_income' => $scaled['min_income'],
                'max_income' => $scaled['max_income'],
                'tax_rate' => $applicableBracket->tax_rate,
                'base_tax' => $scaled['base_tax'],
                'excess_over' => $scaled['excess_over'],
            ];

            $breakdown['calculation_details'] = [
                'excess_amount' => $income - $scaled['excess_over'],
                'tax_on_excess' => ($income - $scaled['excess_over']) * ($applicableBracket->tax_rate / 100),
                'base_tax' => $scaled['base_tax'],
                'total_tax' => $taxAmount,
            ];
        }

        return $breakdown;
    }

    /**
     * Calculate tax for multiple income levels (useful for reports).
     */
    public function calculateTaxForRange($startIncome, $endIncome, $step = 1000, $date = null, string $payFrequency = self::PAY_FREQUENCY_MONTHLY)
    {
        $results = [];

        for ($income = $startIncome; $income <= $endIncome; $income += $step) {
            $results[] = [
                'income' => $income,
                'tax' => $this->calculateTax($income, $date, $payFrequency),
                'breakdown' => $this->getTaxBreakdown($income, $date, $payFrequency),
            ];
        }

        return $results;
    }

    /**
     * Get all active tax brackets for a given date.
     */
    public function getActiveTaxBrackets($date = null)
    {
        return TaxBracket::active()
            ->forDate($date)
            ->ordered()
            ->get();
    }

    /**
     * Resolve pay frequency from payroll period length.
     */
    public static function payFrequencyFromDaysInPeriod(int $daysInPeriod): string
    {
        return $daysInPeriod >= 25
            ? self::PAY_FREQUENCY_MONTHLY
            : self::PAY_FREQUENCY_SEMI_MONTHLY;
    }

    private function scaleFactor(string $payFrequency): float
    {
        return $payFrequency === self::PAY_FREQUENCY_SEMI_MONTHLY ? 0.5 : 1.0;
    }

    private function scaledBracketValues(TaxBracket $bracket, string $payFrequency): array
    {
        $scale = $this->scaleFactor($payFrequency);

        return [
            'min_income' => (float) $bracket->min_income * $scale,
            'max_income' => $bracket->max_income !== null ? (float) $bracket->max_income * $scale : null,
            'base_tax' => (float) $bracket->base_tax * $scale,
            'excess_over' => (float) $bracket->excess_over * $scale,
        ];
    }

    private function incomeFitsBracket(float $income, TaxBracket $bracket, string $payFrequency): bool
    {
        $scaled = $this->scaledBracketValues($bracket, $payFrequency);

        if ($income < $scaled['min_income']) {
            return false;
        }

        if ($scaled['max_income'] !== null && $income > $scaled['max_income']) {
            return false;
        }

        return true;
    }

    private function computeBracketTax(float $income, TaxBracket $bracket, string $payFrequency): float
    {
        if (! $this->incomeFitsBracket($income, $bracket, $payFrequency)) {
            return 0;
        }

        if ((float) $bracket->tax_rate == 0) {
            return 0;
        }

        $scaled = $this->scaledBracketValues($bracket, $payFrequency);
        $excess = $income - $scaled['excess_over'];
        $taxOnExcess = $excess * ((float) $bracket->tax_rate / 100);

        return round($scaled['base_tax'] + $taxOnExcess, 2);
    }

    /**
     * Create or update tax brackets for Philippine income tax.
     */
    public function createPhilippineTaxBrackets()
    {
        $brackets = [
            [
                'name' => 'Exempt',
                'description' => 'Up to ₱20,833 - Exempt from tax',
                'min_income' => 0,
                'max_income' => 20833,
                'tax_rate' => 0,
                'base_tax' => 0,
                'excess_over' => 0,
                'sort_order' => 1,
            ],
            [
                'name' => '15% Bracket',
                'description' => '₱20,834 – ₱33,333 - 15% of the excess over ₱20,833',
                'min_income' => 20834,
                'max_income' => 33333,
                'tax_rate' => 15,
                'base_tax' => 0,
                'excess_over' => 20833,
                'sort_order' => 2,
            ],
            [
                'name' => '20% Bracket',
                'description' => '₱33,334 – ₱66,667 - ₱1,875 + 20% of the excess over ₱33,333',
                'min_income' => 33334,
                'max_income' => 66667,
                'tax_rate' => 20,
                'base_tax' => 1875,
                'excess_over' => 33333,
                'sort_order' => 3,
            ],
            [
                'name' => '25% Bracket',
                'description' => '₱66,668 – ₱166,667 - ₱8,541.80 + 25% of the excess over ₱66,667',
                'min_income' => 66668,
                'max_income' => 166667,
                'tax_rate' => 25,
                'base_tax' => 8541.80,
                'excess_over' => 66667,
                'sort_order' => 4,
            ],
            [
                'name' => '30% Bracket',
                'description' => '₱166,668 – ₱666,667 - ₱33,541.80 + 30% of the excess over ₱166,667',
                'min_income' => 166668,
                'max_income' => 666667,
                'tax_rate' => 30,
                'base_tax' => 33541.80,
                'excess_over' => 166667,
                'sort_order' => 5,
            ],
            [
                'name' => '35% Bracket',
                'description' => 'Over ₱666,667 - ₱183,541.80 + 35% of the excess over ₱666,667',
                'min_income' => 666668,
                'max_income' => null,
                'tax_rate' => 35,
                'base_tax' => 183541.80,
                'excess_over' => 666667,
                'sort_order' => 6,
            ],
        ];

        foreach ($brackets as $bracketData) {
            TaxBracket::updateOrCreate(
                [
                    'name' => $bracketData['name'],
                    'min_income' => $bracketData['min_income'],
                ],
                array_merge($bracketData, [
                    'is_active' => true,
                    'effective_from' => now()->startOfYear(),
                ])
            );
        }
    }
}
