/**
 * Customer Quote Items Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'underscore',
    'ko',
    'mage/template',
    'text!Konstanchuk_CustomerQuoteItems/templates/grid/cells/quote/products.html',
    'Magento_Ui/js/modal/modal'
], function (Column, $, _, ko, mageTemplate, productsTemplate) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },
        getHtml: function (row) {
            return this.getLabel(row);
        },
        getLabel: function (row) {
            var key = this.index + '_html';
            return _.has(row, key) && this.getProducts(row).length ? row[key] : '';
        },
        getQuoteId: function (row) {
            return row[this.index + '_quote_id'];
        },
        getProducts: function (row) {
            var key = this.index + '_quote_products';
            return _.has(row, key) ? JSON.parse(row[key]) : [];
        },
        preview: function (row) {
            if (!this.getLabel(row)) {
                return;
            }
            var modalHtml = $('<div/>').html(this._renderKoTemplate(productsTemplate, {
                quote_id: this.getQuoteId(row),
                products: this.getProducts(row)
            }));
            modalHtml.modal({
                title: $.mage.__('Quote Product Items'),
                innerScroll: true,
                modalClass: '_image-box',
                buttons: [{
                    text: $.mage.__('Ok'),
                    click: function () {
                        this.closeModal();
                    }
                }]
            }).trigger('openModal');
        },
        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        },
        _renderKoTemplate: function (html, data) {
            var node = new DOMParser().parseFromString(html, 'text/html');
            ko.applyBindings(data, node.body);
            var result = node.body.innerHTML.toString();
            ko.cleanNode(node);
            return result;
        }
    });
});