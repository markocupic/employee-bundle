<?php

declare(strict_types=1);

/*
 * This file is part of Contao Test Case Playground.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-test-case-playground
 */

namespace Markocupic\ContaoTestCasePlayground\Tests\Playground\User;

use Contao\TestCase\ContaoTestCase;
use Doctrine\DBAL\Connection;
use Markocupic\ContaoTestCasePlayground\Playground\User\DbalStuff;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class DbalStuffTest extends ContaoTestCase
{
    public function testGetName(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('fetchOne')
            ->willReturn('John Doe')
        ;

        $requestStack = $this->createMock(RequestStack::class);

        $dbalStuff = new DbalStuff($connection, $requestStack);

        $this->assertSame('John Doe', $dbalStuff->getName('john_doe'));
    }

    public function testGetNameFromRequest(): void
    {
        $requests = [];
        $requests[] = Request::create(
            'https://example.com/foobar/index.php?username=john_doe',
            'GET',
            [],
            [],
            [],
            [
                'SCRIPT_FILENAME' => '/foobar/index.php',
                'SCRIPT_NAME' => '/foobar/index.php',
            ]
        );

        $requests[] = Request::create(
            'https://example.com/foobar/index.php?name=john_doe',
            'GET',
            [],
            [],
            [],
            [
                'SCRIPT_FILENAME' => '/foobar/index.php',
                'SCRIPT_NAME' => '/foobar/index.php',
            ]
        );

        $returnValues = [
            [
                'id' => 1,
                'username' => 'john_doe',
                'name' => 'John Doe',
            ],
            false,
        ];

        $connections = [];

        foreach ($requests as $i => $request) {
            $requestStack = $this->createMock(RequestStack::class);
            $requestStack
                ->method('getCurrentRequest')
                ->willReturn($request)
            ;

            $connections[] = $this->createMock(Connection::class);
            $connections[$i]
                ->method('fetchAssociative')
                ->willReturn($returnValues[$i])
            ;

            $dbalStuff = new DbalStuff($connections[$i], $requestStack);

            $this->assertSame($returnValues[$i], $dbalStuff->getNameFromRequest());
        }
    }
}
