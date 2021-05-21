/** Change product quantity with buttons **/
(function () {
/*    const btnMinus = document.querySelector('.btn-step.minus');
    const btnPlus = document.querySelector('.btn-step.plus');
    const input = document.getElementById('productQuantity');
    if (!btnMinus || !btnPlus || !input) {
        return;
    }

    btnMinus.addEventListener('click', valueMinus);
    btnPlus.addEventListener('click', valuePlus);

    const step = parseInt(input.step);

    function valueMinus() {
        let inputValue = parseInt(input.value);
        if (inputValue > input.min) {
            input.value = inputValue - step;
        }
    }

    function valuePlus() {
        let inputValue = parseInt(input.value);
        if (inputValue < input.max) {
            input.value = inputValue + step;
        }
    }*/

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
            // TODO .then update cart count

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


