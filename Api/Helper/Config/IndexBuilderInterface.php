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

    namespace Hippiemonkeys\ModificationMagentoElasticsearch\Api\Helper\Config;

    use Hippiemonkeys\Core\Api\Helper\ConfigInterface;

    interface IndexBuilderInterface
    extends ConfigInterface
    {
        /**
         * Gets Max Result Window
         *
         * @access public
         *
         * @return int
         */
        function getMaxResultWindow(): int;
    }
?>