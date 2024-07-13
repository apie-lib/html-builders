<?php
namespace Apie\HtmlBuilders\TestHelpers;

use Apie\Common\ActionDefinitions\CreateResourceActionDefinition;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\Dto\ValueOption;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\Lists\StringList;
use Apie\Core\Lists\ValueOptionList;
use Apie\Core\Translator\ApieTranslator;
use Apie\Core\Translator\ApieTranslatorInterface;
use Apie\Core\ValueObjects\DatabaseText;
use Apie\Fixtures\BoundedContextFactory;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\UserWithAddress;
use Apie\Fixtures\Entities\UserWithAutoincrementKey;
use Apie\Fixtures\Enums\Gender;
use Apie\Fixtures\Identifiers\OrderIdentifier;
use Apie\Fixtures\Identifiers\UserWithAddressIdentifier;
use Apie\Fixtures\Lists\OrderLineList;
use Apie\Fixtures\ValueObjects\AddressWithZipcodeCheck;
use Apie\Fixtures\ValueObjects\Password as StrongPassword;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\Components\Forms\Checkbox;
use Apie\HtmlBuilders\Components\Forms\Csrf;
use Apie\HtmlBuilders\Components\Forms\Form;
use Apie\HtmlBuilders\Components\Forms\FormGroup;
use Apie\HtmlBuilders\Components\Forms\FormPrototypeHashmap;
use Apie\HtmlBuilders\Components\Forms\FormPrototypeList;
use Apie\HtmlBuilders\Components\Forms\FormSplit;
use Apie\HtmlBuilders\Components\Forms\HiddenField;
use Apie\HtmlBuilders\Components\Forms\HtmlField;
use Apie\HtmlBuilders\Components\Forms\Input;
use Apie\HtmlBuilders\Components\Forms\InputWithAutocomplete;
use Apie\HtmlBuilders\Components\Forms\MultiSelect;
use Apie\HtmlBuilders\Components\Forms\Password;
use Apie\HtmlBuilders\Components\Forms\RemoveConfirm;
use Apie\HtmlBuilders\Components\Forms\Select;
use Apie\HtmlBuilders\Components\Forms\VerifyOtpInput;
use Apie\HtmlBuilders\Components\Layout;
use Apie\HtmlBuilders\Components\Layout\BoundedContextSelect;
use Apie\HtmlBuilders\Components\Layout\LoginSelect;
use Apie\HtmlBuilders\Components\Layout\Logo;
use Apie\HtmlBuilders\Components\Layout\ShowProfile;
use Apie\HtmlBuilders\Components\Resource\Detail;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\BooleanDisplay;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\LinkDisplay;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\ListDisplay;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\NullDisplay;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\SegmentDisplay;
use Apie\HtmlBuilders\Components\Resource\FilterColumns;
use Apie\HtmlBuilders\Components\Resource\Overview;
use Apie\HtmlBuilders\Components\Resource\ResourceActionList;
use Apie\HtmlBuilders\Components\Resource\SingleResourceActionList;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Apie\HtmlBuilders\Lists\ActionList;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ResourceActions\CreateResourceAction;
use Apie\HtmlBuilders\ValueObjects\FormName;
use Apie\OtpValueObjects\HOTPSecret;
use Generator;
use OTPHP\HOTP;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractRenderTestCase extends TestCase
{
    abstract public function getRenderer(): ComponentRendererInterface;

    abstract public function getFixturesPath(): string;

    /**
     * Overwriting this method to return true means the fixtures will be overwritten to ease big refactorings.
     */
    protected function shouldOverwriteFixture(): bool
    {
        return true;//false;
    }

    /**
     * @dataProvider provideComponents
     */
    public function testRender(string $expectedFixtureFile, ComponentInterface $component): void
    {
        $renderer = $this->getRenderer();
        $context = new ApieContext([
            ApieTranslatorInterface::class => new ApieTranslator(),
        ]);
        $actual = $renderer->render($component, $context);
        $fixtureFile = $this->getFixturesPath() . DIRECTORY_SEPARATOR . $expectedFixtureFile;
        if ($this->shouldOverwriteFixture()) {
            file_put_contents($fixtureFile, $actual);
        }
        $expected = file_get_contents($fixtureFile);
        $this->assertEquals($expected, $actual);
    }

    public function provideComponents(): Generator
    {
        $rawContents = new RawContents('<marquee>Hello world</marquee>');
        $defaultConfiguration = new CurrentConfiguration([], new ApieContext(), BoundedContextFactory::createHashmap(), new BoundedContextId('default'));
        yield 'Raw HTML concents' => [
            'expected-raw-contents.html',
            $rawContents,
        ];
        yield 'Simple layout' => [
            'expected-simple-layout.html',
            new Layout(
                'Title',
                $defaultConfiguration,
                $rawContents
            )
        ];
        yield 'Bounded context select => nothing selected' => [
            'expected-bounded-context-select-nothing.html',
            new BoundedContextSelect(
                new CurrentConfiguration([], new ApieContext(), BoundedContextFactory::createHashmap(), null)
            )
        ];
        yield 'Bounded context select => unknown selection' => [
            'expected-bounded-context-select-unknown.html',
            new BoundedContextSelect(
                new CurrentConfiguration([], new ApieContext(), BoundedContextFactory::createHashmap(), new BoundedContextId('unknown'))
            )
        ];
        yield 'Bounded context select => single bounded context' => [
            'expected-bounded-context-select.html',
            new BoundedContextSelect(
                $defaultConfiguration
            )
        ];
        yield 'Bounded context select => multiple bounded context' => [
            'expected-bounded-context-select-multiple.html',
            new BoundedContextSelect(
                new CurrentConfiguration([], new ApieContext(), BoundedContextFactory::createHashmapWithMultipleContexts(), new BoundedContextId('default'))
            )
        ];
        yield 'Logo' => [
            'expected-logo.html',
            new Logo($defaultConfiguration),
        ];
        yield 'Login select' => [
            'expected-login-select.html',
            new LoginSelect(
                $defaultConfiguration
            )
        ];

        yield 'Profile' => [
            'expected-profile.html',
            new ShowProfile(
                $defaultConfiguration,
                new UserWithAddress(
                    new AddressWithZipcodeCheck(
                        new DatabaseText('Evergreen Terrace'),
                        new DatabaseText('742'),
                        new DatabaseText('11111'),
                        new DatabaseText('Springfield'),
                    ),
                    new UserWithAddressIdentifier('d788c9f5-6493-4386-89f4-374be3b28764'),
                )
            )
        ];

        yield 'Simple Menu' => [
            'expected-menu.html',
            new Layout\Menu($defaultConfiguration),
        ];

        yield 'Resource overview filters' => [
            'expected-resource-overview-filters.html',
            new FilterColumns(
                new StringList(['id', 'description']),
                'text search',
                ['description' => 'test'],
            )
            ];
        
        yield 'Resource overview' => [
            'expected-resource-overview.html',
            new Overview(
                [['id' => 12, 'name' => 'Pizza']],
                ['id', 'name'],
                new ResourceActionList(
                    $defaultConfiguration,
                    new ActionList([]),
                    new FilterColumns(new StringList(), '', []),
                )
            )
        ];

        $createResourceAction = new CreateResourceAction(
            new ReflectionClass(UserWithAutoincrementKey::class),
            new CreateResourceActionDefinition(
                new ReflectionClass(UserWithAutoincrementKey::class),
                new BoundedContextId('default')
            )
        );
        $resourceActionList = new ResourceActionList(
            $defaultConfiguration,
            new ActionList([$createResourceAction]),
            new FilterColumns(new StringList(), '', []),
        );
        yield 'Resource action list' => [
            'expected-resource-action-list.html',
            $resourceActionList
        ];

        yield 'Resource overview large list' => [
            'expected-resource-overview-large-list.html',
            new Overview(
                array_fill(0, 100, ['id' => 12, 'name' => 'Pizza']),
                ['id', 'name'],
                $resourceActionList
            )
        ];

        yield 'Form' => [
            'expected-form.html',
            new Form(RequestMethod::POST, null, [], [], new RawContents('test'), new RawContents('test2')),
        ];
        yield 'Form with validation errors' => [
            'expected-form-with-unknown-validation-error.html',
            new Form(RequestMethod::POST, null, ['id' => 'unknown field'], [], new RawContents('test')),
        ];

        yield 'Form group with validation errors' => [
            'expected-form-group-with-validation-error.html',
            new FormGroup(new FormName('test'), null, ['id' => 'unknown field'], new RawContents('test')),
        ];

        yield 'Form with validation error' => [
            'expected-form-with-validation-error.html',
            new Form(RequestMethod::POST, 'validation error', [], [], new RawContents('test'), new RawContents('test2')),
        ];

        yield 'Simple input field' => [
            'expected-input.html',
            new Input('name', 'value')
        ];

        yield 'Simple checkbox with validation error' => [
            'expected-checkbox-with-validation-error.html',
            new Checkbox(new FormName('name'), true, validationError: 'validation error')
        ];

        yield 'Simple input field with validation error' => [
            'expected-input-with-validation-error.html',
            new InputWithAutocomplete(new FormName('name'), 'value', 'https://www.example.com/ajaxCall', validationError: 'validation error')
        ];

        yield 'Simple input field with autocomplete' => [
            'expected-input-with-autocomplete.html',
            new InputWithAutocomplete(new FormName('name'), 'value', 'https://www.example.com/ajaxCall')
        ];
        yield 'HTML Editor' => [
            'expected-html-field.html',
            new HtmlField(
                'form[name]',
                '<div></div>',
                false,
                validationError: 'validation error',
                valueObjectClass: DatabaseText::class
            )
        ];

        yield 'Simple input field with autocomplete and validation error' => [
            'expected-input-with-autocomplete-and-validation-error.html',
            new Input('name', 'value', validationError: 'validation error')
        ];

        yield 'Simple password field' => [
            'expected-input-password.html',
            new Input('name', 'value', 'password')
        ];
        yield 'Hidden field' => [
            'expected-hidden-field.html',
            new HiddenField('name', 'value')
        ];
        yield 'Password field' => [
            'expected-password-field.html',
            new Password(StrongPassword::class, new FormName('name'), null, false)
        ];
        yield 'Password field with validation error' => [
            'expected-password-field-with-validation-error.html',
            new Password(StrongPassword::class, new FormName('name'), 'value', false, 'Validation Error')
        ];
        yield 'Multi select' => [
            'expected-multi-select.html',
            new MultiSelect(
                new FormName('set'),
                [],
                new ValueOptionList(
                    [
                        new ValueOption('boolean true', true),
                        new ValueOption('male', Gender::MALE),
                        new ValueOption('female', Gender::FEMALE),
                    ]
                )
            )
        ];

        yield 'Select' => [
            'expected-select.html',
            new Select(
                new FormName('set'),
                'male',
                new ValueOptionList(
                    [
                        new ValueOption('boolean true', true),
                        new ValueOption('male', Gender::MALE),
                        new ValueOption('female', Gender::FEMALE),
                    ]
                )
            )
        ];

        yield 'Union type' => [
            'expected-type-split.html',
            new FormSplit(
                new FormName('name'),
                '42',
                new ComponentHashmap([
                    'input' => new Input('name', 'value'),
                    'password' => new Input('name', 'value', 'password')
                ])
            )
        ];

        yield 'Form list' => [
            'expected-form-list.html',
            new FormPrototypeList(
                new FormName('name'),
                [],
                '__NAME__',
                new Input('__NAME__', 'value', 'tel')
            )
        ];

        yield 'Form list with values' => [
            'expected-form-list-with-values.html',
            new FormPrototypeList(
                new FormName('name'),
                [
                    '0611223344',
                    '0123456789',
                ],
                '__NAME__',
                new Input('__NAME__', 'value', 'tel')
            )
        ];

        yield 'Form hashmap' => [
            'expected-form-hashmap.html',
            new FormPrototypeHashmap(
                new FormName('name'),
                [],
                '__NAME__',
                new Input('__NAME__', 'value', 'tel')
            )
        ];

        yield 'Form hashmap with values' => [
            'expected-form-hashmap-with-values.html',
            new FormPrototypeHashmap(
                new FormName('name'),
                [
                    'first' => '0611223344',
                    'second' => '0123456789',
                ],
                '__NAME__',
                new Input('__NAME__', 'value', 'tel')
            )
        ];

        yield 'CSRF token' => [
            'expected-csrf-token.html',
            new Csrf('token-123')
        ];
        if (class_exists(HOTPSecret::class)) {
            $hotp = HOTP::create(str_repeat('A', 103));
            assert($hotp instanceof HOTP);
            yield 'OTP secret' => [
                'expected-otp-secret.html',
                new VerifyOtpInput(
                    'name',
                    null,
                    'label',
                    new HOTPSecret($hotp)
                )
            ];
        }
        yield 'List display' => [
            'expected-list-display.html',
            new ListDisplay(
                [
                    [
                        'id' => 1,
                        'description' => 'Description of 1',
                    ],
                    [
                        'id' => 2,
                        'description' => 'Description of 2',
                        'extra' => 'extra field',
                    ]
                ],
                ['id', 'description', 'extra'],
            )
        ];
        yield 'Empty list display' => [
            'expected-empty-list-display.html',
            new ListDisplay(
                [],
                ['id', 'description', 'extra'],
            )
        ];
        yield 'Segment display' => [
            'expected-segment-display.html',
            new SegmentDisplay([
                'test' => new RawContents('value1'),
                'test2' => new RawContents('value2'),
            ]),
        ];
        yield 'Segment display, hide keys' => [
            'expected-segment-display-no-keys.html',
            new SegmentDisplay(
                [
                    new RawContents('value1'),
                    new RawContents('value2'),
                ],
                showKeys: false,
            ),
        ];
        yield 'Empty segment display' => [
            'expected-empty-segment-display.html',
            new SegmentDisplay([
            ]),
        ];
        yield 'Link display' => [
            'expected-link-display.html',
            new LinkDisplay('this is a link', 'https://apie-lib.github.io/projectCoverage/index.html')
        ];
        yield 'Display true' => [
            'expected-true-display.html',
            new BooleanDisplay(true)
        ];
        yield 'Display false' => [
            'expected-false-display.html',
            new BooleanDisplay(false)
        ];
        yield 'Display null' => [
             'expected-null-display.html',
             new NullDisplay()
        ];
        $entity = new Order(OrderIdentifier::fromNative('35eadea7-b93b-4031-acaf-c9759886627d'), new OrderLineList());
        $singleResourceActionList = new SingleResourceActionList($defaultConfiguration, new ActionList([]), $entity->getId());
        yield 'Single resource action list' => [
            'expected-single-resource-action-list.html',
            $singleResourceActionList,
        ];
        
        yield 'Resource details' => [
            'expected-resource-details.html',
            new Detail(
                $entity,
                $singleResourceActionList,
                new SegmentDisplay([
                    'test' => new RawContents('value1'),
                    'test2' => new RawContents('value2'),
                ]),
            )
        ];

        yield 'Remove confirmation text' => [
            'expected-remove-confirm.html',
            new RemoveConfirm(new ReflectionClass(Order::class))
        ];
    }
}
