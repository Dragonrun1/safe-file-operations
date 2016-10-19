<?php
declare(strict_types = 1);
/**
 * Contains class ErrorHandlingTraitSpec.
 *
 * PHP version 7.0+
 *
 * LICENSE:
 * This file is part of Safe File Operations which tries to proved as completely safe read and write file operations as
 * possible.
 * Copyright (C) 2016 Michael Cummings
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see
 * <http://spdx.org/licenses/LGPL-3.0.html>.
 *
 * You should be able to find a copy of this license in the COPYING-LESSER.md
 * file. A copy of the GNU GPL should also be available in the COPYING.md file.
 *
 * @author    Michael Cummings <mgcummings@yahoo.com>
 * @copyright 2016 Michael Cummings
 * @license   LGPL-3.0+
 */
namespace Spec\SafeFileOperations;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use SafeFileOperations\SafeFileError;

/**
 * Class ErrorHandlingTraitSpec.
 *
 * @mixin \SafeFileOperations\ErrorHandlingTrait
 * @mixin \Spec\SafeFileOperations\MockErrorHandling
 *
 * @method void during($method, array $params)
 * @method void shouldBe($value)
 * @method void shouldContain($value)
 * @method void shouldNotEqual($value)
 * @method void shouldReturn($result)
 */
class ErrorHandlingTraitSpec extends ObjectBehavior
{
    /**
     *
     * @throws \PhpSpec\Exception\Example\FailureException
     */
    public function it_returns_default_safe_file_error_initially()
    {
        /**
         * @var \SafeFileOperations\SafeFileError $result
         */
        $result = $this->getFileError()->shouldBeAnInstanceOf('\SafeFileOperations\SafeFileError');
        if (!('' === $result->getMessage() && 0 === $result->getCode())) {
            $mess = 'Bad default Safe File Error';
            throw new FailureException($mess);
        }
    }
    /**
     *
     */
    public function let()
    {
        $this->beAnInstanceOf('\Spec\SafeFileOperations\MockErrorHandling');
    }
    public function it_should_return_false_from_has_file_error_initially()
    {
        $this->hasFileError()->shouldReturn(false);
    }
    public function it_should_return_same_throwable_back_that_it_is_given()
    {
        $throwable = new SafeFileError('test error', 1);
        $this->setError($throwable);
        $this->getFileError()->shouldReturn($throwable);
    }
    public function it_should_return_true_from_has_file_error_once_one_is_set()
    {
        $throwable = new SafeFileError('test error', 1);
        $this->setError($throwable);
        $this->hasFileError()->shouldReturn(true);
    }
}
