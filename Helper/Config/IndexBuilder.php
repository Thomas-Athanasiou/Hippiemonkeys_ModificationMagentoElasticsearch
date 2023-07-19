<?php
    /**
     * @Thomas-Athanasiou
     *
     * @author Thomas Athanasiou {thomas@hippiemonkeys.com}
     * @link https://hippiemonkeys.com
     * @link https://github.com/Thomas-Athanasiou
     * @copyright Copyright (c) 2022 Hippiemonkeys Web Intelligence EE All Rights Reserved.
     * @license http://www.gnu.org/licenses/ GNU General Public License, version 3
     * @package Hippiemonkeys_ModificationMagentoElasticsearch
     */

    declare(strict_types=1);

    namespace Hippiemonkeys\ModificationMagentoElasticsearch\Helper\Config;

    use Hippiemonkeys\ModificationMagentoElasticsearch\Api\Helper\Config\IndexBuilderInterface,
        Hippiemonkeys\Core\Helper\Config\Section\Group\Sub;

    class IndexBuilder
    extends Sub
    implements IndexBuilderInterface
    {
        /**
         * {@inheritdoc}
         */
        public function getMaxResultWindow() : int
        {
            return (int) $this->getData('index_builder_max_result_window');
        }
    }
?>