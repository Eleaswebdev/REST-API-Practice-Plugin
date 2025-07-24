document.addEventListener('DOMContentLoaded', () => {
    const postContainer = document.getElementById('wp-frontend-posts');
    const categoryFilter = document.getElementById('filter-category');
    const sortSelect = document.getElementById('sort-by');
  
    if (!postContainer) return;
  
    let allPosts = [];
    let categories = [];
  
    function renderPosts(posts) {
      postContainer.innerHTML = '<h3>Posts</h3><ul>' +
        posts.map(p => `<li><strong>${p.title}</strong><br><small>${p.date}</small></li>`).join('') +
        '</ul>';
    }
  
    function filterAndSortPosts() {
      let filtered = [...allPosts];
  
      // Filter
      const selectedCat = categoryFilter.value;
      if (selectedCat) {
        filtered = filtered.filter(post => post.categories.includes(parseInt(selectedCat)));
      }
  
      // Sort
      const sortBy = sortSelect.value;
      if (sortBy === 'title') {
        filtered.sort((a, b) => a.title.localeCompare(b.title));
      } else if (sortBy === 'date') {
        filtered.sort((a, b) => new Date(b.date) - new Date(a.date));
      }
  
      renderPosts(filtered);
    }
  
    // Load data
    Promise.all([
      wp.apiFetch({ path: '/wp/v2/posts?per_page=100&_embed' }),
      wp.apiFetch({ path: '/wp/v2/categories?per_page=100' })
    ])
    .then(([posts, cats]) => {
      allPosts = posts;
      categories = cats;
  
      // Fill category filter
      categoryFilter.innerHTML += cats.map(cat =>
        `<option value="${cat.id}">${cat.name}</option>`
      ).join('');
  
      filterAndSortPosts(); // Initial render
    })
    .catch(error => {
      postContainer.innerHTML = `<p style="color:red;">Error loading data: ${error.message}</p>`;
    });
  
    // Event listeners
    categoryFilter.addEventListener('change', filterAndSortPosts);
    sortSelect.addEventListener('change', filterAndSortPosts);
  });
  