<?php
declare(strict_types = 1);
/**
 * Contains SafeFileHandlingTrait Trait.
 *
 * PHP version 7.0+
 *
 * LICENSE:
 * This file is part of Yet Another Php Eve Api Library also know as Yapeal
 * which can be used to access the Eve Online API data and place it into a
 * database.
 * Copyright (C) 2015-2016 Michael Cummings
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
 * @copyright 2015-2016 Michael Cummings
 * @license   http://www.gnu.org/copyleft/lesser.html GNU LGPL
 * @author    Michael Cummings <mgcummings@yahoo.com>
 */
namespace SafeFileOperations;

use FilePathNormalizer\FilePathNormalizerInterface;
use FilePathNormalizer\FilePathNormalizerTrait;

/**
 * Trait SafeFileHandlingTrait
 */
trait SafeFileHandlingTrait
{
    use FilePathNormalizerTrait, SharedFileMethodsTrait;
    /**
     * Safely read contents of file.
     *
     * @param string $pathFile          File name with absolute path.
     * @param int    $estimatedFileSize This is used to calculate the read buffer size to use as well as the
     *                                  proportional timeouts.
     *
     * @return false|string Returns the file contents or false for any problems that prevent it.
     */
    public function fileRead(string $pathFile, int $estimatedFileSize = 16777216)
    {
        $this->setFileError(null);
        try {
            $pathFile = $this->getFpn()
                             ->normalizeFile($pathFile,
                                 FilePathNormalizerInterface::ABSOLUTE_REQUIRED
                                 | FilePathNormalizerInterface::WRAPPER_ALLOWED
                             );
        } catch (\Throwable $exc) {
            $this->setFileError(new SafeFileError('Could not normalize path or file name',
                ErrorHandlingInterface::BAD_PATH_OR_FILE_ERROR, $exc));
            return false;
        }
        // Insure file info is fresh.
        clearstatcache(true, $pathFile);
        if (!is_readable($pathFile) || !is_file($pathFile)) {
            $this->setFileError(new SafeFileError('Was not given accessible file',
                ErrorHandlingInterface::UNREADABLE_FILE_ERROR));
            return false;
        }
        return $this->safeDataRead($pathFile, $estimatedFileSize);
    }
    /**
     * Safely write file using lock and temp file.
     *
     * @param string $pathFile File name with absolute path.
     *
     * @param string $data     Contents to be written to file.
     *
     * @return bool Returns true if contents written, false on any problem that prevents write.
     */
    public function fileWrite(string $pathFile, string $data): bool
    {
        $this->setFileError(null);
        try {
            $pathFile = $this->getFpn()
                             ->normalizeFile($pathFile,
                                 FilePathNormalizerInterface::ABSOLUTE_REQUIRED
                                 | FilePathNormalizerInterface::WRAPPER_ALLOWED
                             );
        } catch (\Throwable $exc) {
            $this->setFileError(new SafeFileError('Could not normalize path or file name',
                ErrorHandlingInterface::BAD_PATH_OR_FILE_ERROR, $exc));
            return false;
        }
        $path = dirname($pathFile);
        $baseFile = basename($pathFile);
        if (false === $this->isWritablePath($path)) {
            $this->setFileError(new SafeFileError('Given non-writable path for file',
                ErrorHandlingInterface::BAD_PATH_OR_FILE_ERROR));
            return false;
        }
        if (false === $this->deleteWithRetry($pathFile)) {
            $this->setFileError(new SafeFileError('Could not delete file before re-writing',
                ErrorHandlingInterface::WRITE_FILE_ERROR, $this->fileError));
            return false;
        }
        $handle = $this->acquireLockedHandle($pathFile);
        if (false === $handle) {
            $this->setFileError(new SafeFileError('Could not acquire locked file handle before re-writing',
                ErrorHandlingInterface::WRITE_FILE_ERROR, $this->fileError));
            return false;
        }
        $tmpFile = sprintf('%1$s/%2$s.tmp', $path, hash('sha1', $baseFile . random_bytes(8)));
        if (false === $this->safeDataWrite($tmpFile, $data)) {
            $this->setFileError(new SafeFileError('Failed while writing to tmp file',
                ErrorHandlingInterface::WRITE_FILE_ERROR, $this->fileError));
            $this->releaseHandle($handle);
            return false;
        }
        $renamed = rename($tmpFile, $pathFile);
        $this->releaseHandle($handle);
        return $renamed;
    }
    /**
     * Reads data from the named file while insuring it either receives full contents or fails.
     *
     * Things that can cause read to fail:
     *
     *   * Unable to acquire exclusive file handle within calculated time or tries limits.
     *   * Read stalls without making any progress or repeatedly stalls to often.
     *   * Exceeds estimated read time based on file size with 2 second minimum enforced.
     *
     * @param string $pathFile          Name of file to try reading from.
     * @param int    $estimatedFileSize This is used to calculate the read
     *                                  buffer size to use as well as the
     *                                  proportional timeouts.
     *
     * @return false|string Returns contents of file or false for any errors that prevent it.
     */
    private function safeDataRead(string $pathFile, int $estimatedFileSize)
    {
        // Buffer size between 4KB and 256KB with 16MB value uses a 100KB buffer.
        $bufferSize = (int)(1 + floor(log($estimatedFileSize, 2))) << 12;
        // Read timeout calculated by estimated file size and write speed of
        // 16MB/sec with 2 second minimum time enforced.
        $timeout = max(2, intdiv($estimatedFileSize, 1 << 24));
        $handle = $this->acquireLockedHandle($pathFile, 'rb+', $timeout);
        if (false === $handle) {
            return false;
        }
        rewind($handle);
        $data = '';
        $tries = 0;
        $timeout = time() + $timeout;
        while (!feof($handle)) {
            if (++$tries > 10 || time() > $timeout) {
                $this->setFileError(new SafeFileError('Exceeded file reading time or try limit',
                    ErrorHandlingInterface::READ_LIMITS_EXCEEDED_ERROR));
                $this->releaseHandle($handle);
                return false;
            }
            $read = fread($handle, $bufferSize);
            // Decrease $tries while making progress but NEVER $tries < 1.
            if ('' !== $read && $tries > 0) {
                --$tries;
            }
            $data .= $read;
        }
        $this->releaseHandle($handle);
        return $data;
    }
    /**
     * Write the data to file name using randomized tmp file, exclusive locking, and time limits.
     *
     * Things that can cause write to fail:
     *
     *   * Unable to acquire exclusive file handle within calculated time or tries limits.
     *   * Write stalls without making any progress or repeatedly stalls to often.
     *   * Exceeds estimated write time based on file size with 2 second minimum enforced.
     *
     * @param string $pathFile File name with absolute path.
     *
     * @param string $data     Contents to be written to file.
     *
     * @return bool Returns true if contents written, false on any problem that prevents write.
     */
    private function safeDataWrite(string $pathFile, string $data): bool
    {
        $amountToWrite = strlen($data);
        // Buffer size between 4KB and 256KB with 16MB file size uses a 100KB buffer.
        $bufferSize = (int)(1 + floor(log($amountToWrite, 2))) << 12;
        // Write timeout calculated by using file size and write speed of
        // 16MB/sec with 2 second minimum time enforced.
        $timeout = max(2, intdiv($amountToWrite, 1 << 24));
        $handle = $this->acquireLockedHandle($pathFile, $timeout);
        if (false === $handle) {
            return false;
        }
        $dataWritten = 0;
        $tries = 0;
        $timeout = time() + $timeout;
        while ($dataWritten < $amountToWrite) {
            if (++$tries > 10 || time() > $timeout) {
                $this->setFileError(new SafeFileError('Exceeded file writing time or try limit',
                    ErrorHandlingInterface::READ_LIMITS_EXCEEDED_ERROR));
                $this->releaseHandle($handle);
                $this->deleteWithRetry($pathFile);
                return false;
            }
            $written = fwrite($handle, substr($data, $dataWritten, $bufferSize));
            // Decrease $tries while making progress but NEVER $tries <= 0.
            if ($written > 0 && $tries > 0) {
                --$tries;
            }
            $dataWritten += $written;
        }
        $this->releaseHandle($handle);
        return true;
    }
}
