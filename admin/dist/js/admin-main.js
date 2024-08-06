// Function to load the categories from the server
function loadCategories(categories = '') {

    const tableBody = document.getElementById('specialsCategory').getElementsByTagName('tbody')[0];

    console.log(categories);

    if( categories ) {

        tableBody.innerHTML = '';
        categories.forEach((category, index) => {
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
    
                    response.data.forEach((category, index) => {
                        buildCategoryTable(tableBody, category, index);
                    });
                }
            }
        });
    }

    
}

// Function to save categories
function saveCategories(categories, name, slug, editIndex) {
    jQuery.ajax({
        url: vmb_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'save_specials_meta',
            data: JSON.stringify(categories),
            option: 'vmb_specials_category'
        },
        success: function(response) {
            if (!response.success) {
                if (response.data && response.data.message) {
                    jQuery('#entryModalError').text(response.data.message).show();
                } else {
                    jQuery('#entryModalError').text('Failed to save categories').show();
                }
            } else {
                Swal.fire(
                    'Saved!',
                    '',
                    'success'
                );

                const table = document.getElementById('specialsCategory').getElementsByTagName('tbody')[0];

                if (editIndex === '') {
                    // Add new category
                    const newRow = table.insertRow();
                    const nameCell = newRow.insertCell(0);
                    const slugCell = newRow.insertCell(1);
                    const actionCell = newRow.insertCell(2);

                    nameCell.textContent = name;
                    slugCell.textContent = slug;
                    actionCell.innerHTML = `
                        <button class="btn btn-sm btn-warning" onclick="editEntry(this)">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteEntry(this)">Delete</button>
                        <button class="btn btn-sm btn-info" onclick="window.location.href='/specialcode/${slug}'">View</button>
                    `;
                } else {
                    // Update existing category
                    const row = table.rows[editIndex];
                    row.cells[0].textContent = name;
                    row.cells[1].textContent = slug;
                }

                // Clear the form fields
                document.getElementById('entryForm').reset();

                // Hide the modal
                var entryModalElement = document.getElementById('entryModal');
                var entryModal = bootstrap.Modal.getInstance(entryModalElement);
                entryModal.hide();
            }
        }
    });
}

// Function to edit an entry
function editCategory(button, index) {
    const row = button.parentNode.parentNode;
    const name = row.cells[0].textContent;
    const slug = row.cells[1].textContent;

    document.getElementById('name').value = name;
    document.getElementById('slug').value = slug;
    document.getElementById('editIndex').value = index;
    document.getElementById('entryModalLabel').textContent = 'Edit Entry';

    var entryModal = new bootstrap.Modal(document.getElementById('entryModal'));
    entryModal.show();
}

// Function to delete an entry
function deleteCategory(button, index) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);

            // Send AJAX request to delete the entry
            jQuery.ajax({
                url: vmb_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'delete_specials_category',
                    index: index
                },
                success: function(response) {
                    if (!response.success) {
                        alert('Failed to delete category');
                    } else {
                        loadCategories(); // Reload categories to update the table
                        Swal.fire(
                            'Deleted!',
                            'Your category has been deleted.',
                            'success'
                        );
                    }
                }
            });
        }
    });
}

// Fetch categories from the server
function fetchCategories() {
    return jQuery.ajax({
        url: vmb_ajax.ajax_url,
        type: 'POST',
        data: { 
            action: 'get_specials_meta',
            option: 'vmb_specials_category'
        }
    });
}

// Function to reset the form and modal title
function resetCategoryForm() {
    document.getElementById('entryForm').reset();
    document.getElementById('editIndex').value = '';
}


// format 
function buildCategoryTable(tableBody, category, index) {
    const newRow = tableBody.insertRow();
    const nameCell = newRow.insertCell(0);
    const slugCell = newRow.insertCell(1);
    const actionCell = newRow.insertCell(2);

    nameCell.textContent = category.name;
    slugCell.textContent = category.slug;
    actionCell.innerHTML = `
        <button class="btn btn-sm btn-warning" onclick="editCategory(this, ${index})">Edit</button>
        <button class="btn btn-sm btn-danger" onclick="deleteCategory(this, ${index})">Delete</button>
        <button class="btn btn-sm btn-info" onclick="window.location.href='/${vmb_ajax.cached_category_slug}/${category.slug}'">View</button>
    `;
}
// Generate table rows with dropdowns for categories
function generateSpecialsTable(specials, categories) {
    const tableBody = document.getElementById('specialsTable').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = '';

    specials.forEach((special, index) => {
        const newRow = tableBody.insertRow();
        newRow.setAttribute('data-modified', special.modified); // Add modified attribute
        newRow.setAttribute('data-disable', special.disable); // new update: Add disable attribute
        newRow.innerHTML = `
            <td>${special.id}</td>
            <td>${special.resort}</td>
            <td>${special.name}</td>
            <td>${special.description}</td>
            <td>${formatDate(special.expiration)}</td>
            <td>${special.category ? special.category : ''}</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-warning" onclick="editSpecial(${index})">Edit</button>
                <button class="btn btn-sm ${special.disable ? 'btn-danger' : 'btn-secondary'}" onclick="toggleDisableSpecial(${index}, this)">
                    ${special.disable ? 'Disabled' : 'Disable'}
                </button>
            </td>
        `; // new update: Add Disable button with dynamic classes and text
    });
}

// Function to reset the form and modal title for specials
function resetSpecialForm() {
    document.getElementById('specialForm').reset();
    document.getElementById('specialModalLabel').textContent = 'Add New Special';
    document.getElementById('editSpecialIndex').value = '';
}

// Function to save the specials to the server
function saveSpecials(specials) {
    jQuery.ajax({
        url: vmb_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'save_table',
            jsonData: JSON.stringify(specials),
        },
        success: function(response) {
            if (!response.success) {
                alert('Failed to save specials');
            } else {
                Swal.fire(
                    'Saved!',
                    '',
                    'success'
                );
            }
        }
    });
}

// Function to edit a special
function editSpecial(visualIndex) {
    const dataTable = jQuery('#specialsTable').DataTable();
    const row = dataTable.row(visualIndex).node();
    const cells = row.getElementsByTagName('td');

    const id = cells[0].textContent;
    const resort = cells[1].textContent;
    const name = cells[2].textContent;
    const description = cells[3].textContent;
    const expiration = cells[4].textContent;
    const category = cells[5].textContent;

    document.getElementById('specialId').value = id;
    document.getElementById('specialResort').value = resort;
    document.getElementById('specialName').value = name;
    document.getElementById('specialDescription').value = description;
    document.getElementById('specialExpiration').value = expiration;
    document.getElementById('specialDisable').checked = row.getAttribute('data-disable') === 'true';

    // Populate the category dropdown with options
    fetchCategories().done(function(response) {
        if (response.success) {
            const categories = response.data;
            const categorySelect = document.getElementById('specialCategory');
            categorySelect.innerHTML = categories.map(cat => `
                <option value="${cat.name}" ${cat.name === category ? 'selected' : ''}>
                    ${cat.name}
                </option>
            `).join('');
        } else {
            alert('Failed to load categories');
        }
    });

    document.getElementById('editSpecialIndex').value = visualIndex;
    document.getElementById('specialModalLabel').textContent = 'Edit Special';

    var specialModal = new bootstrap.Modal(document.getElementById('specialModal'));
    specialModal.show();
}

function toggleDisableSpecial(index, button) { 
    const dataTable = jQuery('#specialsTable').DataTable();
    const row = dataTable.row(index).node();
    const isDisabled = row.getAttribute('data-disable') === 'true';

    row.setAttribute('data-disable', !isDisabled);
    button.className = `btn btn-sm ${!isDisabled ? 'btn-danger' : 'btn-secondary'}`;
    button.textContent = !isDisabled ? 'Disabled' : 'Disable';
    
    // Update the cached specials in the server
    const specials = [];
    const rows = dataTable.rows().nodes(); // Get all rows as an array of nodes

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        specials.push({
            id: row.cells[0].textContent,
            resort: row.cells[1].textContent,
            name: row.cells[2].textContent,
            description: row.cells[3].textContent,
            expiration: row.cells[4].textContent,
            category: row.cells[5].textContent,
            disable: row.getAttribute('data-disable') === 'true', // Include disable attribute
            modified: row.getAttribute('data-modified') === 'true' // Include modified attribute
        });
    }

    saveSpecials(specials);
}

// Fetch categories from the server
function fetchSpecials() {
    return jQuery.ajax({
        url: vmb_ajax.ajax_url,
        type: 'POST',
        data: { 
            action: 'get_specials_meta',
            option: 'vmb_api_cached_specials'
        }
    });
}
// validate slug
function validateSlug(slug) {
    const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
    return slugRegex.test(slug);
}

// generate slug
function generateSlug(text) {
    return text
        .toLowerCase()                   // Convert to lowercase
        .trim()                          // Trim leading and trailing spaces
        .replace(/[\s\W-]+/g, '-')       // Replace spaces and non-word characters with hyphens
        .replace(/^-+|-+$/g, '');        // Remove leading and trailing hyphens
}

// formate date
function formatDate(isoDateString) {
    let date = new Date(isoDateString);
    let options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}
 
