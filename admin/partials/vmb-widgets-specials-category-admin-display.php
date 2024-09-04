<div class="vmb-widgets-container">
    <div class="container mt-5">
        <h2>Specials Category</h2>     


        <!-- Button to open modal -->
        <!-- <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#entryModal" onclick="resetCategoryForm()">
            Add New Category
        </button> -->
        
        <table class="table table-striped table-bordered mt-3" id="specialsCategory">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Table rows will be inserted here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for adding new entry -->
<!-- <div class="modal fade" id="entryModal" tabindex="-1" aria-labelledby="entryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="entryModalLabel">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                Error message container
                <div id="entryModalError" class="alert alert-danger" style="display: none;"></div>

    
                <form id="entryForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name:</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug:</label>
                        <input type="text" class="form-control" id="slug" required>
                    </div>
                    <input type="hidden" id="editIndex">
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </form>
            </div>
        </div>
    </div>
</div> -->


<script>

// Load categories on page load
document.addEventListener('DOMContentLoaded', function() {
    const categories = JSON.parse(vmb_ajax.cached_special_categories);
    loadCategories(categories);
});

// Auto-generate slug when the name field changes
// document.getElementById('name').addEventListener('input', function() {
//     const name = document.getElementById('name').value;
//     const slug = generateSlug(name);
//     document.getElementById('slug').value = slug;
// });

// Hide error message when the modal is opened
// jQuery('#entryModal').on('show.bs.modal', function () {
//     jQuery('#entryModalError').hide();
// });

// JavaScript to handle form submission and add/update row to the table
// document.getElementById('entryForm').addEventListener('submit', function(event) {
//     event.preventDefault(); // Prevent the form from submitting the traditional way

//     // Get the values from the form fields
//     const name = document.getElementById('name').value;
//     const slug = document.getElementById('slug').value;
//     const editIndex = document.getElementById('editIndex').value;

//     // Validate the slug
//     if (!validateSlug(slug)) {
//         jQuery('#entryModalError').text('Invalid slug. Please enter a valid slug (lowercase letters, numbers, and hyphens only).').show();
//         return; // Stop the form submission
//     }

//     // Save the updated categories
//     const table = document.getElementById('specialsCategory').getElementsByTagName('tbody')[0];
//     const categories = [];
//     for (let i = 0; i < table.rows.length; i++) {
//         const row = table.rows[i];
//         categories.push({
//             name: row.cells[0].textContent,
//             slug: row.cells[1].textContent
//         });
//     }

//     if (editIndex === '') {
//         categories.push({ name: name, slug: slug }); // Add new category to categories array
//     } else {
//         categories[editIndex] = { name: name, slug: slug }; // Update existing category in categories array
//     }

//     saveCategories(categories, name, slug, editIndex); // Pass additional parameters to saveCategories
// });

</script>