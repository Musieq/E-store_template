/** Change product quantity with buttons **/
(function () {
    let input = document.querySelectorAll('input.product-quantity');
    if (!input) {
        return;
    }

    input.forEach(el => {
        let btnMinus = el.previousElementSibling;
        let btnPlus = el.nextElementSibling;

        const step = parseInt(el.step);

        btnMinus.addEventListener('click', function () { valueMinus(step, el) });
        btnPlus.addEventListener('click', function () { valuePlus(step, el) });
    })

    function valueMinus(step, el) {
        let inputValue = parseInt(el.value);
        if (inputValue > el.min) {
            el.value = inputValue - step;
        }
    }

    function valuePlus(step, el) {
        let inputValue = parseInt(el.value);
        if (inputValue < el.max) {
            el.value = inputValue + step;
        }
    }
})();


/** Add to cart **/
(function () {
    const addToCartForm = document.getElementById('addToCartForm');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function (e) {
            // Get formdata
            let formData = new FormData(addToCartForm);
            // Set submit data to check if form was submitted on single_product.php
            formData.set('productAddToCart', 'submit')
            fetch(addToCartForm.action, {method:'post', body: formData})
                .then(function() {
                    // Display modal confirming adding to cart
                    let modalAddedToCart = new bootstrap.Modal(document.getElementById('modalAddedToCart'));
                    modalAddedToCart.show();
                });

            e.preventDefault();
        });
    }

})();



/** Remove product from cart **/
(function () {
    const cartRemoveProductsBtn = document.getElementsByName('cartRemoveProduct');
    cartRemoveProductsBtn.forEach(e => {
        e.addEventListener('click', function () {
            window.location.href = 'index.php?source=cart&removeProduct=' + e.value;
        })
    })
})();




/** Update total cost in checkout **/
if (typeof checkoutTotal !== 'undefined' && typeof currency !== 'undefined') {
    (function (checkoutTotal, currency) {
        const shippingOptions = document.getElementsByName('shippingOption');
        const checkoutTotalElement = document.getElementById('checkoutTotalElement');
        shippingOptions.forEach(el => {
            el.addEventListener('change', function () {
                updateTotalCost(el.getAttribute('data-price'), checkoutTotal);
            })
        })

        function updateTotalCost(price, checkoutTotal) {
            checkoutTotal = parseFloat(checkoutTotal);
            price = parseFloat(price);
            checkoutTotal += price;
            checkoutTotal = checkoutTotal.toFixed(2);
            checkoutTotalElement.innerHTML = 'Total cost with shipping: ' + checkoutTotal + ' ' + currency;
        }
        updateTotalCost(0, checkoutTotal);
    })(checkoutTotal, currency);
}


/** Gallery image init **/
window.onload = function(){
    if(document.querySelector('.yBox')){
        let myYbox = new yBox();
        myYbox.init();
    }
}


