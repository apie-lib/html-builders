<?php
namespace Apie\Tests\HtmlBuilders\Components\Layout;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\ValueObjects\DatabaseText;
use Apie\Fixtures\BoundedContextFactory;
use Apie\Fixtures\Entities\UserWithAddress;
use Apie\Fixtures\Identifiers\UserWithAddressIdentifier;
use Apie\Fixtures\ValueObjects\AddressWithZipcodeCheck;
use Apie\HtmlBuilders\Components\Layout\ShowProfile;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use PHPUnit\Framework\TestCase;

class ShowProfileTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_display_some_profile_fields()
    {
        $defaultConfiguration = new CurrentConfiguration([], new ApieContext(), BoundedContextFactory::createHashmap(), new BoundedContextId('default'));
        $user = new UserWithAddress(
            new AddressWithZipcodeCheck(
                new DatabaseText('Evergreen Terrace'),
                new DatabaseText('742'),
                new DatabaseText('11111'),
                new DatabaseText('Springfield'),
            ),
            new UserWithAddressIdentifier('d788c9f5-6493-4386-89f4-374be3b28764'),
        );
        $testItem = new ShowProfile(
            $defaultConfiguration,
            $user
        );
        $this->assertSame(
            $user,
            $testItem->getAttribute('user')
        );
        $this->assertNull(
            $testItem->getAttribute('email')
        );
        $this->assertEquals(
            '/default/resource/UserWithAddress/d788c9f5-6493-4386-89f4-374be3b28764',
            $testItem->getAttribute('profileUrl')
        );
        $this->assertEquals(
            'https://gravatar.com/avatar/d41d8cd98f00b204e9800998ecf8427e',
            $testItem->getAttribute('gravatarUrl')
        );
        $this->assertEquals(
            [
                'id'
            ],
            $testItem->getAttribute('fieldNames')
        );
        $this->assertEquals(
            [
                'id' => 'd788c9f5-6493-4386-89f4-374be3b28764',
            ],
            $testItem->getAttribute('fields')
        );
    }
}
