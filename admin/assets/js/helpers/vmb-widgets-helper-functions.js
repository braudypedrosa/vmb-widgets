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