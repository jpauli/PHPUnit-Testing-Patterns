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
* @subpackage Storage
* @author Julien Pauli <jpauli@php.net>
* @copyright 2010 Julien Pauli <jpauli@php.net>
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
*/

namespace TestingPractices\Storage;
use TestingPractices\Storage;

/**
* Session-based storage
*
* This class is designed to show how to use software architecture
* patterns to build maintenable and testable code.
*
* @package TestingPractices
* @subpackage Stockage
* @author Julien Pauli <jpauli@php.net>
* @copyright 2010 Julien Pauli <jpauli@php.net>
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
* @version Release: @package_version@
*/
class Session implements Storage
{
    /**
     * Has the session been started
     */
    private static bool $isStarted = false;

    /**
     * reference to &$_SESSION[namespace]
     */
    private array $session;

    public function __construct(private string $namespace = "default")
    {
        if (!self::$isStarted) {
            if (session_status() != \PHP_SESSION_ACTIVE) {
                session_start();
            }
            self::$isStarted = true;
        }
        if (!isset($_SESSION[$this->namespace])) {
            $_SESSION[$this->namespace] = [];
        }
        $this->session = &$_SESSION[$this->namespace];
    }

    /**
     * Storage interface
     */
    public function set(string $name, float $price): self
    {
        if (!isset($this->session[$name])) {
            $this->session[$name] = 0;
        }
        $this->session[$name] += abs($price);

        return $this;
    }

    /**
     * Storage interface
     */
    public function delete(string $name) : self
    {
        if (isset($this->session[$name])) {
            unset($this->session[$name]);
        }
        return $this;
    }

    /**
     * Storage interface
     */
    public function total() : float
    {
        return array_sum($this->session);
    }

    /**
     * Storage interface
     */
    public function emptyStorage() : void
    {
        $this->session = [];
    }
}
