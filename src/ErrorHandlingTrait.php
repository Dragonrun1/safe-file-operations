<?php
declare(strict_types = 1);
/**
 * Contains trait ErrorHandlingTrait.
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
namespace SafeFileOperations;

/**
 * Trait ErrorHandlingTrait.
 */
trait ErrorHandlingTrait
{
    /**
     * @return \Throwable
     */
    public function getFileError(): \Throwable
    {
        if (null === $this->fileError) {
            $this->fileError = new SafeFileError('', ErrorHandlingInterface::NO_ERROR);
        }
        return $this->fileError;
    }
    /**
     * @return bool
     */
    public function hasFileError(): bool
    {
        $code = $this->getFileError()
                     ->getCode();
        $message = $this->getFileError()
                        ->getMessage();
        return ErrorHandlingInterface::NO_ERROR !== $code || '' !== $message;
    }
    /**
     * @param \Throwable|null $value
     */
    private function setFileError(\Throwable $value = null)
    {
        $this->fileError = $value;
    }
    /**
     * @var \Throwable|null $fileError
     */
    private $fileError;
}
