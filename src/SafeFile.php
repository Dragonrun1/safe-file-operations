<?php
declare(strict_types = 1);
/**
 * Contains class SafeFile.
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
 * Class SafeFile.
 */
class SafeFile implements SafeFileHandlingInterface
{
    use SafeFileHandlingTrait;
    public function __construct(bool $exception = false)
    {
        $this->setException($exception);
    }
    public function fileRead(string $pathFile, int $estimatedFileSize = 16777216)
    {
        $safe = $this->safeFileRead($pathFile, $estimatedFileSize);
        if (false === $safe && $this->isException()) {
            throw $this->getSafeFileError();
        }
    }
    public function fileWrite(string $pathFile, string $data)
    {
        $safe = $this->safeFileWrite($pathFile, $data);
        if (false === $safe && $this->isException()) {
            throw $this->getSafeFileError();
        }
    }
    /**
     * @return bool
     */
    public function isException(): bool
    {
        return $this->exception;
    }
    /**
     * @param bool $value
     *
     * @return void
     */
    public function setException(bool $value = true)
    {
        $this->exception = $value;
    }
    /**
     * @var bool $exception
     */
    private $exception;
}
