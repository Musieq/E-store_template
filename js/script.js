(function () {
    const btnMinus = document.querySelector('.btn-step.minus');
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
        if (input.value > input.min) {
            input.value = inputValue - step;
        }
    }

    function valuePlus() {
        let inputValue = parseInt(input.value);
        if (input.value < input.max) {
            input.value = inputValue + step;
        }
        console.log(input.value);
    }
})()