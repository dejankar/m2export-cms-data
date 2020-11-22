<?php

declare(strict_types=1);

namespace SkyOptical\ExportCmsData\Controller\Adminhtml\Blocks;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use SkyOptical\ExportCmsData\Model\ResourceModel\CmsBlocksExport as CmsBlocksResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Export
 *
 * @package SkyOptical\ExportCmsData\Controller\Adminhtml\Blocks
 */
class Export extends Action implements HttpPostActionInterface
{
    /**
     * @var CmsBlocksResourceModel
     */
    private $cmsBlocksResourceModel;

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
     * @param CmsBlocksResourceModel $cmsBlocksResourceModel
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CmsBlocksResourceModel $cmsBlocksResourceModel,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->cmsBlocksResourceModel = $cmsBlocksResourceModel;
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
            return  $this->cmsBlocksResourceModel->cmsExport($collection);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('cms/block/index');
        return $resultRedirect;
    }
}
