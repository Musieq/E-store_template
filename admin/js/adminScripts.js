/** Handles deleting after confirming it in modal window. Pass delete button class and ID of modal confirmation button. Function passes href to button in modal window. **/
function deleteAndShowModal(deleteBtnClass, deleteBtnModalID) {
    const deleteLinkSelector = document.querySelectorAll(`.${deleteBtnClass}`);
    const deleteModalConfirmSelector = document.getElementById(`${deleteBtnModalID}`);
    deleteLinkSelector.forEach(e => {
        e.addEventListener('click', function (){
            console.log(e.href);
            deleteModalConfirmSelector.addEventListener('click', function (){
                window.location.href = e.href;
            })
        })
    })
}


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



/** Select every checkbox - bulk actions **/
function selectCheckboxes(checkboxSelectAllID, checkboxSelectName) {
    const checkboxSelectAll = document.getElementById(checkboxSelectAllID);
    const checkboxes = document.getElementsByName(checkboxSelectName);

    checkboxes.forEach(e => {
        e.checked = !!checkboxSelectAll.checked;
    })
}


/** Show modal when bulk deleting images **/
function bulkDeleteModal(formID, selectID) {
    const form = document.getElementById(formID);
    const select = document.getElementById(selectID).value;

    if (select == 1) {
        new bootstrap.Modal(document.getElementById('modalImageDeleteWarning')).toggle();
        document.getElementById('deleteImageConfirm').onclick = function () {
            form.submit();
        }
    }
}


