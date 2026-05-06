import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

function formatEuro(value) {
    const numberValue = Number(value);
    if (Number.isNaN(numberValue)) {
        return '0,00 €';
    }

    try {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
        }).format(numberValue);
    } catch {
        return `${numberValue.toFixed(2).replace('.', ',')} €`;
    }
}

function initInvoiceForm() {
    const container = document.getElementById('invoice-items');
    if (!container) {
        return;
    }

    const addButton = document.getElementById('add-item');
    const draftProduct = document.getElementById('draft-product');
    const draftQuantity = document.getElementById('draft-quantity');
    const invoiceTotalEl = document.getElementById('invoice-total');
    const totalTtc =
        document.getElementById('invoice_total_ttc') || document.querySelector('input[name$="[total_ttc]"]');

    function getProductPriceMap() {
        try {
            const products = JSON.parse(container.dataset.products || '[]');
            const map = new Map();
            for (const product of products) {
                if (!product || product.id == null) {
                    continue;
                }
                map.set(String(product.id), Number(product.price));
            }
            return map;
        } catch {
            return new Map();
        }
    }

    function recalculateTotals() {
        const priceMap = getProductPriceMap();
        let invoiceTotal = 0;

        const rows = container.querySelectorAll('tr.invoice-item-row');
        for (const row of rows) {
            const productSelect = row.querySelector('[data-field="product"]');
            const quantityInput = row.querySelector('[data-field="quantity"]');

            const productId = productSelect && 'value' in productSelect ? productSelect.value : '';
            const qty = quantityInput && 'value' in quantityInput ? Number(quantityInput.value) : 0;
            const unitPrice = priceMap.has(String(productId)) ? Number(priceMap.get(String(productId))) : 0;

            const lineTotal = unitPrice * (Number.isNaN(qty) ? 0 : qty);
            invoiceTotal += Number.isNaN(lineTotal) ? 0 : lineTotal;

            const unitPriceDisplay = row.querySelector('[data-display="unitPrice"]');
            const lineTotalDisplay = row.querySelector('[data-display="lineTotal"]');
            const productDisplay = row.querySelector('[data-display="product"]');
            const quantityDisplay = row.querySelector('[data-display="quantity"]');

            if (productDisplay && draftProduct) {
                const option = draftProduct.querySelector(`option[value="${CSS.escape(String(productId))}"]`);
                if (option) {
                    productDisplay.textContent = option.text;
                }
            }

            if (quantityDisplay) {
                quantityDisplay.textContent = String(Number.isNaN(qty) ? 0 : qty);
            }

            if (unitPriceDisplay) {
                unitPriceDisplay.textContent = formatEuro(unitPrice);
            }
            if (lineTotalDisplay) {
                lineTotalDisplay.textContent = formatEuro(lineTotal);
            }
        }

        if (invoiceTotalEl) {
            invoiceTotalEl.textContent = formatEuro(invoiceTotal);
        }

        if (totalTtc) {
            totalTtc.value = invoiceTotal.toFixed(2);
        }
    }

    if (addButton && addButton.dataset.invoiceItemsBound !== '1') {
        addButton.dataset.invoiceItemsBound = '1';

        addButton.addEventListener('click', (event) => {
            event.preventDefault();

            const selectedProductId = draftProduct && 'value' in draftProduct ? draftProduct.value : '';
            const selectedProductLabel =
                draftProduct && draftProduct.selectedOptions && draftProduct.selectedOptions[0]
                    ? draftProduct.selectedOptions[0].text
                    : '';
            const selectedQuantity =
                draftQuantity && 'value' in draftQuantity ? parseInt(draftQuantity.value || '0', 10) : 0;

            if (!selectedProductId || Number.isNaN(selectedQuantity) || selectedQuantity <= 0) {
                return;
            }

            const prototype = container.dataset.prototype;
            const index = parseInt(container.dataset.index || '0', 10);

            if (!prototype) {
                return;
            }

            const emptyRow = container.querySelector('.invoice-items-empty');
            if (emptyRow) {
                emptyRow.remove();
            }

            const newRowHtml = prototype.replace(/__name__/g, String(index));
            container.insertAdjacentHTML('beforeend', newRowHtml);
            container.dataset.index = String(index + 1);

            const newRow = container.querySelector('tr.invoice-item-row:last-child');
            if (newRow) {
                const productDisplay = newRow.querySelector('[data-display="product"]');
                const quantityDisplay = newRow.querySelector('[data-display="quantity"]');
                const unitPriceDisplay = newRow.querySelector('[data-display="unitPrice"]');
                const lineTotalDisplay = newRow.querySelector('[data-display="lineTotal"]');

                const productSelect = newRow.querySelector('[data-field="product"]');
                const quantityInput = newRow.querySelector('[data-field="quantity"]');

                if (productDisplay) {
                    productDisplay.textContent = selectedProductLabel;
                }

                if (quantityDisplay) {
                    quantityDisplay.textContent = String(selectedQuantity);
                }

                if (productSelect) {
                    productSelect.value = selectedProductId;
                    productSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }

                if (quantityInput) {
                    quantityInput.value = String(selectedQuantity);
                    quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
                }

                const priceMap = getProductPriceMap();
                const unitPrice = priceMap.has(String(selectedProductId)) ? Number(priceMap.get(String(selectedProductId))) : 0;
                const lineTotal = unitPrice * selectedQuantity;

                if (unitPriceDisplay) {
                    unitPriceDisplay.textContent = formatEuro(unitPrice);
                }
                if (lineTotalDisplay) {
                    lineTotalDisplay.textContent = formatEuro(lineTotal);
                }
            }

            if (draftProduct && 'value' in draftProduct) {
                draftProduct.value = '';
            }
            if (draftQuantity && 'value' in draftQuantity) {
                draftQuantity.value = '1';
            }

            recalculateTotals();
        });
    }

    if (container.dataset.invoiceItemsRemoveBound !== '1') {
        container.dataset.invoiceItemsRemoveBound = '1';

        container.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            const removeButton = target.closest('.remove-item');
            if (!removeButton) {
                return;
            }

            event.preventDefault();

            const row = removeButton.closest('tr');
            if (row) {
                row.remove();
            }

            const remainingRows = container.querySelectorAll('tr.invoice-item-row');
            if (remainingRows.length === 0) {
                container.insertAdjacentHTML(
                    'beforeend',
                    '<tr class="invoice-items-empty"><td colspan="6">Aucun Produit / Service</td></tr>'
                );
            }

            recalculateTotals();
        });
    }

    recalculateTotals();
}

document.addEventListener('turbo:load', initInvoiceForm);
document.addEventListener('DOMContentLoaded', initInvoiceForm);
initInvoiceForm();