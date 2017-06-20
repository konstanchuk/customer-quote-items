<?php

/**
 * Customer Quote Items Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\CustomerQuoteItems\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Quote\Model\QuoteRepository;
use Magento\Framework\Json\Helper\Data as JsonHelper;


class QuoteItems extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param QuoteRepository $quoteRepository
     * @param JsonHelper $jsonHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        QuoteRepository $quoteRepository,
        JsonHelper $jsonHelper,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->quoteRepository = $quoteRepository;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item['items_count'] > 0) {
                    $item[$fieldName . '_html'] = sprintf('<button class="button">%s</button>', __('show items'));
                    $item[$fieldName . '_quote_id'] = $item['entity_id'];
                    $item[$fieldName . '_quote_products'] = $this->jsonHelper->jsonEncode($this->getQuoteItemsArray($item['entity_id']));
                }
            }
        }
        return $dataSource;
    }

    protected function getQuoteItemsArray($quoteId)
    {
        $data = [];
        try {
            $quote = $this->quoteRepository->get($quoteId);
            $items = $quote->getItems();
            if ($items) {
                foreach ($items as $item) {
                    $data[] = [
                        'name' => $item->getName(),
                        'sku' => $item->getSku(),
                        'qty' => $item->getQty(),
                        'price' => $item->getPrice(),
                    ];
                }
            }
        } catch (\Exception $e) {}
        return $data;
    }
}