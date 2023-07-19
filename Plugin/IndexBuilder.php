<?php
    /**
     * @Thomas-Athanasiou
     *
     * @author Thomas Athanasiou {thomas@hippiemonkeys.com}
     * @link https://hippiemonkeys.com
     * @link https://github.com/Thomas-Athanasiou
     * @copyright Copyright (c) 2023 Hippiemonkeys Web Intelligence EE All Rights Reserved.
     * @license http://www.gnu.org/licenses/ GNU General Public License, version 3
     * @package Hippiemonkeys_ModificationMagentoElasticsearch
     */

    declare(strict_types=1);

    namespace Hippiemonkeys\ModificationMagentoElasticsearch\Plugin;

    use Magento\Elasticsearch\Model\Adapter\Index\Builder as IndexBuilderAdapter,
        Hippiemonkeys\ModificationMagentoElasticsearch\Api\Helper\Config\IndexBuilderInterface as ConfigInterface;

    class IndexBuilder
    {
        /**
         * Constructor
         *
         * @access public
         *
         * @param \Hippiemonkeys\ModificationMagentoElasticsearch\Api\Helper\Config\IndexBuilderInterface $config
         */
        public function __construct(
            ConfigInterface $config
        )
        {
            $this->_config = $config;
        }

        /**
         * After Build Plugin
         *
         * @access public
         *
         * @return \Magento\Elasticsearch\Model\Adapter\Index\Builder $indexBuilderAdapter
         * @return array $settings
         *
         * @return array
         */
        public function afterBuild(IndexBuilderAdapter $indexBuilderAdapter, array $settings): array
        {
            $config = $this->getConfig();

            if($config->getIsActive())
            {
                $maxResultWindow = $config->getMaxResultWindow();
                if($maxResultWindow > 0)
                {
                    $settings['max_result_window'] = $maxResultWindow;
                }
            }

            return $settings;
        }

        /**
         * Config property
         *
         * @access private
         *
         * @var \Hippiemonkeys\ModificationMagentoElasticsearch\Api\Helper\Config\IndexBuilderInterface $_config
         */
        private $_config;

        /**
         * Gets Config
         *
         * @access protected
         *
         * @return \Hippiemonkeys\ModificationMagentoElasticsearch\Api\Helper\Config\IndexBuilderInterface
         */
        protected function getConfig(): ConfigInterface
        {
            return $this->_config;
        }
    }
?>