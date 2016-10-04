<?php
declare(strict_types = 1);
namespace Spec\SafeFileOperations;

use PhpSpec\ObjectBehavior;

/**
 * Class SafeFileHandlingTraitSpec
 *
 * @mixin \SafeFileOperations\SafeFileHandlingTrait
 *
 * @method void during($method, array $params)
 * @method void shouldBe($value)
 * @method void shouldContain($value)
 * @method void shouldNotEqual($value)
 * @method void shouldReturn($result)
 */
class SafeFileHandlingTraitSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(MockSafeFileHandlingTrait::class);
    }
    public function let()
    {
        $this->beAnInstanceOf('Spec\SafeFileOperations\MockSafeFileHandlingTrait');
    }
}
