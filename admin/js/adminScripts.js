/** Handles deleting categories on categories.php page. Passes href to button in modal window. **/
(function(){
    const deleteCatLink = document.querySelectorAll('.delete-category-link');
    const deleteCatConfirm = document.getElementById('delete-category-confirm');
    deleteCatLink.forEach(e => {
        e.addEventListener('click', function (){
            console.log(e.href);
            deleteCatConfirm.addEventListener('click', function (){
                window.location.href = e.href;
            })
        })
    })
})();
