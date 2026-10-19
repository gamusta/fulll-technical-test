<?php

declare(strict_types=1);

namespace Fulll\Algo;

final class FizzBuzz
{
    public function display(int $limit): void
    {
        for ($number = 1; $number <= $limit; ++$number) {
            echo $this->convert($number), PHP_EOL;
        }
    }

    public function convert(int $number): string
    {
        return match (true) {
            $number % 15 === 0 => 'FizzBuzz',
            $number % 3 === 0 => 'Fizz',
            $number % 5 === 0 => 'Buzz',
            default => (string) $number,
        };
    }
}
