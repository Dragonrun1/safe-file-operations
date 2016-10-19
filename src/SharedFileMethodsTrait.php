<?php
declare(strict_types = 1);
/**
 * Contains trait SharedFileMethodsTrait.
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
 * Trait SharedFileMethodsTrait.
 */
trait SharedFileMethodsTrait
{
    use ErrorHandlingTrait;
    /**
     * Used to acquire a exclusively locked file handle with a given mode and time limit.
     *
     * @param string $pathFile Name of file locked file handle is for.
     * @param string $mode     Mode to open handle with. Default will create
     *                         the file if it does not exist. 'b' option should
     *                         always be used to insure cross OS compatibility.
     * @param int    $timeout  Time it seconds used while trying to get lock.
     *                         Will be internally limited between 2 and 16
     *                         seconds.
     *
     * @return false|resource Returns exclusively locked file handle resource or false on errors.
     */
    protected function acquireLockedHandle(string $pathFile, string $mode = 'cb+', int $timeout = 2)
    {
        $this->setFileError(null);
        $handle = fopen($pathFile, $mode, false);
        if (false === $handle) {
            $this->setFileError(new SafeFileError('Could not get file handle',
                ErrorHandlingInterface::ACQUIRE_HANDLE_ERROR));
            return false;
        }
        if (false === $this->acquiredLock($handle, $timeout)) {
            $this->releaseHandle($handle);
            return false;
        }
        return $handle;
    }
    /**
     * Used to acquire file handle lock that limits the time and number of tries to do so.
     *
     * @param resource $handle  File handle to acquire exclusive lock for.
     * @param int      $timeout Maximum time in seconds to wait for lock.
     *                          Internally limited between 2 and 16 seconds.
     *                          Also determines how many tries to make.
     *
     * @return bool
     */
    protected function acquiredLock($handle, int $timeout = 2): bool
    {
        $timeout = min(16, max(2, $timeout));
        // Give max of $timeout seconds or 2 * $timeout tries to getting lock.
        $maxTries = 2 * $timeout;
        $minWait = 50000;
        $maxWait = 700000;
        $timeout = time() + $timeout;
        $tries = 0;
        while (!flock($handle, LOCK_EX | LOCK_NB)) {
            $wait = random_int($minWait, $maxWait);
            if (++$tries > $maxTries || (time() + $wait) > $timeout) {
                $this->setFileError(new SafeFileError('Exceeded exclusive lock time or try limit',
                    ErrorHandlingInterface::LOCK_LIMITS_EXCEEDED_ERROR));
                return false;
            }
            // Randomized to help prevent deadlocks.
            usleep($wait);
        }
        return true;
    }
    /**
     * Used to delete a file when unlink might fail and it needs to be retried.
     *
     * @param string $pathFile File name with absolute path.
     *
     * @return bool
     */
    protected function deleteWithRetry(string $pathFile): bool
    {
        clearstatcache(true, $pathFile);
        if (!is_file($pathFile)) {
            return true;
        }
        // Acquire exclusive access to file to help prevent conflicts when deleting.
        $handle = $this->acquireLockedHandle($pathFile, 'rb+');
        $tries = 0;
        do {
            if (is_resource($handle)) {
                ftruncate($handle, 0);
                rewind($handle);
                flock($handle, LOCK_UN);
                fclose($handle);
            }
            if (++$tries > 10) {
                $this->setFileError(new SafeFileError('Exceeded delete file try limit',
                    ErrorHandlingInterface::DELETE_LIMIT_EXCEEDED_ERROR));
                return false;
            }
            // Wait 0.01 to 0.5 seconds before trying again.
            usleep(random_int(10000, 500000));
        } while (false === unlink($pathFile));
        clearstatcache(true, $pathFile);
        return true;
    }
    /**
     * Checks that path is readable, writable, and a directory.
     *
     * @param string $path Absolute path to be checked.
     *
     * @return bool Return true for writable directory else false.
     */
    protected function isWritablePath(string $path): bool
    {
        clearstatcache(true, $path);
        return is_readable($path) && is_dir($path) && is_writable($path);
    }
    /**
     * @param resource $handle
     *
     * @return void
     */
    protected function releaseHandle($handle)
    {
        if (is_resource($handle)) {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
