/**
 * Format a number as EUR currency.
 * Uses European formatting: €1.200,00
 * @param {number|string} amount
 * @param {boolean} showDecimals - Whether to show decimal places (default: false for clean display)
 * @returns {string}
 */
export function formatEUR(amount, showDecimals = false) {
  const num = parseFloat(amount) || 0;
  if (showDecimals) {
    return '€' + num.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  // If the number has meaningful decimals, show them
  if (num % 1 !== 0) {
    return '€' + num.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  return '€' + num.toLocaleString('de-DE', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

/**
 * Calculate discount for a given price.
 * Supports either fixed amount OR percentage discount (amount takes priority).
 * @param {number|string} price - Original price
 * @param {number|string|null} discountAmount - Fixed discount amount in EUR
 * @param {number|string|null} discountPercent - Percentage discount (0-100)
 * @returns {{ finalPrice: number, savings: number, hasDiscount: boolean }}
 */
export function calcDiscount(price, discountAmount, discountPercent) {
  const originalPrice = parseFloat(price) || 0;
  let savings = 0;

  if (discountAmount && parseFloat(discountAmount) > 0) {
    savings = parseFloat(discountAmount);
  } else if (discountPercent && parseFloat(discountPercent) > 0) {
    savings = originalPrice * parseFloat(discountPercent) / 100;
  }

  // Round savings to 2 decimal places
  savings = Math.round(savings * 100) / 100;

  return {
    finalPrice: Math.max(0, originalPrice - savings),
    savings: savings,
    hasDiscount: savings > 0
  };
}

/**
 * Get the effective (discounted) price from an item object.
 * Works with visas, packages, hotels, groups.
 * @param {object} item - Object with price, discount_amount, discount_percent
 * @returns {number}
 */
export function getEffectivePrice(item) {
  if (!item) return 0;
  const { finalPrice } = calcDiscount(item.price, item.discount_amount, item.discount_percent);
  return finalPrice;
}
