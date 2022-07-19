<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Ui\Component\Listing\Customer\Column;

use EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\Customer\Column\Actions;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see Actions
 */
class ActionsTest extends TestCase
{
    /**
     * @var UrlInterface|MockObject
     */
    private UrlInterface $urlBuilder;

    /**
     * @var Actions
     */
    private Actions $actions;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $context = $this->createMock(ContextInterface::class);
        $uiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->urlBuilder = $this->createMock(UrlInterface::class);
        $this->actions = new Actions(
            $context,
            $uiComponentFactory,
            $this->urlBuilder,
            [],
            ['name' => 'actions']
        );
    }

    /**
     * @see Actions::prepareDataSource()
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testPrepareDataSource(): void
    {
        $expectedValue = [
            'data' => [
                'items' => [
                    [
                        'entity_id' => 1,
                        'email' => 'email1@email.com',
                        'name' => 'Firstname1 Lastname1',
                        'actions' => [
                            'edit' => [
                                'callback' => [
                                    [
                                        'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.'
                                            . 'general.customer_model',
                                        'target' => 'closeModal'
                                    ],
                                    [
                                        'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.'
                                            . 'general.customer_button',
                                        'target' => 'updateData',
                                        'params' => [
                                            'entityId' => 1,
                                            'options' => [
                                                [
                                                    'label' => 'Email',
                                                    'value' => '<a href="mailto:email1@email.com"'
                                                        . ' target="_blank">email1@email.com</a>'
                                                ],
                                                [
                                                    'label' => 'Name',
                                                    'value' => '<a href="https://example.com/admin/customerPrice/1"'
                                                        . ' target="_blank">Firstname1 Lastname1</a>'
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'href' => '#',
                                'label' => 'Assign',
                                '__disableTmpl' => true
                            ]
                        ]
                    ],
                    [
                        'entity_id' => 2,
                        'email' => 'email2@email.com',
                        'name' => 'Firstname2 Lastname2',
                        'actions' => [
                            'edit' => [
                                'callback' => [
                                    [
                                        'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.'
                                            . 'general.customer_model',
                                        'target' => 'closeModal'
                                    ],
                                    [
                                        'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.'
                                            . 'general.customer_button',
                                        'target' => 'updateData',
                                        'params' => [
                                            'entityId' => 2,
                                            'options' => [
                                                [
                                                    'label' => 'Email',
                                                    'value' => '<a href="mailto:email2@email.com"'
                                                        . ' target="_blank">email2@email.com</a>'
                                                ],
                                                [
                                                    'label' => 'Name',
                                                    'value' => '<a href="https://example.com/admin/customerPrice/2"'
                                                        . ' target="_blank">Firstname2 Lastname2</a>'
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'href' => '#',
                                'label' => 'Assign',
                                '__disableTmpl' => true
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $dataSource = [
            'data' => [
                'items' => [
                    [
                        'entity_id' => 1,
                        'email' => 'email1@email.com',
                        'name' => 'Firstname1 Lastname1'
                    ],
                    [
                        'entity_id' => 2,
                        'email' => 'email2@email.com',
                        'name' => 'Firstname2 Lastname2'
                    ]
                ]
            ]
        ];

        $this->urlBuilder->expects($this->any())
            ->method('getUrl')
            ->will(
                $this->returnValueMap([
                    ['customer/index/edit', ['id' => 1], 'https://example.com/admin/customerPrice/1'],
                    ['customer/index/edit', ['id' => 2], 'https://example.com/admin/customerPrice/2']
                ])
            );

        $this->assertEquals($expectedValue, $this->actions->prepareDataSource($dataSource));
    }
}
