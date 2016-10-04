<?php
declare(strict_types = 1);
namespace Spec\SafeFileOperations;

use PhpSpec\ObjectBehavior;
use SafeFileOperations\SafeFileError;

/**
 * Class SafeFileErrorSpec
 *
 * @mixin \SafeFileOperations\SafeFileError
 *
 * @method void during($method, array $params)
 * @method void shouldBe($value)
 * @method void shouldContain($value)
 * @method void shouldNotEqual($value)
 * @method void shouldReturn($result)
 */
class SafeFileErrorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(SafeFileError::class);
    }
    public function let()
    {
        $this->beConstructedWith('');
    }
}
