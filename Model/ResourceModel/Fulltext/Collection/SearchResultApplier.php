<?php
    /**
     * @Thomas-Athanasiou
     *
     * @author Thomas Athanasiou {thomas@hippiemonkeys.com}
     * @link https://hippiemonkeys.com
     * @link https://github.com/Thomas-Athanasiou
     * @copyright Copyright (c) 2022 Hippiemonkeys Web Inteligence EE All Rights Reserved.
     * @license http://www.gnu.org/licenses/ GNU General Public License, version 3
     * @package Hippiemonkeys_ModificationMagentoElasticsearch
     */

    declare(strict_types=1);

    namespace Hippiemonkeys\ModificationMagentoElasticsearch\Model\ResourceModel\Fulltext\Collection;

    use Magento\Catalog\Api\Data\ProductInterface,
        Magento\CatalogInventory\Model\StockStatusApplierInterface,
        Magento\CatalogInventory\Model\ResourceModel\StockStatusFilterInterface,
        Magento\Framework\Api\Search\SearchResultInterface,
        Magento\Framework\App\Config\ScopeConfigInterface,
        Magento\Framework\App\ObjectManager,
        Magento\Framework\Data\Collection,
        Magento\Framework\EntityManager\MetadataPool,
        Magento\Elasticsearch\Model\ResourceModel\Fulltext\Collection\SearchResultApplier as ParentSearchResultApplier,
        Hippiemonkeys\Core\Api\Helper\ConfigInterface;

    class SearchResultApplier
    extends ParentSearchResultApplier
    {
        /**
         * Constructor
         *
         * @access public
         *
         * @param \Magento\Framework\Data\Collection $collection
         * @param \Magento\Framework\Api\Search\SearchResultInterface $searchResult
         * @param int $size
         * @param int $currentPage
         * @param \Magento\Framework\App\Config\ScopeConfigInterface|null $scopeConfig
         * @param \Magento\Framework\EntityManager\MetadataPool|null $metadataPool
         * @param \Magento\CatalogInventory\Model\ResourceModel\StockStatusFilterInterface|null $stockStatusFilter
         * @param \Magento\CatalogInventory\Model\StockStatusApplierInterface|null $stockStatusApplier
         */
        public function __construct(
            Collection $collection,
            SearchResultInterface $searchResult,
            int $size,
            int $currentPage,
            ConfigInterface $config,
            ?ScopeConfigInterface $scopeConfig = null,
            ?MetadataPool $metadataPool = null,
            ?StockStatusFilterInterface $stockStatusFilter = null,
            ?StockStatusApplierInterface $stockStatusApplier = null
        )
        {
            parent::__construct(
                $collection,
                $searchResult,
                $size,
                $currentPage,
                $scopeConfig,
                $metadataPool,
                $stockStatusFilter,
                $stockStatusApplier
            );

            $this->_collection = $collection;
            $this->_searchResult = $searchResult;
            $this->_size = $size;
            $this->_currentPage = $currentPage;
            $this->_config =  $config;
        }

        /**
         * @inheritdoc
         */
        public function apply()
        {
            if($this->getIsActive())
            {
                $collection = $this->getCollection();
                $searchResult = $this->getSearchResult();

                if (empty($searchResult->getItems()))
                {
                    $collection->getSelect()->where('NULL');
                    return;
                }

                $ids = [];
                $items = $this->sliceItems(
                    $searchResult->getItems(),
                    $this->getSize(),
                    $this->getCurrentPage()
                );

                foreach ($items as $item) {
                    $ids[] = (int)$item->getId();
                }

                $collection->getSelect()
                    ->where('e.entity_id IN (?)', $ids)
                    ->reset(\Magento\Framework\DB\Select::ORDER)
                    ->order(new \Zend_Db_Expr(\sprintf('FIELD(e.entity_id,%s)', implode(',', $ids))));
            }
            else
            {
                parent::apply();
            }
        }

        /**
         * Slice current items
         *
         * @access private
         *
         * @param array $items
         * @param int $size
         * @param int $currentPage
         * @return array
         */
        private function sliceItems(array $items, int $size, int $currentPage): array
        {
            if ($size !== 0)
            {
                // Check that current page is in a range of allowed page numbers, based on items count and items per page,
                // than calculate offset for slicing items array.
                $itemsCount = count($items);
                $maxAllowedPageNumber = ceil($itemsCount/$size);
                if ($currentPage < 1)
                {
                    $currentPage = 1;
                }

                if ($currentPage > $maxAllowedPageNumber)
                {
                    $currentPage = $maxAllowedPageNumber;
                }

                $items = array_slice(
                    $items,
                    $this->getOffset((int) $currentPage, $size),
                    $size
                );
            }
            return $items;
        }

        /**
         * Get offset for given page.
         *
         * @access private
         *
         * @param int $pageNumber
         * @param int $pageSize
         * @return int
         */
        private function getOffset(int $pageNumber, int $pageSize): int
        {
            return ($pageNumber - 1) * $pageSize;
        }

        /**
         * Gets wether the currency modification is active or not.
         *
         * @access protected
         *
         * @return bool
         */
        protected function getIsActive(): bool
        {
            return $this->getConfig()->getIsActive();
        }

        /**
         * Returns if display out of stock status set or not in catalog inventory
         *
         * @access private
         *
         * @return bool
         */
        private function hasShowOutOfStockStatus(): bool
        {
            return (bool) $this->getScopeConfig()->getValue(
                \Magento\CatalogInventory\Model\Configuration::XML_PATH_SHOW_OUT_OF_STOCK,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        /**
         * Collection property
         *
         * @access private
         *
         * @var Magento\Framework\Data\Collection|\Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $_collection
         */
        private $_collection;

        /**
         * Gets Collection
         *
         * @access protected
         *
         * @return Magento\Framework\Data\Collection|\Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
         */
        protected function getCollection(): Collection
        {
            return $this->_collection;
        }

        /**
         * Search Result property
         *
         * @access private
         *
         * @var \Magento\Framework\Api\Search\SearchResultInterface $_searchResult
         */
        private $_searchResult;

        /**
         * Gets Search Result
         *
         * @access protected
         *
         * @return \Magento\Framework\Api\Search\SearchResultInterface
         */
        protected function getSearchResult(): SearchResultInterface
        {
            return $this->_searchResult;
        }

        /**
         * Size property
         *
         * @access private
         *
         * @var int $_size
         */
        private $_size;

        /**
         * Gets Size
         *
         * @access protected
         *
         * @return int
         */
        protected function getSize(): int
        {
            return $this->_size;
        }

        /**
         * Current Page property
         *
         * @access private
         *
         * @var int $_currentPage
         */
        private $_currentPage;

        /**
         * Gets Current Page
         *
         * @access protected
         *
         * @return int
         */
        protected function getCurrentPage(): int
        {
            return $this->_currentPage;
        }

        /**
         * Config property
         *
         * @access private
         *
         * @var \Hippiemonkeys\Core\Api\Helper\ConfigInterface $_config
         */
        private $_config;

        /**
         * Gets Config
         *
         * @access protected
         *
         * @return \Hippiemonkeys\Core\Api\Helper\ConfigInterface
         */
        protected function getConfig(): ConfigInterface
        {
            return $this->_config;
        }
    }
?>