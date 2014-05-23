<?php

use PHPUnit\Framework\TestCase;
use TestingPractices\Storage\Mock;
use TestingPractices\Product;
use TestingPractices\Cart;
use TestingPractices\Storage;

class MainTest extends TestCase
{
    private Product $product;
    private Storage $phpunitMockStorage;
    private Cart $cart;

    public function setUp() : void
    {
        $this->product            = new Product('foo', 5);
        $this->phpunitMockStorage = $this->createMock(Storage::CLASS);
        $this->cart               = new Cart($this->phpunitMockStorage);
    }

    public function testSelfMadeMockObjectRespond()
    {
        $this->cart->setStorage($mockStorage = new Mock);
        $this->cart->buy($this->product, 2);
        $this->assertEquals(2*5, $this->cart->invoice());
        $this->assertArrayHasKey('foo', $mockStorage->storage);
        $this->assertEquals(5*2, $mockStorage->storage['foo']);
    }

    public function testWithPHPUnitStubs()
    {
        $qty   = 10;
        $off   = 0;
        $price = 10;

        $this->product->setPrice($price);
        $this->phpunitMockStorage->expects($this->any())
                                   ->method('set')
                                   ->withAnyParameters()
                                   ->willReturn($this->phpunitMockStorage);
        $this->cart->buy($this->product, $qty, $off);
    }

    public function testWithPHPUnitSpies()
    {
        $this->phpunitMockStorage->expects($this->once())
                                   ->method('set');
        $this->cart->buy($this->product, 2);
    }

    public function testWithMultiplePHPUnitSpies()
    {
        $this->phpunitMockStorage->expects($this->once())
                                   ->method('set');

        $this->phpunitMockStorage->expects($this->never())
                                   ->method('delete');

        $this->phpunitMockStorage->expects($this->once())
                                   ->method('total');

        $this->cart->buy($this->product, 2);
        $this->cart->invoice();
    }

    /** @dataProvider dataList */
    public function testCheckOutWithSpiesAndDataProvider(float $price, int $discount, float $finalPrice)
    {
        $this->product->setPrice($price);

        $this->phpunitMockStorage->expects($this->once())
                                   ->method('set')
                                   ->with($this->product->getName(), $finalPrice);

        $this->cart->buy($this->product, 1, $discount);

        $this->phpunitMockStorage->expects($this->once())
                                   ->method('total')
                                   ->willReturn($finalPrice);

        $this->assertEquals($finalPrice, $this->cart->invoice());
    }

    public function dataList()
    {
        yield [30.0,50,15.0];
        yield [100.0,25,75.0];
    }
}