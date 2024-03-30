<?php

declare(strict_types=1);

namespace App\Services\Decimal;

use JsonSerializable;

class Decimal implements JsonSerializable
{
    public const VALUE = 'value';
    public const PRECISION = 'precision';

    private const MAX_PRECISION = 8;

    private $value;

    private $precision;

    public function __construct(string $value, int $precision = self::MAX_PRECISION)
    {
        $this->value = number_format((float) $value, $precision, '.', '');
        $this->precision = $precision;
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function withMaxPrecision(): self
    {
        $copy = clone $this;

        $copy->precision = self::MAX_PRECISION;

        return $copy;
    }

    public function withPrecision(int $precision): self
    {
        $copy = clone $this;

        $copy->precision = $precision;

        return $copy;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function round(int $precision): string
    {
        return bcadd($this->value, '0', $precision);
    }

    public function add(Decimal $value): self
    {
        return new static(bcadd($this->value, $value->getValue(), $this->precision), $this->precision);
    }

    public function sub(Decimal $value): self
    {
        return new static(bcsub($this->value, $value->getValue(), $this->precision), $this->precision);
    }

    public function multiply(Decimal $value): self
    {
        return new static(bcmul($this->value, $value->getValue(), $this->precision), $this->precision);
    }

    public function divide(Decimal $value): self
    {
        if ($value->isZero()) {
            return $value;
        }

        return new static(bcdiv($this->value, $value->getValue(), $this->precision), $this->precision);
    }

    public function min(Decimal ...$decimals): self
    {
        $min = $this;

        foreach ($decimals as $decimal) {
            if ($decimal->less($min)) {
                $min = $decimal;
            }
        }

        return $min;
    }

    public function max(Decimal ...$decimals): self
    {
        $max = $this;

        foreach ($decimals as $decimal) {
            if ($decimal->greater($max)) {
                $max = $decimal;
            }
        }

        return $max;
    }

    public function less(Decimal $decimal): bool
    {
        return bccomp($this->getValue(), $decimal->getValue(), $this->precision) === -1;
    }

    public function greater(Decimal $decimal): bool
    {
        return bccomp($this->getValue(), $decimal->getValue(), $this->precision) === 1;
    }

    public function equals(Decimal $decimal): bool
    {
        return bccomp($this->getValue(), $decimal->getValue(), $this->precision) === 0;
    }

    public function percent(Decimal $percent): self
    {
        $copy = clone $this;

        return $copy->multiply($percent->withMaxPrecision()->divide(new Decimal('100')));
    }

    public function shortened(): string
    {
        $divisor = '1';
        $suffix = '';

        if (bccomp($this->value, '1000000') >= 0) {
            $divisor = '1000000';
            $suffix = 'm';
        } elseif (bccomp($this->value, '1000') >= 0) {
            $divisor = '1000';
            $suffix = 'k';
        }

        return bcdiv($this->value, $divisor) . $suffix;
    }

    public function isZero(): bool
    {
        return $this->equals(new self('0'));
    }

    public function jsonSerialize()
    {
        return [
            self::VALUE => $this->value,
            self::PRECISION => $this->precision,
        ];
    }

    public function __toString(): string
    {
        return rtrim(rtrim(number_format((float) $this->value, $this->precision, '.', ' '), '0'), '.');
    }
}
