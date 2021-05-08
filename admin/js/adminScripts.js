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


/** Show images in product_add.php **/
function ajaxFilterImages() {
    // Get inputs and container
    const imageContainer = document.getElementById('productImages');
    const imageFilterTitle = document.getElementById('imageFilterTitle');
    const imageFilterDate = document.getElementById('imageFilterDate');

    // Check if they exist
    if (!imageContainer || !imageFilterDate || !imageFilterTitle) {
        return;
    }

    // Get values from inputs
    const imageFilterTitleValue = imageFilterTitle.value;
    const imageFilterDateValue = imageFilterDate.value;


    // Fetch images
    function fetchImg(link) {
        fetch(link)
            .then(response => response.text())
            .then(data => {
                imageContainer.innerHTML = data;
            }).then(paginationInit)
            .then(selectCheckboxImage)
    }
    fetchImg("product_ajax_images.php?source=products&addProduct=1&imageFilterTitle="+imageFilterTitleValue+"&imageFilterDate="+imageFilterDateValue+"&imageFilterSubmit=filter");


    // Pagination
    function paginationInit() {
        const pagination = document.querySelectorAll('.page-link');
        if (!pagination) {
            return;
        }
        pagination.forEach(page => {
            page.addEventListener('click', function (e) {
                e.preventDefault();
                fetchImg(page.href);
            })
        })
    }

    // Selecting images
    function selectCheckboxImage() {
        let imageList = document.querySelectorAll('.imageList');
        if (!imageList) {
            return;
        }

        imageList.forEach(e => {
            e.addEventListener('keydown', function (event) {
                if (event.keyCode === 32 || event.code === 'Space' || event.key === ' ') {
                    checkImage(e);
                }
            })

            e.addEventListener('click', function () {
                checkImage(e);
            })
        })

        function checkImage(checkbox) {
            let checkboxChecked = checkbox.getAttribute('aria-checked');
            if (checkboxChecked === 'true') {
                checkbox.setAttribute('aria-checked', 'false');
            } else if(checkboxChecked === 'false') {
                checkbox.setAttribute('aria-checked', 'true');
            }
        }
    }
}
ajaxFilterImages();


/** Show modal on link click. Handle selecting images on product_add.php **/
(function () {
    // Prepare modal and show it on click
    const productImagesModal = new bootstrap.Modal(document.getElementById('addProductSelectImages'));
    const showModalBtn = document.getElementById('showProductImagesModal');
    const chooseImagesBtn = document.getElementById('chooseImagesBtn');
    const selectedImagesContainer = document.getElementById('containerImagesDraggable');
    const imageInput = document.getElementById('addProductImages');

    // Show modal on click
    showModal()

    // Add listener for submitting selected images
    if (!chooseImagesBtn) {
        console.log("Submit button doesn't exist")
        return;
    }
    chooseImagesBtn.addEventListener('click', function () {
        const imageList = document.querySelectorAll('.imageList');
        // Get array with all selected images
        let selectedImagesArr = getSelectedImages(imageList);

        // Close modal if at least 1 image is selected
        if (selectedImagesArr.length > 0) {
            productImagesModal.hide();

            // Show images on page
            if (!selectedImagesContainer && !imageInput) {
                console.log('Image container or input doesn\'t exist');
                return;
            }
            showSelectedImages(selectedImagesArr);

            // Allow drag and drop
            dragAndDrop(removeSelectedImage);

            // Remove unwanted selected images
            removeSelectedImage();
        }
    })




    function showModal() {
        if (!showModalBtn || !productImagesModal) {
            return;
        }

        showModalBtn.addEventListener('click', function () {
            productImagesModal.show();
        })
    }


    function getSelectedImages(imageList) {
        let selectedImagesArr = [];
        // Get all selected images
        if (!imageList) {
            return;
        }

        imageList.forEach(image => {
            if(image.getAttribute('aria-checked') === 'true'){
                // Create array with selected images
                selectedImagesArr.push(image.cloneNode(true));

                // Unselect images
                image.setAttribute('aria-checked', 'false');
            }
        })
        return selectedImagesArr;
    }


    function showSelectedImages(selectedImagesArr) {
        selectedImagesArr.forEach(image => {
            selectedImagesContainer.appendChild(image);
            image.removeAttribute('role');
            image.removeAttribute('aria-checked');
            image.setAttribute('draggable', 'true');

            // Add image id to hidden input
            let imageID = image.getAttribute('data-id');
            let imageInputValue = imageInput.value;
            if (imageInputValue === '') {
                imageInput.value = imageID;
            } else {
                imageInput.value += ','+imageID;
            }

            // Create element for removing selected img
            let removeSelectedImg = document.createElement('div');
            removeSelectedImg.classList.add('removeSelectedImg');
            removeSelectedImg.innerHTML = 'x';
            image.appendChild(removeSelectedImg);
        })
    }


    function removeSelectedImage() {
        // Remove selected image on click
        let removeSelectedImgBtn = document.querySelectorAll('.removeSelectedImg');
        if (!removeSelectedImgBtn) {
            return;
        }
        removeSelectedImgBtn.forEach(el => {
            el.addEventListener('click', function () {
                let parent = el.parentElement;
                parent.remove();

                // Update hidden input field value
                updateInputValue();
            })
        })
    }

    function updateInputValue() {
        imageInput.value = '';
        let imageList = selectedImagesContainer.childNodes;
        imageList.forEach(e => {
            let imageID = e.getAttribute('data-id');
            let imageInputValue = imageInput.value;
            if (imageInputValue === '') {
                imageInput.value = imageID;
            } else {
                imageInput.value += ','+imageID;
            }
        })
    }

/*    function dragAndDrop() {
        function handleDragStart(e) {
            this.style.opacity = '0.4';

            dragSrcEl = this;

            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);
        }

        function handleDragEnd(e) {
            this.style.opacity = '1';

            items.forEach(function (item) {
                item.classList.remove('over');
            });
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }

            return false;
        }

        function handleDragEnter(e) {
            this.classList.add('over');
        }

        function handleDragLeave(e) {
            this.classList.remove('over');
        }

        function handleDrop(e) {
            e.stopPropagation(); // stops the browser from redirecting.
            console.log(e);
            if (dragSrcEl !== this) {
                //dragSrcEl.outerHTML = this.outerHTML;
                this.outerHTML = e.dataTransfer.getData('text/html');
                updateInputValue();
                getItems();
            }

            return false;
        }

        let dragSrcEl;
        let getItems = function () {
            return document.querySelectorAll('.selected-product-images .imageList');
        }
        let items = getItems();
        //let items = document.querySelectorAll('.selected-product-images .imageList');
        items.forEach(function(item) {
            item.addEventListener('dragstart', handleDragStart, false);
            item.addEventListener('dragover', handleDragOver, false);
            item.addEventListener('dragenter', handleDragEnter, false);
            item.addEventListener('dragleave', handleDragLeave, false);
            item.addEventListener('dragend', handleDragEnd, false);
            item.addEventListener('drop', handleDrop, false);

        });
    }*/

    function dragAndDrop() {
        let dragSrcEl = null;

        function handleDragStart(e) {
            // Target (this) element is the source node.
            dragSrcEl = this;

            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);

            //this.classList.add('dragElem');
            this.style.opacity = '.4';
        }
        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault(); // Necessary. Allows us to drop.
            }


            e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.

            return false;
        }

        function handleDragEnter(e) {
            // this / e.target is the current hover target.
            this.classList.add('over');
        }

        function handleDragLeave(e) {
            this.classList.remove('over');  // this / e.target is previous target element.
        }

        function handleDrop(e) {
            // this/e.target is current target element.

            if (e.stopPropagation) {
                e.stopPropagation(); // Stops some browsers from redirecting.
            }

            // Don't do anything if dropping the same column we're dragging.
            if (dragSrcEl !== this) {
                // Set the source column's HTML to the HTML of the column we dropped on.
                this.parentNode.removeChild(dragSrcEl);
                let dropHTML = e.dataTransfer.getData('text/html');

                // Add before selected element
                this.insertAdjacentHTML('beforebegin',dropHTML);
                // Get added element and create event listeners
                let dropElem = this.previousSibling;
                addDnDHandlers(dropElem);
                // Update input field
                updateInputValue();
            }
            this.classList.remove('over');
            return false;
        }

        function handleDragEnd(e) {
            // this/e.target is the source node.
            this.classList.remove('over');
            this.style.opacity = '1';
        }

        function addDnDHandlers(elem) {
            elem.addEventListener('dragstart', handleDragStart, false);
            elem.addEventListener('dragenter', handleDragEnter, false)
            elem.addEventListener('dragover', handleDragOver, false);
            elem.addEventListener('dragleave', handleDragLeave, false);
            elem.addEventListener('drop', handleDrop, false);
            elem.addEventListener('dragend', handleDragEnd, false);

        }

        let cols = document.querySelectorAll('.selected-product-images .imageList');
        [].forEach.call(cols, addDnDHandlers);
    }


})();




/*(function () {
    // Prepare modal and show it on click
    const productImagesModal = new bootstrap.Modal(document.getElementById('addProductSelectImages'));
    const showModalBtn = document.getElementById('showProductImagesModal');
    if (!showModalBtn || !productImagesModal) {
        return;
    }

    showModalBtn.addEventListener('click', function () {
        productImagesModal.show();
    })


    // Get select button
    const chooseImagesBtn = document.getElementById('chooseImagesBtn');
    if (!chooseImagesBtn) {
        return;
    }

    chooseImagesBtn.addEventListener('click', function () {
        let selectedImagesArr = [];
        // Get all selected images
        const imageList = document.querySelectorAll('.imageList');
        if (!imageList) {
            return;
        }

        imageList.forEach(image => {
            if(image.getAttribute('aria-checked') === 'true'){
                // Create array with selected images
                selectedImagesArr.push(image.cloneNode(true));

                // Unselect images
                image.setAttribute('aria-checked', 'false');
            }
        })

        // Close modal if at least 1 image is selected
        if (selectedImagesArr.length > 0) {
            productImagesModal.hide();

            // Show them on page
            const selectedImagesContainer = document.getElementById('containerImagesDraggable');
            const imageInput = document.getElementById('addProductImages');
            if (!selectedImagesContainer && !imageInput) {
                return;
            }

            selectedImagesArr.forEach(image => {
                selectedImagesContainer.appendChild(image);
                image.removeAttribute('role');
                image.removeAttribute('aria-checked');

                // Add image id to hidden input
                let imageID = image.getAttribute('data-id');
                let imageInputValue = imageInput.value;
                if (imageInputValue === '') {
                    imageInput.value = imageID;
                } else {
                    imageInput.value += ','+imageID;
                }

                // Create element for removing selected img
                let removeSelectedImg = document.createElement('div');
                removeSelectedImg.classList.add('removeSelectedImg');
                removeSelectedImg.innerHTML = 'x';
                image.appendChild(removeSelectedImg);
            })

            // Remove selected image on click
            let removeSelectedImgBtn = document.querySelectorAll('.removeSelectedImg');
            if (!removeSelectedImgBtn) {
                return;
            }
            removeSelectedImgBtn.forEach(el => {
                el.addEventListener('click', function () {
                    let parent = el.parentElement;
                    parent.remove();

                    // Update hidden input field value
                    imageInput.value = '';
                    let imageList = selectedImagesContainer.childNodes;
                    imageList.forEach(e => {
                        console.log(e);
                        let imageID = e.getAttribute('data-id');
                        let imageInputValue = imageInput.value;
                        if (imageInputValue === '') {
                            imageInput.value = imageID;
                        } else {
                            imageInput.value += ','+imageID;
                        }
                    })
                })
            })

        }
    })
})();*/



/** Initialize CKEditor **/
const CKEditorElement = document.querySelector( '#addProductDescription');
if (CKEditorElement) {
    ClassicEditor
        .create( CKEditorElement, {
            removePlugins: ['Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'CKFinder', 'EasyImage']
        } )
        .then( editor => {
            //console.log( editor );
        } )
        .catch( error => {
            //console.error( error );
        } );
}
