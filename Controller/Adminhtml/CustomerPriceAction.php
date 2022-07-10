<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Provides common functionality for the actions
 */
abstract class CustomerPriceAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'EPuzzle_CustomerPriceAdminUi::admin';
}
