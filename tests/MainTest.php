<?php
use TestingPractices\Storage\Mock;
use TestingPractices\Product;
use TestingPractices\Cart;
use TestingPractices\Storage;
use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
    private $product;
    private $phpunitMockStorage;
    private $cart;
    private $mockStorage;

    public function setUp() : void
    {
        $this->product            = new Product('foo', 5);
        $this->phpunitMockStorage = $this->createMock(Storage::CLASS);
        $this->mockStorage        = new Mock();
        $this->cart               = new Cart();
    }

    public function testSelfMadeMockobjectRespond()
    {
        $this->cart->setStorage($this->mockStorage);
        $this->cart->buy($this->product, 2);
        $this->assertEquals(2*5, $this->cart->invoice());
        $this->assertArrayHasKey('foo', $this->mockStorage->stock);
        $this->assertEquals(5*2, $this->mockStorage->stock['foo']);
    }

    public function testWithPHPUnitStubs()
    {
        $qte   = 10;
        $reduc = 0;
        $prix  = 10;

        $this->product->setPrice($prix);
        $this->phpunitMockStorage->expects($this->any())
                                   ->method('set')
                                   ->withAnyParameters()
                                   ->willReturn($this->phpunitMockStorage);
        $this->cart->setStorage($this->phpunitMockStorage);
        $this->cart->buy($this->product, $qte, $reduc);
        $this->assertTrue(true); /* this test performs assertions */
    }

    public function testWithPHPUnitSpies()
    {
        $this->phpunitMockStorage->expects($this->once())
                                   ->method('set');
        $this->cart->setStorage($this->phpunitMockStorage);
        $this->cart->buy($this->product, 2);
    }

    public function testWithMultiplePHPUnitSpies()
    {
        $this->phpunitMockStorage->expects($this->once())
                                   ->method('set');

        $this->phpunitMockStorage->expects($this->never())
                                   ->method('delete');

        $this->phpunitMockStorage->expects($this->at(1))
                                   ->method('total');

        $this->cart->setStorage($this->phpunitMockStorage);
        $this->cart->buy($this->product, 2);
        $this->cart->invoice();
    }

    /** @dataProvider dataList */
    public function testCheckOutWithSpiesAndDataprovider($price, $discount, $finalPrice)
    {
        $this->product->setPrice($price);
        $this->cart->setStorage($this->phpunitMockStorage);

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
        yield [30,50,15];
        yield [100,25,75];
    }
}