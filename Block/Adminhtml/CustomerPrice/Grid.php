<?php
namespace EPuzzle\CustomerPriceAdminUi\Block\Adminhtml\CustomerPrice;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use EPuzzle\CustomerPriceAdminUi\Model\ResourceModel\CustomerPrice\Grid\CollectionFactory;
use Magento\Customer\Block\Adminhtml\Grid\Renderer\Multiaction;
use Magento\Eav\Model\ConfigFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Grid extends Extended
{
    private CollectionFactory $collectionFactory;

    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('custonerPriceGrid');
        $this->setDefaultSort('item_id');
        $this->setDefaultDir('DESC');
        $this->setEmptyText(__('There are no customer prices yet.'));
    }

    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'newButton',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('New Customer Price'),
                'onclick' => "setLocation('" . $this->getUrl('*/*/new') . "')",
                'class' => 'add primary',
            ]
        );

        return parent::_prepareLayout();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_name', ['header' => __('Name'), 'index' => 'product_name']);
        $this->addColumn('product_sku', ['header' => __('SKU'), 'index' => 'product_sku']);
        $this->addColumn('price', ['header' => __('Price'), 'index' => 'price', 'filter' => false]);
        $this->addColumn('qty', ['header' => __('Quantity'), 'index' => 'qty', 'filter' => false]);
        $this->addColumn('customer_name', ['header' => __('Cusomer'), 'index' => 'customer_name']);
        $this->addColumn('customer_email', ['header' => __('Email'), 'index' => 'customer_email']);
        $this->addColumn('created_at', ['header' => __('Created'), 'index' => 'created_at', 'filter' => false]);
        $this->addColumn('updated_at', ['header' => __('Modified'), 'index' => 'updated_at', 'filter' => false]);

        $this->addColumn(
            'actions',
            [
                'header' => __('Actions'),
                'type' => 'action',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base'=> '*/*/edit',
                        ],
                        'field' => 'item_id',
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'item_id',
                'header_css_class' => 'col-actions',
                'column_css_class' => 'col-actions',
            ]
        );

        return parent::_prepareColumns();
    }
}
