<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Component\User\Security\Generator;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\User\Security\Checker\UniquenessCheckerInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Sylius\Component\User\Security\Generator\UniqueTokenGenerator;
use Sylius\Resource\Generator\RandomnessGeneratorInterface;

final class UniqueTokenGeneratorTest extends TestCase
{
    /** @var RandomnessGeneratorInterface&MockObject */
    private MockObject $generatorMock;

    /** @var UniquenessCheckerInterface&MockObject */
    private MockObject $checkerMock;

    private GeneratorInterface $uniqueTokenGenerator;

    protected function setUp(): void
    {
        $this->generatorMock = $this->createMock(RandomnessGeneratorInterface::class);
        $this->checkerMock = $this->createMock(UniquenessCheckerInterface::class);
        $this->uniqueTokenGenerator = new UniqueTokenGenerator($this->generatorMock, $this->checkerMock, 12);
    }

    public function testShouldImplementGeneratorInterface(): void
    {
        $this->assertInstanceOf(GeneratorInterface::class, $this->uniqueTokenGenerator);
    }

    public function testShouldThrowInvalidArgumentExceptionOnInstantiationWithAnOutOfRangeLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UniqueTokenGenerator($this->generatorMock, $this->checkerMock, -1);
        $this->expectException(InvalidArgumentException::class);
        new UniqueTokenGenerator($this->generatorMock, $this->checkerMock, 0);
    }

    public function testShouldGenerateTokensWithLengthStatedOnInstantiation(): void
    {
        $token = 'vanquishable';

        $this->generatorMock->expects($this->once())->method('generateUriSafeString')->with(12)->willReturn($token);
        $this->checkerMock->expects($this->once())->method('isUnique')->with($token)->willReturn(true);

        $this->uniqueTokenGenerator->generate();
    }

    public function testShouldGenerateStringTokens(): void
    {
        $token = 'vanquishable';

        $this->generatorMock->expects($this->once())->method('generateUriSafeString')->with(12)->willReturn($token);
        $this->checkerMock->expects($this->once())->method('isUnique')->with($token)->willReturn(true);

        $this->assertIsString($this->uniqueTokenGenerator->generate());
    }
}
