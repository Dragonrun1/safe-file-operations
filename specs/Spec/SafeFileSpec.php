<?php
declare(strict_types = 1);
namespace Spec\SafeFileOperations;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use SafeFileOperations\SafeFile;
use SafeFileOperations\SafeFileError;
use SafeFileOperations\SafeFileHandlingInterface;

/**
 * Class SafeFileSpec
 *
 * @mixin \SafeFileOperations\SafeFile
 *
 * @method void during($method, array $params)
 * @method void shouldBe($value)
 * @method void shouldContain($value)
 * @method void shouldNotEqual($value)
 * @method void shouldReturn($result)
 */
class SafeFileSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(SafeFile::class);
    }
    public function it_throws_error_if_path_is_not_absolute()
    {
        try {
            $this->fileRead('a');
        } catch (SafeFileError $exc) {
            $expectedMess = 'Could not normalize path or file name';
            if ($expectedMess !== $exc->getMessage()) {
                $mess = sprintf('expected error message to be: "%1$s", but got "%2$s"', $expectedMess,
                    $exc->getMessage());
                throw new FailureException($mess);
            }
            if (SafeFileHandlingInterface::BAD_PATH_OR_FILE_ERROR !== $exc->getCode()) {
                $mess = sprintf('expected error code to be: "%1$s", but got "%2$s"',
                    SafeFileHandlingInterface::BAD_PATH_OR_FILE_ERROR,
                    $exc->getCode());
                throw new FailureException($mess);
            }
            return;
        } catch (\Throwable $throwable) {
            $mess = sprintf('expected error of class "\SafeFileOperations\SafeFileError", but got "%1$s"',
                get_class($throwable));
            throw new FailureException($mess);
        }
        throw new FailureException('expected error of class "\SafeFileOperations\SafeFileError", but no error thrown');
    }
    public function let()
    {
        $this->beConstructedWith(true);
    }
}
