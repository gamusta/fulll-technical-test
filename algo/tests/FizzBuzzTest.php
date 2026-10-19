<?php

declare(strict_types=1);

namespace Fulll\Algo\Tests;

use Fulll\Algo\FizzBuzz;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FizzBuzzTest extends TestCase
{
    /**
     * @return array<string, array{int, string}>
     */
    public static function numbers(): array
    {
        return [
            'not divisible' => [7, '7'],
            'divisible by 3' => [3, 'Fizz'],
            'divisible by 5' => [5, 'Buzz'],
            'divisible by 3 and 5' => [15, 'FizzBuzz'],
            'multiple of 15' => [30, 'FizzBuzz'],
        ];
    }

    #[DataProvider('numbers')]
    public function testConvert(int $number, string $expected): void
    {
        self::assertSame($expected, (new FizzBuzz())->convert($number));
    }

    public function testDisplayPrintsOneLinePerNumberFromOneToLimit(): void
    {
        $this->expectOutputString("1\n2\nFizz\n4\nBuzz\nFizz\n7\n8\nFizz\nBuzz\n11\nFizz\n13\n14\nFizzBuzz\n");

        (new FizzBuzz())->display(15);
    }
}
