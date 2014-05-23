<?php
/**
* PHPUnit-Testing-Patterns
*
* Copyright (c) 2010, Julien Pauli <jpauli@php.net>.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions
* are met:
*
* * Redistributions of source code must retain the above copyright
* notice, this list of conditions and the following disclaimer.
*
* * Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in
* the documentation and/or other materials provided with the
* distribution.
*
* * Neither the name of Julien Pauli nor the names of his
* contributors may be used to endorse or promote products derived
* from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
* COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
* @package TestingPractices
* @author Julien Pauli <jpauli@php.net>
* @copyright 2010 Julien Pauli <jpauli@php.net>
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
*/

namespace TestingPractices;

/**
* Cart class
*
* This class is designed to show how to use software architecture
* patterns to build maintenable and testable code.
*
* @package TestingPractices
* @author Julien Pauli <jpauli@php.net>
* @copyright 2010 Julien Pauli <jpauli@php.net>
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
* @version Release: @package_version@
*/
class Cart
{
    public function __construct(private Storage $storage)
    {
    }

    /**
     * Setter for Storage object
     */
    public function setStorage(Storage $storage) : self
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Getter for Storage object
     * @throws \LogicException
     */
    public function getStorage() : Storage
    {
        return $this->storage;
    }

    /**
     * Buy some products
     */
    public function buy(Sellable $product, int $qty, int $discount = 0) : self
    {
        $discount = abs($discount);
        if ($discount > 100) {
            $discount = 100;
        }
        $this->getStorage()->set($product->getName(), $qty * ($product->getPrice() - $product->getPrice()*$discount/100));

        return $this;
    }

    /**
     * Give back a product
     */
    public function unBuy(Product $p) : self
    {
        $this->getStorage()->delete($p->getName());

        return $this;
    }

    /**
     * Get the total amount
     */
    public function invoice() : float
    {
        return $this->getStorage()->total();
    }

    /**
     * Empty the cart
     */
    public function emptyCart() : self
    {
        $this->getStockage->emptyStorage();

        return $this;
    }
}