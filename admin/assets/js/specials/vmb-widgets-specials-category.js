// Function to load the categories from the server
function loadCategories(categories = '') {

    const tableBody = document.getElementById('specialsCategory').getElementsByTagName('tbody')[0];

    if( categories ) {

        tableBody.innerHTML = '';
        // Sort categories alphabetically by name
        const sortedCategories = categories.sort((a, b) => a.name.localeCompare(b.name));
        sortedCategories.forEach((category, index) => {
            buildCategoryTable(tableBody, category, index);
        });

    } else {

        jQuery.ajax({
            url: vmb_ajax.ajax_url,
            type: 'POST',
            data: { 
                action: 'get_specials_meta',
                option: 'vmb_specials_category'
            },
            success: function(response) {
                if (response.success) {
                    
                    tableBody.innerHTML = '';

                    // Sort categories alphabetically by name
                    const categories = response.data.sort((a, b) => a.name.localeCompare(b.name));

                    categories.forEach((category, index) => {
                        buildCategoryTable(tableBody, category, index);
                    });
                }
            }
        });
    }

    
}

// Fetch categories from the server
// function fetchCategories() {
//     return jQuery.ajax({
//         url: vmb_ajax.ajax_url,
//         type: 'POST',
//         data: { 
//             action: 'get_specials_meta',
//             option: 'vmb_specials_category'
//         }
//     });
// }


// Build and display category table
function buildCategoryTable(tableBody, category, index) {
    const newRow = tableBody.insertRow();
    const nameCell = newRow.insertCell(0);
    const slugCell = newRow.insertCell(1);
    const actionCell = newRow.insertCell(2);

    nameCell.textContent = category.name;
    slugCell.textContent = category.slug;
    actionCell.innerHTML = `
        <button class="btn btn-sm btn-info" onclick="window.location.href='/${vmb_ajax.cached_category_slug}/${category.slug}'">View</button>
    `;
}

// Function to save categories
// function saveCategories(categories, name, slug, editIndex) {
//     jQuery.ajax({
//         url: vmb_ajax.ajax_url,
//         type: 'POST',
//         data: {
//             action: 'save_specials_meta',
//             data: JSON.stringify(categories),
//             option: 'vmb_specials_category'
//         },
//         success: function(response) {
//             if (!response.success) {
//                 if (response.data && response.data.message) {
//                     jQuery('#entryModalError').text(response.data.message).show();
//                 } else {
//                     jQuery('#entryModalError').text('Failed to save categories').show();
//                 }
//             } else {
//                 Swal.fire(
//                     'Saved!',
//                     '',
//                     'success'
//                 );

//                 const table = document.getElementById('specialsCategory').getElementsByTagName('tbody')[0];

//                 if (editIndex === '') {
//                     // Add new category
//                     const newRow = table.insertRow();
//                     const nameCell = newRow.insertCell(0);
//                     const slugCell = newRow.insertCell(1);
//                     const actionCell = newRow.insertCell(2);

//                     nameCell.textContent = name;
//                     slugCell.textContent = slug;
//                     actionCell.innerHTML = `
//                         <button class="btn btn-sm btn-warning" onclick="editEntry(this)">Edit</button>
//                         <button class="btn btn-sm btn-danger" onclick="deleteEntry(this)">Delete</button>
//                         <button class="btn btn-sm btn-info" onclick="window.location.href='/specialcode/${slug}'">View</button>
//                     `;
//                 } else {
//                     // Update existing category
//                     const row = table.rows[editIndex];
//                     row.cells[0].textContent = name;
//                     row.cells[1].textContent = slug;
//                 }

//                 // Clear the form fields
//                 document.getElementById('entryForm').reset();

//                 // Hide the modal
//                 var entryModalElement = document.getElementById('entryModal');
//                 var entryModal = bootstrap.Modal.getInstance(entryModalElement);
//                 entryModal.hide();
//             }
//         }
//     });
// }

// Function to edit an entry
// function editCategory(button, index) {
//     const row = button.parentNode.parentNode;
//     const name = row.cells[0].textContent;
//     const slug = row.cells[1].textContent;

//     document.getElementById('name').value = name;
//     document.getElementById('slug').value = slug;
//     document.getElementById('editIndex').value = index;
//     document.getElementById('entryModalLabel').textContent = 'Edit Entry';

//     var entryModal = new bootstrap.Modal(document.getElementById('entryModal'));
//     entryModal.show();
// }

// Function to delete an entry
// function deleteCategory(button, index) {
//     Swal.fire({
//         title: 'Are you sure?',
//         text: "You won't be able to revert this!",
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Yes, delete it!'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             const row = button.parentNode.parentNode;
//             row.parentNode.removeChild(row);

//             // Send AJAX request to delete the entry
//             jQuery.ajax({
//                 url: vmb_ajax.ajax_url,
//                 type: 'POST',
//                 data: {
//                     action: 'delete_specials_category',
//                     index: index
//                 },
//                 success: function(response) {
//                     if (!response.success) {
//                         alert('Failed to delete category');
//                     } else {
//                         loadCategories(); // Reload categories to update the table
//                         Swal.fire(
//                             'Deleted!',
//                             'Your category has been deleted.',
//                             'success'
//                         );
//                     }
//                 }
//             });
//         }
//     });
// }

// Function to reset the form and modal title
// function resetCategoryForm() {
//     document.getElementById('entryForm').reset();
//     document.getElementById('editIndex').value = '';
// }

