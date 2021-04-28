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


/** Displays image preview when adding image on images.php **/
(function(){
    const imageAddPreview = document.getElementById('addImage');
    if (!imageAddPreview) { return; }

    imageAddPreview.addEventListener('change', function (){
        if (this.files && this.files[0] && this.files[0].type.match('image/*')) {
            let reader = new FileReader();

            reader.onload = function (e) {
                document.getElementById('imageUpload').src = e.target.result;
            }

            reader.readAsDataURL(this.files[0]); // convert to base64 string

        }
    })
})();



