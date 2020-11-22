<?php

declare(strict_types=1);

namespace SkyOptical\ExportCmsData\Model\ResourceModel;

use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as cmsBlocksCollectionFactory;
use Magento\Cms\Model\ResourceModel\Block\Collection;

class CmsBlocksExport
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var cmsBlocksCollectionFactory
     */
    protected $cmsBlocksCollectionFactory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Csv
     */
    private $csv;

    /**
     * CmsBlocksExport constructor.
     * @param Filesystem $filesystem
     * @param cmsBlocksCollectionFactory $cmsBlocksCollectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FileFactory $fileFactory
     * @param TimezoneInterface $timezone
     * @param DirectoryList $directoryList
     * @param Csv $csvProcessor
     */
    public function __construct(
        Filesystem $filesystem,
        cmsBlocksCollectionFactory $cmsBlocksCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FileFactory $fileFactory,
        TimezoneInterface $timezone,
        DirectoryList $directoryList,
        Csv $csvProcessor
    ) {
        $this->cmsBlocksCollectionFactory = $cmsBlocksCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->fileFactory = $fileFactory;
        $this->directoryList = $directoryList;
        $this->csv = $csvProcessor;
        $this->timezone = $timezone;
        $this->filesystem = $filesystem;
    }
    /**
     * @param Collection $selectedItems
     * @return ResponseInterface
     * @throws FileSystemException
     */
    public function cmsExport(Collection $selectedItems): ResponseInterface
    {
        return $this->downloadCsv($selectedItems);
    }

    /**
     * @param Collection $blocks
     * @return ResponseInterface
     * @throws FileSystemException
     * @throws \Exception
     */
    public function downloadCsv(Collection $blocks): ResponseInterface
    {
        $filepath = 'export/custom_' . $this->timezone->date()->format('m_d_Y_H_i_s') . '.csv';
        $this->directory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->directory->create('export');
        /* Open file */
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();

        /* Write Header */
        $stream->writeCsv($this->getColumnHeaders($blocks));
           foreach ($blocks as $block)
           {
               $blockData=$block->getData();
               array_pop($blockData);
               $stream->writeCsv($blockData);
           }
        $content = [];
        $content['type'] = 'filename'; // must keep filename
        $content['value'] = $filepath;
        $content['rm'] = '1'; //remove csv from var folder

        $csvfilename = 'CmsBlocks.csv';
        return $this->fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);
    }

    /**
     * @param $blocks
     * @return string[]
     */
    public function getColumnHeaders($blocks): array
    {
        return array_keys($blocks->getFirstItem()->getData());
    }

}
