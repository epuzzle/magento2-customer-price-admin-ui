<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPrice;

use EPuzzle\CustomerPrice\Api\CustomerPriceRepositoryInterface;
use EPuzzle\CustomerPrice\Api\Data\CustomerPriceInterface;
use EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPriceAction;
use EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice\Locator;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Save the customer price entity
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Save extends CustomerPriceAction implements HttpPostActionInterface
{
    /**
     * @var Locator
     */
    private Locator $locator;

    /**
     * @var CustomerPriceRepositoryInterface
     */
    private CustomerPriceRepositoryInterface $customerPriceRepository;

    /**
     * Save
     *
     * @param Context $context
     * @param Locator $locator
     * @param CustomerPriceRepositoryInterface $customerPriceRepository
     */
    public function __construct(
        Context $context,
        Locator $locator,
        CustomerPriceRepositoryInterface $customerPriceRepository
    ) {
        parent::__construct($context);

        $this->locator = $locator;
        $this->customerPriceRepository = $customerPriceRepository;
    }

    /**
     * Save the request entity
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $model = $this->locator->getCustomerPrice();
            $itemId = (int)$this->getRequest()->getParam('item_id');
            if ($itemId && !$model->getItemId()) {
                $this->messageManager->addErrorMessage(__('This customer price no longer exists.'));
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }

            $model->setData($data);

            try {
                $this->customerPriceRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the customer price.'));
                return $this->processRequestReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            } catch (Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while saving the customer price.')
                );
            }

            $resultRedirect->setPath('*/*/edit', ['item_id' => $itemId]);
            return $resultRedirect;
        }

        $resultRedirect->setPath('*/*/');
        return $resultRedirect;
    }

    /**
     * Process and set the customer price return
     *
     * @param CustomerPriceInterface $model
     * @param array $data
     * @param Redirect $resultRedirect
     * @return ResultInterface
     */
    private function processRequestReturn(
        CustomerPriceInterface $model,
        array $data,
        Redirect $resultRedirect
    ): ResultInterface {
        $redirect = $data['back'] ?? 'close';
        if ($redirect === 'continue') {
            return $resultRedirect->setPath('*/*/edit', ['item_id' => $model->getItemId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
