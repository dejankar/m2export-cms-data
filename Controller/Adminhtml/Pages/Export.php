<?php

declare(strict_types=1);

namespace SkyOptical\ExportCmsData\Controller\Adminhtml\Pages;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use SkyOptical\ExportCmsData\Model\ResourceModel\CmsPagesExport as CmsPagesResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Export
 *
 * @package SkyOptical\ExportCmsData\Controller\Adminhtml\Pages
 */
class Export extends Action implements HttpPostActionInterface
{
    /**
     * @var CmsPagesResourceModel
     */
    private $cmsPagesResourceModel;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var Context
     */
    private $context;

    /**
     * Export constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CmsPagesResourceModel $cmsPagesResourceModel
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CmsPagesResourceModel $cmsPagesResourceModel,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->cmsPagesResourceModel = $cmsPagesResourceModel;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->context = $context;
        $this->storeManager=$storeManager;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return  $this->cmsPagesResourceModel->cmsExport($collection);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('cms/pages/index');
        return $resultRedirect;
    }
}
