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
 


