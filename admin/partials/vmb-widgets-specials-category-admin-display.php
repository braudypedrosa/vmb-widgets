<div class="vmb-widgets-container">
    <div class="container mt-5">
        <h2>Specials Category</h2>     
        
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

<script>
// Load categories on page load
document.addEventListener('DOMContentLoaded', function() {
    const categories = JSON.parse(vmb_ajax.cached_special_categories);
    loadCategories(categories);
});
</script>