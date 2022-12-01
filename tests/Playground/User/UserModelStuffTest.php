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

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Model\Collection;
use Contao\TestCase\ContaoTestCase;
use Contao\UserModel;
use Markocupic\ContaoTestCasePlayground\Playground\User\UserModelStuff;

class UserModelStuffTest extends ContaoTestCase
{
    public function testGetName(): void
    {
        $model = $this->mockClassWithProperties(UserModel::class);
        $model->name = 'Marko Cupic';

        $adapter = $this->mockAdapter(['findOneBy', 'findByUsername']);
        $adapter
            ->method('findByUsername')
            ->willReturn($model)
        ;

        $adapter
            ->method('findOneBy')
            ->willReturn($model)
        ;

        $framework = $this->createMock(ContaoFramework::class);
        $framework
            ->expects($this->atLeastOnce())
            ->method('getAdapter')
            ->with(UserModel::class)
            ->willReturn($adapter)
        ;

        $userModelStuff = new UserModelStuff($framework);

        $this->assertSame('Marko Cupic', $userModelStuff->getName('markocupic'));
    }

    public function testCountUsers(): void
    {
        // Create user models
        $userModels = $this->getContaoUsers();

        // All users
        $collection = new Collection([...$userModels], 'tl_user');

        $adapter = $this->mockAdapter(['findAll']);
        $adapter
            ->method('findAll')
            ->willReturn($collection, null)
        ;

        $framework = $this->createMock(ContaoFramework::class);
        $framework
            ->expects($this->atLeastOnce())
            ->method('getAdapter')
            ->with(UserModel::class)
            ->willReturn($adapter)
        ;

        $userModelStuff = new UserModelStuff($framework);

        $this->assertSame(3, $userModelStuff->countUsers());
    }

    public function testCountAdmins(): void
    {
        // Create user models
        $userModels = $this->getContaoUsers();

        // Admins only
        $collection = new Collection([$userModels[0], $userModels[1]], 'tl_user');

        $adapter = $this->mockAdapter(['findBy']);
        $adapter
            ->method('findBy')
            ->with('admin', '1')
            ->willReturn($collection, null)
        ;

        $framework = $this->createMock(ContaoFramework::class);
        $framework
            ->expects($this->atLeastOnce())
            ->method('getAdapter')
            ->with(UserModel::class)
            ->willReturn($adapter)
        ;

        $userModelStuff = new UserModelStuff($framework);

        $this->assertSame(2, $userModelStuff->countAdmins());
    }

    private function mockContaoUser(int $id, string $username, string $name, bool $isAdmin): UserModel
    {
        $userModel = $this->mockClassWithProperties(UserModel::class);
        $userModel->id = $id;
        $userModel->username = $username;
        $userModel->name = $name;
        $userModel->admin = true === $isAdmin ? '1' : '';

        $userModel
            ->method('row')
            ->willReturn((array) $userModel)
        ;

        return $userModel;
    }

    private function getContaoUsers(): array
    {
        $userModels = [];
        $userModels[] = $this->mockContaoUser(1, 'fritznimmersatt', 'Fritz Nimmersatt', true);
        $userModels[] = $this->mockContaoUser(1, 'arnie', 'Arnold Schwarzenegger', true);
        $userModels[] = $this->mockContaoUser(1, 'jamesbond', 'James Bond', false);

        return $userModels;
    }
}
