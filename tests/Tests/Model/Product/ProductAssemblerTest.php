<?php

declare(strict_types=1);

namespace Tests\Tests\Model\Product;

use App\Model\Product\ProductAssembler;
use App\Model\Product\ProductDTO;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ProductAssemblerTest extends TestCase
{
    private ProductAssembler $assembler;

    protected function setUp(): void
    {
        $this->assembler = new ProductAssembler();
    }

    public function testCreateProductDtoListWithValidData(): void
    {
        $data = [
            'products' => [
                [
                    'width' => 10.5,
                    'height' => 20.3,
                    'length' => 15.7,
                    'weight' => 5.2,
                ],
                [
                    'width' => 8.0,
                    'height' => 12.0,
                    'length' => 10.0,
                    'weight' => 3.5,
                ],
            ],
        ];

        $result = $this->assembler->createProductDtoList($data);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(ProductDTO::class, $result);

        $this->assertEquals(10.5, $result[0]->width);
        $this->assertEquals(20.3, $result[0]->height);
        $this->assertEquals(15.7, $result[0]->length);
        $this->assertEquals(5.2, $result[0]->weight);

        $this->assertEquals(8.0, $result[1]->width);
        $this->assertEquals(12.0, $result[1]->height);
        $this->assertEquals(10.0, $result[1]->length);
        $this->assertEquals(3.5, $result[1]->weight);
    }

    public function testCreateProductDtoListWithSingleProduct(): void
    {
        $data = [
            'products' => [
                [
                    'width' => 5.0,
                    'height' => 5.0,
                    'length' => 5.0,
                    'weight' => 1.0,
                ],
            ],
        ];

        $result = $this->assembler->createProductDtoList($data);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(ProductDTO::class, $result[0]);
        $this->assertEquals(5.0, $result[0]->width);
    }

    public function testCreateProductDtoListWithEmptyProducts(): void
    {
        $data = [
            'products' => [],
        ];

        $result = $this->assembler->createProductDtoList($data);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testCreateProductDtoListWithMissingProductsKey(): void
    {
        $data = [];

        $result = $this->assembler->createProductDtoList($data);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testCreateProductDtoListThrowsExceptionWhenProductsIsNotArray(): void
    {
        $data = [
            'products' => 'not an array',
        ];

        try {
            $this->assembler->createProductDtoList($data);
            self::fail('Expected InvalidArgumentException was not thrown.');
        } catch (InvalidArgumentException $e) {
            self::assertEquals('Products data must be an array.', $e->getMessage());
        }
    }

    public function testCreateProductDtoListThrowsExceptionWhenSingleProductIsNotArray(): void
    {
        $data = [
            'products' => [
                'not an array',
            ],
        ];

        try {
            $this->assembler->createProductDtoList($data);
            self::fail('Expected InvalidArgumentException was not thrown.');
        } catch (InvalidArgumentException $e) {
            self::assertEquals('Single Product data must be an array.', $e->getMessage());
        }
    }

    /**
     * @param array<string, mixed> $productData
     */
    #[DataProvider('missingFieldsProvider')]
    public function testCreateProductDtoListThrowsExceptionWhenFieldIsMissing(
        array $productData,
        string $expectedMessage
    ): void {
        $data = ['products' => [$productData]];

        try {
            $this->assembler->createProductDtoList($data);
            self::fail('Expected InvalidArgumentException was not thrown.');
        } catch (InvalidArgumentException $e) {
            self::assertEquals($expectedMessage, $e->getMessage());
        }
    }

    /**
     * @return array<string, array{productData: array<string, mixed>, expectedMessage: string}>
     */
    public static function missingFieldsProvider(): array
    {
        return [
            'missing width' => [
                'productData' => [
                    'height' => 20.3,
                    'length' => 15.7,
                    'weight' => 5.2,
                ],
                'expectedMessage' => 'Width must be a float or int.',
            ],
            'missing height' => [
                'productData' => [
                    'width' => 10.5,
                    'length' => 15.7,
                    'weight' => 5.2,
                ],
                'expectedMessage' => 'Height must be a float or int.',
            ],
            'missing length' => [
                'productData' => [
                    'width' => 10.5,
                    'height' => 20.3,
                    'weight' => 5.2,
                ],
                'expectedMessage' => 'Length must be a float or int.',
            ],
            'missing weight' => [
                'productData' => [
                    'width' => 10.5,
                    'height' => 20.3,
                    'length' => 15.7,
                ],
                'expectedMessage' => 'Weight must be a float or int.',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $productData
     */
    #[DataProvider('validProductDataProvider')]
    public function testCreateProductDtoListWithVariousValidValues(
        array $productData,
        float $expectedWidth,
        float $expectedHeight,
        float $expectedLength,
        float $expectedWeight
    ): void {
        $data = ['products' => [$productData]];

        $result = $this->assembler->createProductDtoList($data);

        $this->assertCount(1, $result);
        $this->assertEquals($expectedWidth, $result[0]->width);
        $this->assertEquals($expectedHeight, $result[0]->height);
        $this->assertEquals($expectedLength, $result[0]->length);
        $this->assertEquals($expectedWeight, $result[0]->weight);
    }

    /**
     * @return array<string, array{productData: array<string, mixed>, expectedWidth: float, expectedHeight: float, expectedLength: float, expectedWeight: float}>
     */
    public static function validProductDataProvider(): array
    {
        return [
            'integer values' => [
                'productData' => [
                    'width' => 10,
                    'height' => 20,
                    'length' => 15,
                    'weight' => 5,
                ],
                'expectedWidth' => 10.0,
                'expectedHeight' => 20.0,
                'expectedLength' => 15.0,
                'expectedWeight' => 5.0,
            ],
            'string numeric values' => [
                'productData' => [
                    'width' => '10.5',
                    'height' => '20.3',
                    'length' => '15.7',
                    'weight' => '5.2',
                ],
                'expectedWidth' => 10.5,
                'expectedHeight' => 20.3,
                'expectedLength' => 15.7,
                'expectedWeight' => 5.2,
            ],
            'zero values' => [
                'productData' => [
                    'width' => 0.0,
                    'height' => 0.0,
                    'length' => 0.0,
                    'weight' => 0.0,
                ],
                'expectedWidth' => 0.0,
                'expectedHeight' => 0.0,
                'expectedLength' => 0.0,
                'expectedWeight' => 0.0,
            ],
            'large numbers' => [
                'productData' => [
                    'width' => 1000000.99,
                    'height' => 2000000.88,
                    'length' => 3000000.77,
                    'weight' => 5000000.66,
                ],
                'expectedWidth' => 1000000.99,
                'expectedHeight' => 2000000.88,
                'expectedLength' => 3000000.77,
                'expectedWeight' => 5000000.66,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $productData
     */
    #[DataProvider('invalidFieldValuesProvider')]
    public function testCreateProductDtoListThrowsExceptionWhenFieldValueIsInvalid(
        array $productData,
        string $expectedMessage
    ): void {
        $data = ['products' => [$productData]];

        try {
            $this->assembler->createProductDtoList($data);
            self::fail('Expected InvalidArgumentException was not thrown.');
        } catch (InvalidArgumentException $e) {
            self::assertEquals($expectedMessage, $e->getMessage());
        }
    }

    /**
     * @return array<string, array{productData: array<string, mixed>, expectedMessage: string}>
     */
    public static function invalidFieldValuesProvider(): array
    {
        return [
            'invalid width' => [
                'productData' => [
                    'width' => 'invalid',
                    'height' => 20.3,
                    'length' => 15.7,
                    'weight' => 5.2,
                ],
                'expectedMessage' => 'Width must be a float or int.',
            ],
            'invalid height' => [
                'productData' => [
                    'width' => 10.5,
                    'height' => 'invalid',
                    'length' => 15.7,
                    'weight' => 5.2,
                ],
                'expectedMessage' => 'Height must be a float or int.',
            ],
            'invalid length' => [
                'productData' => [
                    'width' => 10.5,
                    'height' => 20.3,
                    'length' => 'invalid',
                    'weight' => 5.2,
                ],
                'expectedMessage' => 'Length must be a float or int.',
            ],
            'invalid weight' => [
                'productData' => [
                    'width' => 10.5,
                    'height' => 20.3,
                    'length' => 15.7,
                    'weight' => 'invalid',
                ],
                'expectedMessage' => 'Weight must be a float or int.',
            ],
        ];
    }

    public function testCreateProductDtoListWithMixedProducts(): void
    {
        $data = [
            'products' => [
                [
                    'width' => 10.5,
                    'height' => 20.3,
                    'length' => 15.7,
                    'weight' => 5.2,
                ],
                [
                    'width' => '8',
                    'height' => '12',
                    'length' => '10',
                    'weight' => '3',
                ],
                [
                    'width' => 0.0,
                    'height' => 0.0,
                    'length' => 0.0,
                    'weight' => 0.0,
                ],
            ],
        ];

        $result = $this->assembler->createProductDtoList($data);

        $this->assertCount(3, $result);
        $this->assertContainsOnlyInstancesOf(ProductDTO::class, $result);
    }

    public function testCreateProductDtoListStopsAtFirstInvalidProduct(): void
    {
        $data = [
            'products' => [
                [
                    'width' => 10.5,
                    'height' => 20.3,
                    'length' => 15.7,
                    'weight' => 5.2,
                ],
                [
                    'width' => 'invalid',
                    'height' => 12.0,
                    'length' => 10.0,
                    'weight' => 3.5,
                ],
                [
                    'width' => 5.0,
                    'height' => 5.0,
                    'length' => 5.0,
                    'weight' => 1.0,
                ],
            ],
        ];

        try {
            $this->assembler->createProductDtoList($data);
            self::fail('Expected InvalidArgumentException was not thrown.');
        } catch (InvalidArgumentException $e) {
            self::assertEquals('Width must be a float or int.', $e->getMessage());
        }
    }

    public function testCreateProductDtoListWithExtraFields(): void
    {
        $data = [
            'products' => [
                [
                    'width' => 10.5,
                    'height' => 20.3,
                    'length' => 15.7,
                    'weight' => 5.2,
                    'extraField' => 'should be ignored',
                    'anotherField' => 123,
                ],
            ],
        ];

        $result = $this->assembler->createProductDtoList($data);

        $this->assertCount(1, $result);
        $this->assertEquals(10.5, $result[0]->width);
        $this->assertEquals(20.3, $result[0]->height);
        $this->assertEquals(15.7, $result[0]->length);
        $this->assertEquals(5.2, $result[0]->weight);
    }
}

