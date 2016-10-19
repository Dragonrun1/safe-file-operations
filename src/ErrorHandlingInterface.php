<?php
declare(strict_types = 1);
/**
 * Contains interface ErrorHandlingInterface.
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
 * Interface ErrorHandlingInterface.
 */
interface ErrorHandlingInterface
{
    const NO_ERROR = 0;
    const ACQUIRE_HANDLE_ERROR = 1;
    const BAD_PATH_OR_FILE_ERROR = 2;
    const BAD_WRITE_PATH_ERROR = 3;
    const DATA_READ_ERROR = 4;
    const DATA_WRITE_ERROR = 5;
    const DELETE_LIMIT_EXCEEDED_ERROR = 6;
    const LOCK_LIMITS_EXCEEDED_ERROR = 7;
    const READ_LIMITS_EXCEEDED_ERROR = 8;
    const REWIND_FILE_ERROR = 9;
    const UNREADABLE_FILE_ERROR = 10;
    const WRITE_FILE_ERROR = 11;
    const WRITE_LIMITS_EXCEEDED_ERROR = 12;
    /**
     * @return \Throwable
     */
    public function getFileError(): \Throwable;
    /**
     * @return bool
     */
    public function hasFileError(): bool;
}
